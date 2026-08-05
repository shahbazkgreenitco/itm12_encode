<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\OllamaService;
use Paperdoc\Facades\Paperdoc;
use Paperdoc\Document\Style\TextStyle;
use Paperdoc\Document\Table;
use Paperdoc\Document\TableRow;
use Paperdoc\Document\TableCell;
use Paperdoc\Document\Paragraph;
use Paperdoc\Document\TextRun;

class NLQueryController extends Controller
{
    protected OllamaService $ollama;

    public function __construct(OllamaService $ollama)
    {
        $this->ollama = $ollama;
    }

    public function index()
    {
        return view('nl-query.index');
    }

    public function query(Request $request)
    {
        $request->validate([
            'query' => 'required|string|max:500'
        ]);

        $sql = null;

        try {
            $sql     = $this->ollama->generateSQL($request->input('query'));
            $results = $this->ollama->runQuery($sql);

            $results = array_map(fn($row) => (array) $row, $results);
            $columns = array_keys($results[0]);

            return response()->json([
                'success' => true,
                'query'   => $sql,
                'columns' => $columns,
                'results' => $results,
                'count'   => count($results)
            ]);

        } catch (\Exception $e) {
            $message = $e->getMessage();

            if ($message === 'NO_RESULTS') {
                return response()->json([
                    'success' => true,
                    'query'   => $sql ?? null,
                    'columns' => [],
                    'results' => [],
                    'count'   => 0,
                    'empty'   => true
                ]);
            }

            return response()->json([
                'success' => false,
                'error'   => $message,
                'sql'     => $sql
            ], 422);
        }
    }

    // ── Shared table builder ───────────────────────────────────────────────

    private function buildTable(array $columns, array $results): Table
    {
        $table = new Table();

        // Header row
        $headerRow = new TableRow();
        foreach ($columns as $col) {
            $label = str_replace('_', ' ', ucwords($col, '_'));
            $para  = Paragraph::make()->addRun(
                TextRun::make($label, TextStyle::make()->setBold()->setColor('#FFFFFF'))
            );
            $headerRow->addCell(
                TableCell::make()->addElement($para)
            );
        }
        $table->addRow($headerRow);

        // Data rows
        foreach ($results as $row) {
            $dataRow = new TableRow();
            foreach ($columns as $col) {
                $val  = isset($row[$col]) && $row[$col] !== null ? (string) $row[$col] : '—';
                $para = Paragraph::make()->addRun(
                    TextRun::make($val, TextStyle::make()->setFontSize(9))
                );
                $dataRow->addCell(
                    TableCell::make()->addElement($para)
                );
            }
            $table->addRow($dataRow);
        }

        return $table;
    }

    // ── Shared report header builder ───────────────────────────────────────

    private function buildReportHeader($section, string $userQuery, string $sql, int $count): void
    {
        $section->addParagraph('AI Query Report',         TextStyle::make()->setBold()->setFontSize(16));
        $section->addParagraph('Query: ' . $userQuery,    TextStyle::make()->setBold()->setFontSize(10));
        $section->addParagraph('SQL: ' . $sql,            TextStyle::make()->setItalic()->setFontSize(9)->setColor('#666666'));
        $section->addParagraph('Records: ' . $count,      TextStyle::make()->setFontSize(9));
        $section->addParagraph('Generated: ' . now()->format('Y-m-d H:i:s'), TextStyle::make()->setFontSize(9)->setColor('#999999'));
    }

    // ── Shared temp path helper ────────────────────────────────────────────

    private function makeTempPath(string $filename): string
    {
        $dir = storage_path('app/temp');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir . DIRECTORY_SEPARATOR . $filename;
    }

    // ── Export PDF ─────────────────────────────────────────────────────────

    public function exportPdf(Request $request)
    {
        $request->validate([
            'query'   => 'required|string|max:500',
            'sql'     => 'required|string',
            'columns' => 'required|array',
            'results' => 'required|array',
        ]);

        $userQuery = $request->input('query');
        $sql       = $request->input('sql');
        $columns   = $request->input('columns');
        $results   = $request->input('results');

        $doc     = Paperdoc::create('pdf', 'AI Query Report');
        $section = $doc->openSection();

        $this->buildReportHeader($section, $userQuery, $sql, count($results));
        $section->addElement($this->buildTable($columns, $results));

        $filename = 'ai-query-' . now()->format('Y-m-d-His') . '.pdf';
        $tmpPath  = $this->makeTempPath($filename);

        Paperdoc::save($doc, $tmpPath);

        return response()->download($tmpPath, $filename, [
            'Content-Type' => 'application/pdf',
        ])->deleteFileAfterSend(true);
    }

    // ── Export Excel ───────────────────────────────────────────────────────

    public function exportExcel(Request $request)
    {
        $request->validate([
            'query'   => 'required|string|max:500',
            'sql'     => 'required|string',
            'columns' => 'required|array',
            'results' => 'required|array',
        ]);

        $userQuery = $request->input('query');
        $sql       = $request->input('sql');
        $columns   = $request->input('columns');
        $results   = $request->input('results');

        $doc     = Paperdoc::create('xlsx', 'AI Query Report');
        $section = $doc->openSection();

        $this->buildReportHeader($section, $userQuery, $sql, count($results));
        $section->addElement($this->buildTable($columns, $results));

        $filename = 'ai-query-' . now()->format('Y-m-d-His') . '.xlsx';
        $tmpPath  = $this->makeTempPath($filename);

        Paperdoc::save($doc, $tmpPath);

        return response()->download($tmpPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    // ── Schema preview ─────────────────────────────────────────────────────

    public function schema()
    {
        return response()->json([
            'tables' => $this->ollama->getSchemaPreview()
        ]);
    }

    public function debug()
    {
        $database   = env('DB_DATABASE');
        $tables     = DB::select("SHOW TABLES");
        $tableKey   = "Tables_in_{$database}";
        $firstTable = $tables[0]->$tableKey;
        $stmt       = DB::select("SHOW CREATE TABLE `{$firstTable}`");
        $row        = (array) $stmt[0];

        return response()->json([
            'keys'  => array_keys($row),
            'table' => $firstTable,
            'raw'   => $row
        ]);
    }
}