<?php

namespace App\Exports;

use Auth;
use App\Helpers\Common as CommonHelper;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class SchedulerExport implements FromCollection, WithEvents, WithTitle
{
    use Exportable;

    private $collection;

    public function __construct($arrays)
    {
        $output = [];

        foreach ($arrays as $array) {
            $output[] = array_values($array);
        }

        $this->collection = collect($output);
    }

    public function collection()
    {
        // Prevent automatic output
        return collect();
    }

    public function headings(): array
    {
        return [
            trans('ticket_schedular.schedular_listing_fields.company_name'),
            trans('ticket_schedular.schedular_listing_fields.department'),
            trans('ticket_schedular.schedular_listing_fields.user'),
            trans('ticket_schedular.schedular_listing_fields.problem_category'),
            trans('ticket_schedular.schedular_listing_fields.problem_sub_category'),
            trans('ticket_schedular.schedular_listing_fields.priority'),
            trans('ticket_schedular.schedular_listing_fields.tat'),
            trans('ticket_schedular.schedular_listing_fields.device'),
            trans('ticket_schedular.schedular_listing_fields.cron_frequency'),
            trans('ticket_schedular.schedular_listing_fields.subject'),
            trans('ticket_schedular.schedular_listing_fields.content'),
            trans('ticket_schedular.schedular_listing_fields.updated_on'),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                /*
                 * Company Logo
                 */
                $drawing = new Drawing();

                $logoUrl = CommonHelper::CompLogo();

                $logoPath = str_replace(url('/'), public_path(), $logoUrl);

                if (!file_exists($logoPath)) {
                    $logoPath = public_path(ltrim(parse_url($logoUrl, PHP_URL_PATH), '/'));
                }
                if (file_exists($logoPath)) {
                    $drawing->setPath($logoPath);
                    $drawing->setCoordinates('A1');
                    $drawing->setHeight(60);
                    $drawing->setWorksheet($sheet);
                }

                /*
                 * Export Title
                 */
                $lastColumn = Coordinate::stringFromColumnIndex(count($this->headings()));

                $sheet->mergeCells("B1:{$lastColumn}1");
                $sheet->setCellValue('B1', 'Scheduler Listing Export');

                $sheet->getStyle('B1')->getFont()
                    ->setBold(true)
                    ->setSize(16);

                /*
                 * Title Background
                 */
                $sheet->getStyle("A1:{$lastColumn}1")
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFFFCC');

                /*
                * Export Information
                */
                $user = Auth::user();

                $exportBy = $user->display_name ?? $user->username ?? '';
                $location = optional($user->location)->name ?? '';
                $company = optional($user->company)->name ?? '';
                $timestamp = now()->format('d-m-Y h:i A');
                $exportInfo = "Exported From: {$company} | " ."Exported By: {$exportBy} | " ."Exported Location: {$location} | " ."Exported Time: {$timestamp}";

                $sheet->mergeCells("A2:{$lastColumn}2");
                $sheet->setCellValue('A2', $exportInfo);

                $sheet->getStyle("A2:{$lastColumn}2")->getFont()
                    ->setItalic(true)
                    ->setSize(10);

                $sheet->getStyle("A2:{$lastColumn}2")->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                /*
                * Header Row
                */
                $sheet->fromArray([$this->headings()], null, 'A4');

                $sheet->getStyle("A4:{$lastColumn}4")->getFont()
                    ->setBold(true);

                $sheet->getStyle("A4:{$lastColumn}4")
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFCCFF');

                /*
                 * Data
                 */
                $sheet->fromArray(
                    $this->collection->toArray(),
                    null,
                    'A5'
                );

                /*
                 * Auto Size Columns
                 */
                foreach (range('A', $lastColumn) as $column) {
                    $sheet->getColumnDimension($column)
                        ->setAutoSize(true);
                }

                /*
                 * Row Heights
                 */
                $sheet->getRowDimension(1)->setRowHeight(60);
                $sheet->getRowDimension(2)->setRowHeight(20);

                /*
                * Freeze Header
                */
                $sheet->freezePane('A5');

                /*
                * Wrap Text
                */
                $sheet->getStyle("A5:{$lastColumn}" . ($this->collection->count() + 4))->getAlignment()->setWrapText(true);
            },
        ];
    }

    public function title(): string
    {
        return 'Scheduler Listing';
    }
}