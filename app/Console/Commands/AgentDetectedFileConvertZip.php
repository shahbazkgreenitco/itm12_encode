<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use ZipArchive;
use Carbon\Carbon;
use Log;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class AgentDetectedFileConvertZip extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:convert-zip';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Agent detected file a convert zip monthly wise';

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
        $basePath = storage_path("app/ni");
        $currentYear = now()->year;
        $currentMonth = now()->format('m');
        $years = File::directories($basePath);

        foreach ($years as $yearPath) {
            $year = basename($yearPath);
            $monthFolders = File::directories($yearPath);
            foreach ($monthFolders as $monthPath) {
                $month = basename($monthPath);
                if ($year == $currentYear && $month == $currentMonth) {
                    continue;
                }
                $zipPath = "{$basePath}/{$year}/{$month}.zip";
                if (file_exists($zipPath)) {
                    continue;
                }
                $zip = new ZipArchive();
                if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                    $files = new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($monthPath),
                        RecursiveIteratorIterator::LEAVES_ONLY
                    );

                    foreach ($files as $file) {
                        if (!$file->isDir()) {
                            $filePath = $file->getRealPath();
                            $relativePath = substr($filePath, strlen($monthPath) + 1);
                            $zip->addFile($filePath, $relativePath);
                        }
                    }
                    $zip->close();
                    Log::info("Zip created: $zipPath\n");
                    File::deleteDirectory($monthPath);
                } else {
                    Log::info("Failed to create zip: $zipPath\n");
                }
            }
        }
    }
}