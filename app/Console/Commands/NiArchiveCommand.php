<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class NiArchiveCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'archive_files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'archive files in the storage folder';

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
    public function handle() {
        try {
            $roots = [
                storage_path('app/ni'),
                storage_path('app/sw_patch'),
                storage_path('app/ni_blocked_ip_mac'),
                storage_path('app/ni_data_duplicate'),
                storage_path('app/ni_data_incomplete'),
                storage_path('app/ni_mac'),
                storage_path('app/ni_sw_tracking'),
            ];

            foreach ($roots as $basePath) {
                $this->info("Processing root: $basePath");
                if (!file_exists($basePath)) {
                    continue;
                }
                $years = \File::directories($basePath);
                foreach ($years as $yearPath) {
                    $year = basename($yearPath);
                    $months = \File::directories($yearPath);
                    foreach ($months as $monthPath) {
                        $month = basename($monthPath);
                        $days = \File::directories($monthPath);
                        foreach ($days as $dayPath) {
                            $day = basename($dayPath);
                            $folderDate = \Carbon\Carbon::createFromFormat('Y-m-d', "$year-$month-$day");
                            if ($folderDate->isToday()) {
                                continue;
                            }
                            $zipFile = "$monthPath/{$year}-{$month}-{$day}.zip";
                            if (file_exists($zipFile)) {
                                continue;
                            }
                            $zip = new \ZipArchive;
                            if ($zip->open($zipFile, \ZipArchive::CREATE) === TRUE) {
                                $files = new \RecursiveIteratorIterator(
                                    new \RecursiveDirectoryIterator($dayPath),
                                    \RecursiveIteratorIterator::LEAVES_ONLY
                                );
                                foreach ($files as $file) {
                                    if (!$file->isDir()) {
                                        $filePath = $file->getRealPath();
                                        $relativePath = substr($filePath, strlen($dayPath) + 1);
                                        $zip->addFile($filePath, $relativePath);
                                    }
                                }
                                $zip->close();
                                if (file_exists($zipFile) && filesize($zipFile) > 0) {
                                    \File::deleteDirectory($dayPath);
                                    $this->info("Archived: $dayPath");
                                }
                            }
                        }
                    }
                }
                $lastMonth = now()->subMonth();
                $lmYear = $lastMonth->format('Y');
                $lmMonth = $lastMonth->format('m');

                $lmPath = "$basePath/$lmYear/$lmMonth";
                $monthlyZip = "$basePath/$lmYear/{$lmYear}-{$lmMonth}.zip";

                if (file_exists($monthlyZip)) {
                    continue;
                }

                if (file_exists($lmPath)) {
                    $dailyZips = glob("$lmPath/*.zip");
                    if (!empty($dailyZips)) {
                        $zip = new \ZipArchive;
                        if ($zip->open($monthlyZip, \ZipArchive::CREATE) === TRUE) {
                            foreach ($dailyZips as $file) {
                                $zip->addFile($file, basename($file));
                            }
                            $zip->close();
                            if (file_exists($monthlyZip) && filesize($monthlyZip) > 0) {
                                foreach ($dailyZips as $file) {
                                    unlink($file);
                                }
                                $this->info("Monthly archive created: $monthlyZip");
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
        
    }
}
