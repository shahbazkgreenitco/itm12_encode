<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportBladeDocumentation extends Command
{
    protected $signature = 'blade:docs';

    protected $description = 'Export blade documentation to excel';

    public function handle()
    {
        $viewPath = resource_path('views');

        $spreadsheet = new Spreadsheet();

        /*
        |--------------------------------------------------------------------------
        | Sheet 1 - Documentation
        |--------------------------------------------------------------------------
        */
        $docSheet = $spreadsheet->getActiveSheet();
        $docSheet->setTitle('Documentation');

        $docSheet->fromArray([
            [
                'Module Name',
                'File Name',
                'Tag',
                'Version',
                'Page ID',
                'Author',
                'Created On',
                'Reviewed By',
                'Format',
                'Change Log'
            ]
        ]);

        /*
        |--------------------------------------------------------------------------
        | Sheet 2 - Version History
        |--------------------------------------------------------------------------
        */
        $versionSheet = $spreadsheet->createSheet();

        $versionSheet->setTitle('Version History');

        $versionSheet->fromArray([
            [
                'File Name',
                'Version',
                'Description',
                'Writer',
                'Reviewer'
            ]
        ]);

        /*
        |--------------------------------------------------------------------------
        | Sheet 3 - Missing Docs
        |--------------------------------------------------------------------------
        */
        $missingSheet = $spreadsheet->createSheet();

        $missingSheet->setTitle('Missing Docs');

        $missingSheet->fromArray([
            ['File Path']
        ]);

        $docRow = 2;
        $versionRow = 2;
        $missingRow = 2;

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($viewPath)
        );

        foreach ($iterator as $file) {

            if (!$file->isFile()) {
                continue;
            }

            if (!str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }

            $content = file_get_contents($file->getPathname());

            $data = $this->parseBladeFile($content);

            if (!$data) {

                $missingSheet->setCellValue(
                    'A'.$missingRow,
                    str_replace(base_path().'/', '', $file->getPathname())
                );

                $missingRow++;
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Documentation Sheet
            |--------------------------------------------------------------------------
            */
            $docSheet->fromArray([
                [
                    $data['module'],
                    $data['file_name'],
                    $data['tag'],
                    $data['version'],
                    $data['page_id'],
                    $data['author'],
                    $data['created_on'],
                    $data['reviewed_by'],
                    $data['format'],
                    $data['change_log']
                ]
            ], null, 'A'.$docRow);

            $docRow++;

            /*
            |--------------------------------------------------------------------------
            | Version History
            |--------------------------------------------------------------------------
            */
            if ($data['format'] == 'JSON_META') {

                foreach ($data['versions'] as $version) {

                    $versionSheet->fromArray([
                        [
                            $data['file_name'],
                            $version['version'] ?? '',
                            $version['description'] ?? '',
                            $version['writer'] ?? '',
                            $version['reviewer'] ?? ''
                        ]
                    ], null, 'A'.$versionRow);

                    $versionRow++;
                }
            } else {

                preg_match_all(
                    '/\[(.*?)\]\s*-\s*(.*)/',
                    $data['change_log'],
                    $matches,
                    PREG_SET_ORDER
                );

                foreach ($matches as $match) {

                    $versionSheet->fromArray([
                        [
                            $data['file_name'],
                            trim($match[1]),
                            trim($match[2]),
                            $data['author'],
                            $data['reviewed_by']
                        ]
                    ], null, 'A'.$versionRow);

                    $versionRow++;
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Auto Width
        |--------------------------------------------------------------------------
        */
        foreach ($spreadsheet->getAllSheets() as $sheet) {

            foreach (range('A', $sheet->getHighestColumn()) as $column) {

                $sheet->getColumnDimension($column)
                    ->setAutoSize(true);
            }
        }

        $fileName = storage_path(
            'app/blade_documentation_'.date('Ymd_His').'.xlsx'
        );

        $writer = new Xlsx($spreadsheet);
        $writer->save($fileName);

        $this->info('Excel Generated');
        $this->info($fileName);

        return Command::SUCCESS;
    }

    private function parseBladeFile($content)
    {
        $result = $this->parseJsonMeta($content);

        if ($result) {
            return $result;
        }

        $result = $this->parseHeaderMeta($content);

        if ($result) {
            return $result;
        }

        return null;
    }

    private function parseJsonMeta($content)
    {
        if (
            !preg_match(
                '/@page-meta\s*(\{.*?\})\s*--}}/s',
                $content,
                $match
            )
        ) {
            return null;
        }

        $json = json_decode($match[1], true);

        if (!$json) {
            return null;
        }

        $versions = $json['versions'] ?? [];

        $latestVersion = '';
        $author = '';
        $createdOn = '';
        $reviewedBy = '';

        if (!empty($versions)) {

            $latest = end($versions);

            $latestVersion = $latest['version'] ?? '';
            $author = $latest['writer'] ?? '';
            $createdOn = $latest['from'] ?? '';
            $reviewedBy = $latest['reviewer'] ?? '';
        }

        $logs = [];

        foreach ($versions as $version) {

            $logs[] =
                '['.($version['version'] ?? '').'] - '.
                ($version['description'] ?? '').
                ' ('.($version['writer'] ?? '').')';
        }

        return [
            'module'      => '',
            'file_name'   => $json['file'] ?? '',
            'tag'         => '',
            'version'     => $latestVersion,
            'page_id'     => $json['page_no'] ?? '',
            'author'      => $author,
            'created_on'  => $createdOn,
            'reviewed_by' => $reviewedBy,
            'change_log'  => implode("\n", $logs),
            'format'      => 'JSON_META',
            'versions'    => $versions
        ];
    }

    private function parseHeaderMeta($content)
    {
        if (!preg_match('/File:/i', $content)) {
            return null;
        }

        preg_match('/File:\s*(.+)/i', $content, $fileMatch);
        preg_match('/Module:\s*(.+)/i', $content, $moduleMatch);
        preg_match('/([A-Z]{2,}\/\d+\/\d+)/', $content, $tagMatch);
        preg_match('/Version:\s*(.+)/i', $content, $versionMatch);
        preg_match('/Page ID:\s*(.+)/i', $content, $pageMatch);
        preg_match('/Author:\s*(.+)/i', $content, $authorMatch);
        preg_match('/Created On:\s*(.+)/i', $content, $createdMatch);
        preg_match('/Reviewed By:\s*(.+)/i', $content, $reviewedMatch);

        $changeLog = '';

        if (
            preg_match(
                '/Change Log:(.*?)(?:\*\/|--}})/s',
                $content,
                $changeMatch
            )
        ) {
            $changeLog = trim($changeMatch[1]);
        }

        return [
            'module'      => trim($moduleMatch[1] ?? ''),
            'file_name'   => trim($fileMatch[1] ?? ''),
            'tag'         => trim($tagMatch[1] ?? ''),
            'version'     => trim($versionMatch[1] ?? ''),
            'page_id'     => trim($pageMatch[1] ?? ''),
            'author'      => trim($authorMatch[1] ?? ''),
            'created_on'  => trim($createdMatch[1] ?? ''),
            'reviewed_by' => trim($reviewedMatch[1] ?? ''),
            'change_log'  => $changeLog,
            'format'      => 'HEADER',
            'versions'    => []
        ];
    }
}