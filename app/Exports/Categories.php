<?php

namespace App\Exports;

use App\Helpers\Common as CommonHelper;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class Categories implements FromCollection, WithEvents, WithTitle
{
    use Exportable;
    private $collection;

    public function __construct($arrays)
    {
        $output = [];
        foreach ($arrays as $array) {
            if (isset($array['require_acceptance']) && $array['require_acceptance'] == '1') {
                $array['require_acceptance'] = '✔';
            }
            if (isset($array['checkin_email']) && $array['checkin_email'] == '1') {
                $array['checkin_email'] = '✔';
            }
            if (isset($array['eula']) && $array['eula'] == '1') {
                $array['eula'] = '✔';
            }
            if (isset($array['checkout_email_accept']) && $array['checkout_email_accept'] == '1') {
                $array['checkout_email_accept'] = '✔';
            }
            $output[] = array_values($array);
        }
        $this->collection = collect($output);
    }
    public function collection()
    {
        return collect();
    }

    public function headings(): array
    {
        return [
                trans('config.categories_fields.id'),
                trans('config.categories_fields.header_name'),
                trans('config.categories_fields.header_asset_department'),
                trans('config.categories_fields.header_checkin_email'),
                trans('config.categories_fields.header_category_type'),
                trans('config.categories_fields.header_require_acceptance'),
                trans('config.categories_fields.header_checkout_email_accept'),
                trans('config.categories_fields.header_account_type'),
                trans('config.categories_fields.header_updated_at'),
                trans('config.categories_fields.header_threshold'),
                trans('config.categories_fields.header_eula'),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                // Company Logo
                $drawing = new Drawing();
                $logoUrl = CommonHelper::CompLogo();
                $logoPath = public_path(
                    ltrim(parse_url($logoUrl, PHP_URL_PATH), '/')
                );

                if (file_exists($logoPath)) {
                    $drawing->setPath($logoPath);
                    $drawing->setCoordinates('A1');
                    $drawing->setHeight(60);
                    $drawing->setWorksheet($sheet);
                }

                // Title
                $lastColumn = Coordinate::stringFromColumnIndex(count($this->headings()));

                // Leave A & B for the logo, use remaining columns for title
                $sheet->mergeCells("C1:{$lastColumn}1");
                $sheet->setCellValue('C1', trans('config.categories_fields.export_title'));

                $sheet->getStyle('C1')->getFont()
                    ->setBold(true)
                    ->setSize(16);

                $sheet->getStyle("C1:{$lastColumn}1")
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                // Title Background
                $sheet->getStyle("A1:{$lastColumn}1")
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFFFCC');

                // Header Row
                $sheet->fromArray([$this->headings()], null, 'A3');

                $sheet->getStyle("A3:{$lastColumn}3")
                    ->getFont()
                    ->setBold(true);

                $sheet->getStyle("A3:{$lastColumn}3")
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFCCFF');

                // Data
                $sheet->fromArray($this->collection->toArray(), null, 'A4');

                // Row height
                $sheet->getRowDimension(1)->setRowHeight(60);
            },
        ];
    }

    public function title(): string
    {
        return trans('config.categories_fields.categories');
    }
}