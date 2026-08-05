<?php

namespace App\Imports\Consumables;

use Illuminate\Http\Request;
use App\Models\Consumable;
use App\Models\User;
use App\Models\Place;
use App\Models\Actionlog;
use App\Models\BulkActions;
use App\Models\Category;
use App\Models\Device;
use Carbon\Carbon;
use App\Helpers\Common as CommonHelper;
use Validator;
use Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;
use Log;
use Mail;
use App\Models\Settings;
use App\Models\ThresholdSettings;
use App\Models\Threshold;
use App\Models\ThresholdAlertSettings;
use App\Mail\Consumables\ConsumableThreshouldNotification;
use App\Mail\ThreshouldNotification;
use App\Imports\Consumables\ConsumableImportStore;

class ConsumableBulkCheckout implements ToCollection, SkipsEmptyRows, WithHeadingRow, SkipsOnError
{
    use Importable, SkipsErrors;
    public $data, $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        $return = ["msg"=>"Unable to checkout the given consumable list", "status"=>"danger"];
        try {  
            $success = 0;
            $fail = 0;
            $fail_msgs = [];
            $data = [];
            $exception_break = false;
            $tot_insert_records = 0;
            $invalied_key = false;
            $companyIds = CommonHelper::getAccessibleCompanyIds();
            
            $required_keys = ['batch_code', 'assigned_for', 'assigned_to', 'note'];
         
            foreach($collection as $key => $row) {
                if(trim($row['batch_code']) == "" || $row['batch_code'] == NULL) {
                    $fail++;
                    $fail_msgs[] = trans('content.consumables_fields.The_consumable_ID_not_found');
                    $data[] = [
                        $row['batch_code'],
                        $row['assigned_for'],
                        $row['assigned_to'],
                        $row['note'],
                        'fail',
                        "The consumable Batch Code is not found"
                    ];
                    continue;
                }

                $temp_arr = $row->toArray();
                if (config("app.client") == "etherealmachines") {
                    $temp_arr['id'] = (string) str_ireplace(['cns','cn'],'', $row['batch_code']);
                } else {
                    $temp_arr['id'] = (string) str_ireplace('cns','', $row['batch_code']);
                }

                $validateTag = Validator::make($temp_arr, [
                    'id' => 'required|string|exists:consumables,id',
                    'assigned_for' => [
                        'required',
                        'string',
                        Rule::in(['user', 'place', 'device', 'User', 'Place', 'Device', 'USER', 'PLACE', 'DEVICE'])
                    ],
                    'assigned_to' => [
                        'required',
                        'string',
                        'clean_text_only'
                    ],
                    'note' => [
                        Rule::requiredIf(fn () => config('app.client') == 'knightfrank'),
                        'nullable',
                        'string',
                        'clean_text_only',
                        'max:255',
                    ], 
                ]);
                if($validateTag->fails()) {
                    $fail++;
                    $fail_msgs[] = "The record for batch no. '" . $row['batch_code'] . "' has not found.";
                    $data[] = [
                        $row['batch_code'],
                        $row['assigned_for'],
                        $row['assigned_to'],
                        $row['note'],
                        'fail',
                        "The record for batch no. '" . $row['batch_code'] . "' has not found."
                    ];
                    continue;
                }

                $consumable = Consumable::where('id', $temp_arr['id'])->withCount(['users', 'device','places'])->with('category')->first();

                if (!in_array($consumable->company_id, $companyIds)) {
                    $fail++;
                    $fail_msgs[] = trans('content.consumables_fields.permission_denied_for_consumable_checkout',['batch_code' =>  $row['batch_code']]);
                    $data[] = [
                        $row['batch_code'],
                        $row['assigned_for'],
                        $row['assigned_to'],
                        $row['note'],
                        'fail',
                        trans('content.consumables_fields.permission_denied_for_consumable_checkout',['batch_code' =>  $row['batch_code']])
                    ];
                    Log::info("Permission denied: You don't have access to checkout consumable (Batch No: " . $row['batch_code'] . ")");
                    continue;
                }

                $totalAssigned = ($consumable->users_count ?? 0) + ($consumable->places_count ?? 0) + ($consumable->device_count ?? 0) + ($consumable->scrap_qty ?? 0);
                if ($consumable->qty <= $totalAssigned) {
                    $fail++;
                    $fail_msgs[] = 'Consumable does not have enough quantity to checkout for batch: ' . ($row['batch_code'] ?? '');
                    $data[] = [
                        $row['batch_code'],
                        $row['assigned_for'],
                        $row['assigned_to'],
                        $row['note'],
                        'fail',
                        "Consumable does not have enough quantity to checkout"
                    ];
                    continue;
                }
                
                $user = Auth::user();

                $checkoutFor = strtolower(trim($row['assigned_for']));
                $assignedId = $assignedForType = null;

                if ($checkoutFor == 'user') {
                    $today = Carbon::today()->toDateString();
                    $assignedUser = User::where('username', $row['assigned_to'])->where('activated', 1)->where('company_id',$consumable->company_id)->first();
                    if (empty($assignedUser)) {
                        $fail++;
                        $fail_msgs[] = "Assigned user not found for that company " . ($row['batch_code'] ?? '');
                        $data[] = [
                            $row['batch_code'],
                            $row['assigned_for'],
                            $row['assigned_to'],
                            $row['note'],
                        'fail',
                        "Chosen user is not found for that company for checkout"
                        ];
                        continue;
                    } elseif( $assignedUser->last_working_date != null && $assignedUser->last_working_date <= $today ) {
                        $fail++;
                        $fail_msgs[] = "Assigned user found but last working date has toady expired or expired " . ($row['batch_code'] ?? '');
                        $data[] = [
                            $row['batch_code'],
                            $row['assigned_for'],
                            $row['assigned_to'],
                            $row['note'],
                        'fail',
                        "Chosen user is not available for checkout (last working date toady expired or expired)"
                        ];
                        continue;
                    }
                    $assignedId = $assignedUser->id;
                    $assignedForType = 1;
                } elseif ($checkoutFor == 'place') {
                    $place = Place::where('place', $row['assigned_to'])->where('company_id',$consumable->company_id)->first();
                    if (empty($place)) {
                        $fail++;
                        $fail_msgs[] = "Assigned place not found for that company ". ($row['batch_code'] ?? '');
                        $data[] = [
                            $row['batch_code'],
                            $row['assigned_for'],
                            $row['assigned_to'],
                            $row['note'],
                        'fail',
                        "Chosen Place is not found for that company for checkout"
                        ];
                        continue;
                    }
                    $assignedId = $place->id;
                    $assignedForType = 2;
                } elseif ($checkoutFor == 'device') {
                    $device = Device::where('asset_tag', $row['assigned_to'])->where('company_id',$consumable->company_id)->first();
                    if (empty($device)) {
                        $fail++;
                        $fail_msgs[] = "Assigned device not found for that company ". ($row['batch_code'] ?? '');
                        $data[] = [
                            $row['batch_code'],
                            $row['assigned_for'],
                            $row['assigned_to'],
                            $row['note'],
                        'fail',
                        "Chosen Device is not found for that company for checkout"
                        ];
                        continue;
                    }
                    $assignedId = $device->id;
                    $assignedForType = 3;
                }

                if ($consumable && $assignedId) {
                    $log = new Actionlog();
                    $log->asset_type = "consumable";
                    $log->checkedout_to = $assignedId;
                    $log->location_id = $user->location_id ?? null;
                    $log->assigned_to_type = $assignedForType;
                    $log->assigned_for = $assignedForType;
                    $log->consumable_id = $consumable->id;
                    $log->note = $row['note'] ?? null;
                    $log->user_id = $user->id;
                    $log->action_type = "checkout";
                    $log->save();
                    $consumable->users()->attach($user->id, [
                        'consumable_id' => $consumable->id,
                        'user_id' => $user->id,
                        'assigned_to' => $assignedId,
                        'assigned_for' => $assignedForType,
                        'asset_logs_id' => $log->id,
                    ]);
                    $data[] = [
                        $row['batch_code'],
                        $row['assigned_for'],
                        $row['assigned_to'],
                        $row['note'],
                        'success',
                        "Checked out successfully"
                    ];
                    $success++;
                }
            }
            $return['fail'] = $fail;
            $return['success'] = $success;
            $return['fail_msgs'] = $fail_msgs;
            return $this->data = $return;
        } catch(\Exception $e) {
            Log::error("consumable bulk Checkout error: " . $e->getMessage());
            return $this->data = $return;
        }

        $file_name = $this->request->file('import_file');
        $given_file_original_name = preg_replace('@[^0-9a-z\\.]+@i', '', $file_name->getClientOriginalName());

        $defaultKkeys = array('Batch Code', 'Assigned For','Assigned To', 'Note');
        $succ_fail = array('Success/Fail', 'Message');
        $keys = array_merge($defaultKkeys, $succ_fail);
        $name = 'ConsumableBulkCheckoutFormat_'.date('dmYHis').'.xlsx';
        $doc_path = Excel::store(new ConsumableImportStore($data, $keys), $name, 'bulk_documents');
        $log = new BulkActions();

        $log->action_type = 1;
        $log->module_id = 4;
        $log->created_at = date("Y-m-d H:i:s");
        $log->doc_path = $name;
        $log->doc_name = $given_file_original_name;
        $log->tot_success = $success;
        $log->tot_failure = $fail;
        $log->user_id = Auth::user()->id;
        $log->save();

        $return['fail'] = $fail;
        $return['success'] = $success;
        $return['fail_msgs'] = $fail_msgs;
        return $this->data = $return;
    }

    public function model(array $row)
    {
        $invalied_key = false;
        $required_keys = ['batch_code', 'assigned_for', 'assigned_to', 'note'];

    }

    public function notifythresholdAlert($cons_id, $category_id)
    {
        $alertnotify = [];
        $alertmail = [];
        if(Settings::first()->alerts_enabled == 1){
            $alertnotify = CommonHelper::getGlobalAlertEmail();
        }
        //Get Consumable Details By Id
        $thresholdSettings = ThresholdSettings::first();
        $consumableDetails = Consumable::where('id',$cons_id)->first();
        $singleConsThreshold = $consumableDetails->consumable_thresholds;
        $totSingleConsThreshold = $consumableDetails->qty;
        $total_single_chkout_consumables  = Consumable::getCheckoutConsumableTotalById($cons_id)[0]->total_checkouts;
        $availableSingleConsumables = $totSingleConsThreshold - $total_single_chkout_consumables;
        //Get Details By Category
        $availableConsumables = 0;
        $thresouldCatValue = 0;
        if (!empty($category_id)) {
            $thresouldDtl = Threshold::where('cat_id', '=', $category_id)->first();
            $totalConsumableData = Consumable::getCatConsumableTotal($category_id);
            $totalCheckoutData = Consumable::getCheckoutConsumableTotalByCat($category_id);
            $totalScrapConsumableData = Consumable::getCatConsumableTotalScrap($category_id);
            $total_consumable = !empty($totalConsumableData) ? $totalConsumableData[0]->total_consumables : 0;
            $total_scrap_qty_consumables = !empty($totalScrapConsumableData) ? $totalScrapConsumableData[0]->total_scrap_qty_consumables : 0;
            $total_chkout_consumables = !empty($totalCheckoutData) ? $totalCheckoutData[0]->total_checkouts : 0;
            $availableConsumables = $total_consumable - ($total_chkout_consumables + $total_scrap_qty_consumables);
            $thresouldCatValue = !empty($thresouldDtl) ? $thresouldDtl->threshold : 0;
        }
        $thresholdUserEmails = ThresholdAlertSettings::where('threshold_alert_settings.asset_id', $cons_id)
        ->where('threshold_alert_settings.asset_type', 4)
        ->join('users', 'users.id', '=', 'threshold_alert_settings.user_id')
        ->pluck('users.email') 
        ->toArray();
        $alertmail = [];
        if($thresholdSettings->threshold_enabled && $thresholdSettings->alerts_enabled ) {
            if($thresholdSettings->send_alerts == 1){
                $alertmail[] = $thresholdSettings->email;
            }
            if (!empty($thresholdUserEmails)) {
                $alertmail = array_merge($alertmail,$thresholdUserEmails);
            }
        }
        if (config('mail.service_enabled') && !empty($alertmail) && ($singleConsThreshold > 0) && ($availableSingleConsumables <= $singleConsThreshold)) {
            Mail::to($alertmail)->cc($alertnotify)->send(new ConsumableThreshouldNotification($consumableDetails, $singleConsThreshold, $availableSingleConsumables, $totSingleConsThreshold));
        } elseif(config('mail.service_enabled') && !empty($alertmail) && ($thresouldCatValue >= 0) && ($availableConsumables <= $thresouldCatValue)) {
            $catDetail = Category::find($category_id);
            Mail::to($alertmail)->cc($alertnotify)->send(new ThreshouldNotification ($catDetail,$thresouldCatValue,$availableConsumables) );
            $thresouldDtl->notify_count = $thresouldDtl->notify_count + 1 ;
            $thresouldDtl->last_notified_date = date('Y-m-d');
            $thresouldDtl->save();
        }
    }

    public function onError(\Throwable $e)
    {
        // Handle the exception how you'd like.
    }
}
