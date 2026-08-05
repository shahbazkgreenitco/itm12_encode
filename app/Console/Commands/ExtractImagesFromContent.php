<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class ExtractImagesFromContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'extract:images 
                            {--id= : Single ticket ID} 
                            {--range= : Range of IDs like 100-150} 
                            {--ids= : Comma-separated IDs like 1,5,20}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract base64 images, save to folder, and update content with cid format';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $query = DB::table('tkt_tickets')->select('id', 'content');

        if ($this->option('id')) {
            $query->where('id', $this->option('id'));
        }

        if ($this->option('range')) {
            [$start, $end] = explode('-', $this->option('range'));
            $query->whereBetween('id', [(int) $start, (int) $end]);
        }

        if ($this->option('ids')) {
            $ids = explode(',', $this->option('ids'));
            $query->whereIn('id', $ids);
        }

        $tickets = $query->get();
        $this->info("Processing " . count($tickets) . " tickets...");
        $progress = $this->output->createProgressBar(count($tickets));
        $progress->start();

        foreach ($tickets as $ticket) {
            $content = $ticket->content;
            preg_match_all('/<img[^>]+src="data:image\/([^;]+);base64,([^"]+)"[^>]*>/', $content, $matches, PREG_SET_ORDER);

            foreach ($matches as $index => $match) {
                $imageExt = $match[1];
                $base64Image = $match[2];

                preg_match('/data-filename="([^"]+)"/', $match[0], $filenameMatch);
                $originalFileName = $filenameMatch[1] ?? 'image_' . $ticket->id . '_' . $index . '.' . $imageExt;

                $imageData = base64_decode($base64Image);
                $cid = Str::random(40);
                $tmpId = Str::random(25);
                $todayPath = Carbon::now()->format('Y/m/d');

                $fileName = $todayPath . '/' . $cid . '.' . $imageExt;
                $thumbName = $todayPath . '/' . $cid . '_thumb.' . $imageExt;

                Storage::disk('tkt_attachment')->put($fileName, $imageData);
                Storage::disk('tkt_attachment')->put($thumbName, $imageData);

                DB::table('tkt_attachments')->insert([
                    'ticket_id' => $ticket->id,
                    'following_id' => auth()->id() ?? 1,
                    'file_name' => $fileName,
                    'original_file_name' => $originalFileName,
                    'extension' => $imageExt,
                    'cid' => $cid,
                    'tmp_id' => $tmpId,
                    'thumbnail' => $thumbName,
                    'uploader_id' => auth()->id() ?? 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $newImgTag = '<img src="cid:' . $cid . '" data-filename="' . $originalFileName . '">';
                $content = str_replace($match[0], $newImgTag, $content);
            }

            DB::table('tkt_tickets')->where('id', $ticket->id)->update(['content' => $content]);
            $progress->advance();
        }

        $progress->finish();
        $this->info("\nDone!");
    }

}
