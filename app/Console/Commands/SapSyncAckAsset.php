<?php

namespace App\Console\Commands;

use App\Models\SAP\GrnBasic;
use App\Models\SAP\GrnItem;
use App\Helpers\Common as CommonHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;

class SapSyncAckAsset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sap:sync-grn-ack {--send-ack : Send acknowledgment to SAP after saving}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync GRN data from SAP API and optionally send acknowledgment';

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
        $this->info('GRN Sync started at: ' . now());
        $nowTime = Carbon::now();
        try {
            // Step 1: Fetch GRN data from SAP
            $grnData = $this->fetchGrnFromSap();

            if (empty($grnData)) {
                $this->warn('No GRN data received from SAP API');
                Log::info('No GRN data received from SAP API: '. $nowTime);
                return 0;
            }

            // Step 2: Save to database
            $savedRecords = $this->saveGrnToDatabase($grnData);

            // Step 3: Send acknowledgment back to SAP if flag is set
            if ($this->option('send-ack') && !empty($savedRecords)) {
                $this->sendAcknowledgmentToSap($savedRecords);
            }

            $this->info('GRN Sync completed successfully');
            return 0;

        } catch (\Exception $e) {
            $this->error('CRITICAL ERROR: ' . $e->getMessage());
            Log::error('GRN Sync Exception', [
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
            return 1;
        }
    }

    private function fetchGrnFromSap()
    {
        $url = config('services.sap.url')."GRN_HEADERSet";
        if (empty($url)) {
            throw new \Exception('SAP API URL is not configured');
        }

        $now = Carbon::now(config('app.timezone'));
        $toDate = $now->format('d.m.Y');
        $fromDate = $now->subDays(30)->format('d.m.Y');

        $params = [
            'expand' => 'Items',
            'format' => 'json',
            'filter' => "(fdate eq '".$fromDate."' and tdate eq '".$toDate."')",
        ];

        $response = CommonHelper::getSapRequest($url, $params);

        return $response['d']['results'] ?? [];
    }

    private function saveGrnToDatabase($results) {
        $savedRecords = [];
        $totalRecords = count($results);
        $bar = $this->output->createProgressBar($totalRecords);

        foreach ($results as $row) {
            try {
                $grnBasic = GrnBasic::updateOrCreate(
                    ['grnNo' => $row['grnNo']],
                    [
                        'poNo' => $row['poNo'] ?? null,
                        'refGrnNo' => $row['refGrnNo'] ?? null,
                        'invoiceNo' => $row['invoiceNo'] ?? null,
                        'vendorCode' => $row['vendorCode'] ?? null,
                        'vendorName' => $row['vendorName'] ?? null,
                        'grnPostingDate' => $row['grnPostingDate'] ? CommonHelper::getDateAs($row['grnPostingDate'], "Y-m-d", "d/m/Y") : null,
                        'invoiceDate' => $row['invoiceDate'] ? CommonHelper::getDateAs($row['invoiceDate'], "Y-m-d", "d/m/Y") : null,
                        'challanDate' => $row['challanDate'] ? CommonHelper::getDateAs($row['challanDate'], "Y-m-d", "d/m/Y") : null,
                        'challanNo' => $row['challanNo'] ?? null,
                        'companyCode' => $row['companyCode'] ?? null,
                        'poDate' => $row['poDate'] ? CommonHelper::getDateAs($row['poDate'], "Y-m-d", "d/m/Y") : null,
                        'status' => $row['status'] ?? null,
                        'is_ack_sent' => false,
                    ]
                );

                foreach ($row['Items']['results'] ?? [] as $item) {
                    GrnItem::updateOrCreate(
                        [
                            'basic_id' => $grnBasic->id,
                            'serialNo' => $item['serialNo'] ?? null
                        ],
                        [
                            'grnItem' => $item['grnItem'] ?? null,
                            'materialCode' => $item['materialCode'] ?? null,
                            'description' => $item['description'] ?? null,
                            'grQty' => $item['grQty'] ?? null,
                            'acceptedQty' => $item['acceptedQty'] ?? null,
                            'uom' => $item['uom'] ?? null,
                            'challanQty' => $item['challanQty'] ?? null,
                            'plantCode' => $item['plantCode'] ?? null,
                            'plantName' => $item['plantName'] ?? null,
                            'assetNo' => $item['assetNo'] ?? null,
                        ]
                    );
                }

                $savedRecords[] = $grnBasic;
                $bar->advance();

            } catch (\Exception $e) {
                $this->error("\nError processing GRN: " . ($row['grnNo'] ?? 'Unknown'));
                Log::error('Error processing GRN record', [
                    'grnNo' => $row['grnNo'] ?? 'Unknown',
                    'error' => $e->getMessage()
                ]);
            }
        }
        $bar->finish();
        $this->info("\n");

        return $savedRecords;
    }

    private function sendAcknowledgmentToSap($savedRecords)
    {
        $this->info("\nSending acknowledgment to SAP...");

        $successCount = 0;
        $failureCount = 0;

        foreach ($savedRecords as $grnBasic) {
            try {
                // Skip if already sent
                if ($grnBasic->is_ack_sent) {
                    $this->line("GRN {$grnBasic->grnNo} - Already acknowledged, skipping");
                    continue;
                }

                $grnItems = GrnItem::where('basic_id', $grnBasic->id)->count();

                // $payloadItems = $grnItems->map(function ($item, $index) {
                //     return [
                //         'grnLineItem' => (string)($item->grnItem ?? $index + 1),
                //         'grnStatus' => "1A",
                //         'grnDate' => null,
                //         'grnTime' => null,
                //     ];
                // })->values()->toArray();

                $payload = [
                    'grnNo' => (string)$grnBasic->grnNo,
                    'refGrnNo' => (string)($grnBasic->refGrnNo ?? $grnBasic->grnNo),
                    'grnStatus' => ($grnBasic->status ?? '') . 'A',
                    'noGrnItem' => (string)$grnItems,
                    'grnProcessDateTime' => date('d.m.Y H:i:s'),
                ];
                // dd($payload);

                // Log the payload
                Log::info('Sending SAP acknowledgment payload', [
                    'grnNo' => $grnBasic->grnNo,
                    'payload' => $payload
                ]);

                $url = config('services.sap.post_url');
                if (empty($url)) {
                    throw new \Exception('SAP POST URL is not configured');
                }

                $headers = [
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'X-Requested-With: X',
                ];
                // dd($url, $payload, $headers);

                $response = CommonHelper::postSapRequest($url, $payload, $headers);

                Log::info('SAP acknowledgment response', [
                    'grnNo' => $grnBasic->grnNo,
                    'success' => $response['success'] ?? false,
                    'status' => $response['status'] ?? null,
                    'response' => $response['response'] ?? null
                ]);

                if ($response['success'] ?? false) {
                    $grnBasic->update(['is_ack_sent' => true]);
                    $successCount++;
                    $this->info("GRN {$grnBasic->grnNo} - Acknowledgment sent successfully");
                } else {
                    $failureCount++;

                    $errorMsg = 'Unknown error';
                    if (isset($response['response']['error']['message']['value'])) {
                        $errorMsg = $response['response']['error']['message']['value'];
                    } elseif (isset($response['error'])) {
                        $errorMsg = is_array($response['error']) ? json_encode($response['error']) : $response['error'];
                    } elseif (isset($response['response']) && is_string($response['response'])) {
                        $errorMsg = $response['response'];
                    }

                    $this->error("GRN {$grnBasic->grnNo} - Failed to send acknowledgment: " . $errorMsg);
                }

            } catch (\Exception $e) {
                $failureCount++;
                $this->error("GRN {$grnBasic->grnNo} - Error: " . $e->getMessage());
                Log::error('Failed to send SAP acknowledgment', [
                    'grnNo' => $grnBasic->grnNo,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        $this->info("\nAcknowledgment Summary:");
        $this->info("Successful: {$successCount}");
        $this->info("Failed: {$failureCount}");
    }
}