<?php

namespace App\Imports\Device;

use App\Models\Actionlog;
use App\Models\BulkActions;
use App\Models\Category;
use App\Models\Company;
use App\Models\Device;
use App\Models\Label;
use App\Models\Location;
use App\Models\Model;
use App\Models\ProjectManagement\Project;
use App\Models\Settings;
use App\Models\User;
use App\Models\Place;
use App\Models\AssetAllocationType;
use App\Models\AssetInOutReason;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
use Log;
use Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Helpers\Common as CommonHelper;
use App\Models\TransferItem;

class DeviceBulkCheckout implements ToCollection, SkipsEmptyRows, WithHeadingRow, SkipsOnError
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

    private function addResultRow(array &$data, $row, string $status, string $message): void
    {
        $data[] = [
            $row['serial'] ?? '',
            $row['device_name'] ?? '',
            $row['checkout_to'] ?? '',
            $row['checkout_user_place'] ?? '',
            $row['checkout_reason'] ?? '',
            $row['project_name'] ?? '',
            $row['last_checkout'] ?? '',
            $row['expected_checkin_date'] ?? '',
            $row['notes'] ?? '',
            $row['allocation_type'] ?? '',
            $status,
            $message,
        ];
    }
    public function collection(Collection $collection)
    {
        $return = ["msg" => "Unable to checkout the given device list", "status" => "danger"];
        try {
            $success = 0;
            $fail = 0;
            $fail_msgs = [];
            $data = [];
            $exception_break = false;
            $tot_insert_records = 0;
            $invalied_key = false;
            $deployedLabel = Label::getDeployedLabel()->id;
            $soldLabel = Label::getSoldLabel()->id;
            $pendingLabel = Label::getPendingLabel()->id;
            $scrapLabel = Label::getScrapLabel()->id;
            $repairLabel = Label::getRepairLabel()->id;
            $lostLabel = Label::getLostLabel()->id;
            $companyId = CommonHelper::getAccessibleCompanyIds();
            $reasons = AssetInOutReason::where(['action_type' => 2, 'status' => 1])->get()->pluck('id', 'name')->toArray();
            $required_keys = ['serial', 'device_name', 'checkout_to', 'checkout_user_place', 'checkout_reason', 'project_name', 'last_checkout', 'expected_checkin_date', 'notes', 'allocation_type'];

            foreach ($collection as $key => $row) {
                $collected_keys_tot = 0;
                foreach ($row as $trim_k => $trim_v) {
                    if (! $trim_k) {
                        continue;
                    }
                    if (in_array($trim_k, $required_keys)) {
                        $collected_keys_tot++;
                    } else {
                        $invalied_key = true;
                        break;
                    }
                }
                if ($invalied_key || count($required_keys) != $collected_keys_tot) {
                    $exception_break = true;
                    $return["msg"] = trans('content.device_fields.invalid_column');
                    Session::flash('msg', $return);
                    continue;
                }

                if (trim($row['serial']) == "" || $row['serial'] == NULL) {
                    $fail++;
                    $fail_msgs[] = "Serial is not found";
                    $data[] = [
                        $row['serial'],
                        $row['device_name'],
                        $row['checkout_to'],
                        $row['checkout_user_place'],
                        $row['checkout_reason'],
                        $row['project_name'],
                        $row['last_checkout'],
                        $row['expected_checkin_date'],
                        $row['notes'],
                        $row['allocation_type'],
                        'fail',
                        'The record for serial "' . $row['serial'] . '" has not found.'
                    ];
                    continue;
                }

                $validateTag = Validator::make($row->toArray(), [
                    'serial' => 'required|exists:assets,serial'
                ]);

                if ($validateTag->fails()) {
                    $fail++;
                    $fail_msgs[] = "The record for serial '" . $row['serial'] . "' has not found.";
                    $data[] = [
                        $row['serial'],
                        $row['device_name'],
                        $row['checkout_to'],
                        $row['checkout_user_place'],
                        $row['checkout_reason'],
                        $row['project_name'],
                        $row['last_checkout'],
                        $row['expected_checkin_date'],
                        $row['notes'],
                        $row['allocation_type'],
                        'fail',
                        "The record for serial '" . $row['serial'] . "' has not found."
                    ];
                    continue;
                }

                $validateAssignedFor = Validator::make($row->toArray(), [
                    'checkout_to' => 'required'
                ]);

                if ($validateAssignedFor->fails()) {
                    $fail++;
                    $fail_msgs[] = "Assigned for is not found.";
                    $data[] = [
                        $row['serial'],
                        $row['device_name'],
                        $row['checkout_to'],
                        $row['checkout_user_place'],
                        $row['checkout_reason'],
                        $row['project_name'],
                        $row['last_checkout'],
                        $row['expected_checkin_date'],
                        $row['notes'],
                        $row['allocation_type'],
                        'fail',
                        "Assigned for is not found."
                    ];
                    continue;
                }

                $validateAssignedTo = Validator::make($row->toArray(), [
                    'checkout_user_place' => 'required'
                ]);

                if ($validateAssignedTo->fails()) {
                    $fail++;
                    $fail_msgs[] = "Assigned To is not found.";
                    $data[] = [
                        $row['serial'],
                        $row['device_name'],
                        $row['checkout_to'],
                        $row['checkout_user_place'],
                        $row['checkout_reason'],
                        $row['project_name'],
                        $row['last_checkout'],
                        $row['expected_checkin_date'],
                        $row['notes'],
                        $row['allocation_type'],
                        'fail',
                        "Assigned To is not found."
                    ];
                    continue;
                }

                $validateCheckoutReason = Validator::make($row->toArray(), [
                    'checkout_reason' => [
                        Rule::requiredIf(fn() => config('app.client') === 'knightfrank' || config('app.client') === 'rolepermission'),
                        'nullable',
                        'string',
                        'exists:asset_inout_reason,name'
                    ]
                ]);
                if ($validateCheckoutReason->fails()) {
                    $fail++;
                    $fail_msgs[] = "Please provide the checkout reason correctly";
                    $data[] = [
                        $row['serial'],
                        $row['device_name'],
                        $row['checkout_to'],
                        $row['checkout_user_place'],
                        $row['checkout_reason'],
                        $row['project_name'],
                        $row['last_checkout'],
                        $row['expected_checkin_date'],
                        $row['notes'],
                        $row['allocation_type'],
                        'fail',
                        "Please provide the checkout reason correctly."
                    ];
                    continue;
                }

                $validatePct = Validator::make($row->toArray(), [
                    'project_name' => 'nullable|string|exists:projects,name'
                ]);

                if ($validatePct->fails()) {
                    $fail++;
                    $fail_msgs[] = "Please provide the project name correctly";
                    $data[] = [
                        $row['serial'],
                        $row['device_name'],
                        $row['checkout_to'],
                        $row['checkout_user_place'],
                        $row['checkout_reason'],
                        $row['project_name'],
                        $row['last_checkout'],
                        $row['expected_checkin_date'],
                        $row['notes'],
                        $row['allocation_type'],
                        'fail',
                        "Please provide the project name correctly."
                    ];
                    continue;
                }

                $validateAllocationType = Validator::make($row->toArray(), [
                    'allocation_type' => 'required'
                ]);

                if ($validateAllocationType->fails()) {
                    $fail++;
                    $fail_msgs[] = "Allocation Type is not found.";
                    $data[] = [
                        $row['serial'],
                        $row['device_name'],
                        $row['checkout_to'],
                        $row['checkout_user_place'],
                        $row['checkout_reason'],
                        $row['project_name'],
                        $row['last_checkout'],
                        $row['expected_checkin_date'],
                        $row['notes'],
                        $row['allocation_type'],
                        'fail',
                        "Allocation Type is not found."
                    ];
                    continue;
                }
                $validateNoteType = Validator::make($row->toArray(), [
                    'notes' => [
                        Rule::requiredIf(fn() => config('app.client') === 'knightfrank'),
                        'nullable',
                        'clean_text_only',
                        'max:2000',
                    ],
                ]);

                if ($validateNoteType->fails()) {
                    $fail++;
                    $fail_msgs[] = "Note is required for the serial: '" . $row['serial'] . "'";
                    $data[] = [
                        $row['serial'],
                        $row['device_name'],
                        $row['checkout_to'],
                        $row['checkout_user_place'],
                        $row['checkout_reason'],
                        $row['project_name'],
                        $row['last_checkout'],
                        $row['expected_checkin_date'],
                        $row['notes'],
                        $row['allocation_type'],
                        'fail',
                        "Note is required for the serial: '" . $row['serial'] . "'"
                    ];
                    continue;
                }
                $device_col = Device::where("serial", "like", $row['serial'])->whereNull("deleted_at")->limit(1)->get();
                if (! count($device_col)) {
                    $fail++;
                    $fail_msgs[] = "The record not found for the serial: '" . $row['serial'] . "'";
                    $data[] = [
                        $row['serial'],
                        $row['device_name'],
                        $row['checkout_to'],
                        $row['checkout_user_place'],
                        $row['checkout_reason'],
                        $row['project_name'],
                        $row['last_checkout'],
                        $row['expected_checkin_date'],
                        $row['notes'],
                        $row['allocation_type'],
                        'fail',
                        "The record not found for the serial: '" . $row['serial'] . "'"
                    ];
                    continue;
                }

                $device = $device_col[0];
                if (!in_array($device->company_id, $companyId)) {
                    $fail++;
                    $fail_msgs[] = "Permission denied to checkout for another company for the serial: '" . $row['serial'] . "'";
                    $data[] = [
                        $row['serial'],
                        $row['device_name'],
                        $row['checkout_to'],
                        $row['checkout_user_place'],
                        $row['checkout_reason'],
                        $row['project_name'],
                        $row['last_checkout'],
                        $row['expected_checkin_date'],
                        $row['notes'],
                        $row['allocation_type'],
                        'fail',
                        "Permission denied to checkout for another company for the serial: '" . $row['serial'] . "'",
                    ];
                    continue;
                }
                $oldStatus = $device->status_id;

                $dev_transfer = TransferItem::where('device_id', $device->id)->where('transfer_status', 1)->first();
                if (!empty($dev_transfer)) {
                    $fail++;
                    $fail_msgs[] = "'" . $row['serial'] . "'Selected Device is Under Transfer'";
                    $data[] = [
                        $row['serial'],
                        $row['device_name'],
                        $row['checkout_to'],
                        $row['checkout_user_place'],
                        $row['checkout_reason'],
                        $row['project_name'],
                        $row['last_checkout'],
                        $row['expected_checkin_date'],
                        $row['notes'],
                        $row['allocation_type'],
                        'fail',
                        "'" . $row['serial'] . "'Selected Device is Under Transfer'"
                    ];
                    continue;
                }

                $validate = Validator::make((array) $row, [
                    'name' => 'nullable|clean_text_only|string|max:100',
                    'last_checkout' => 'nullable|clean_text_only|string|max:30',
                    'expected_checkin_date' => 'nullable|clean_text_only|string|max:30',
                    //'notes' => 'nullable|clean_text_only|string|max:2000',
                    'project_name' => 'nullable|clean_text_only|string|max:2000',
                ]);

                if ($validate->fails()) {
                    $errors = $validate->errors()->all();
                    $fail++;
                    $fail_msgs[] = "The record : '" . $row['serial'] . "' has not able to checkout due to: " . implode(', ', $errors);
                    $data[] = [
                        $row['serial'],
                        $row['device_name'],
                        $row['checkout_to'],
                        $row['checkout_user_place'],
                        $row['checkout_reason'],
                        $row['project_name'],
                        $row['last_checkout'],
                        $row['expected_checkin_date'],
                        $row['notes'],
                        $row['allocation_type'],
                        'fail',
                        "The record : '" . $row['serial'] . "' has not able to checkout due to: " . implode(', ', $errors),
                    ];
                    continue;
                }

                if ($row['device_name']) {
                    $device->name = strtolower($row['device_name']) != "null" ? $row['device_name'] : $device->name;
                }

                if ($device->status_id == $deployedLabel) {
                    $fail++;
                    $fail_msgs[] = "This " . $row['serial'] . " device is already checked out to the User/Place, please check in this device first.";
                    $data[] = [
                        $row['serial'],
                        $row['device_name'],
                        $row['checkout_to'],
                        $row['checkout_user_place'],
                        $row['checkout_reason'],
                        $row['project_name'],
                        $row['last_checkout'],
                        $row['expected_checkin_date'],
                        $row['notes'],
                        $row['allocation_type'],
                        'fail',
                        "This " . $row['serial'] . " device is already checked out to the User/Place, please check in this device first."
                    ];
                    continue;
                }

                if ($device->status_id == $soldLabel || $device->status_id == $pendingLabel || $device->status_id == $scrapLabel || $device->status_id == $lostLabel || $device->status_id == $repairLabel) {
                    $fail++;
                    $fail_msgs[] = "Chosen device is not in valid status, so unable to checkout for '" . $row['serial'] . "'";
                    $data[] = [
                        $row['serial'],
                        $row['device_name'],
                        $row['checkout_to'],
                        $row['checkout_user_place'],
                        $row['checkout_reason'],
                        $row['project_name'],
                        $row['last_checkout'],
                        $row['expected_checkin_date'],
                        $row['notes'],
                        $row['allocation_type'],
                        'fail',
                        "Chosen device is not in valid status, so unable to checkout for '" . $row['serial'] . "'."
                    ];
                    continue;
                }

                try {
                    if ($row['checkout_to'] == "User") {
                        $device->assigned_for = 1;
                    }

                    if ($row['checkout_to'] == "Place") {
                        $device->assigned_for = 2;
                    }
                } catch (\Exception $e) {
                    $fail++;
                    $fail_msgs[] = "Please provide the text 'User' or 'Place' in assigned for field";
                    $data[] = [
                        $row['serial'],
                        $row['device_name'],
                        $row['checkout_to'],
                        $row['checkout_user_place'],
                        $row['checkout_reason'],
                        $row['project_name'],
                        $row['last_checkout'],
                        $row['expected_checkin_date'],
                        $row['notes'],
                        $row['allocation_type'],
                        'fail',
                        "Please provide the text 'User' or 'Place' in assigned for field"
                    ];
                }

                if ($row['checkout_to'] == "User") {
                    try {
                        if ($row['checkout_user_place']) {
                            $thisOwnerId  = User::where('username', 'like', $row['checkout_user_place'])->where('company_id', $device->company_id)->first()->id;
                            $device->assigned_to = $thisOwnerId;
                            $device->status_id = 6;
                            $device->stock_place = null;

                            $user = User::find($device->assigned_to);
                            if (empty($user)) {
                                $fail++;
                                $fail_msgs[] = "Chosen user is not found for the device '" . $row['serial'] . "' Because either the user does not exist or the company of this device does not have this user.";
                                $data[] = [
                                    $row['serial'],
                                    $row['device_name'],
                                    $row['checkout_to'],
                                    $row['checkout_user_place'],
                                    $row['checkout_reason'],
                                    $row['project_name'],
                                    $row['last_checkout'],
                                    $row['expected_checkin_date'],
                                    $row['notes'],
                                    $row['allocation_type'],
                                    'fail',
                                    "Chosen user is not found for the device '" . $row['serial'] . "' Because either the user does not exist or the company of this device does not have this user.",
                                ];
                                continue;
                            }
                            if ($user->checkoutBasicClearance()) {
                                $fail++;
                                $fail_msgs[] = "Chosen user is not in active status , so unable to checkout this device '" . $row['serial'] . "'";
                                $data[] = [
                                    $row['serial'],
                                    $row['device_name'],
                                    $row['checkout_to'],
                                    $row['checkout_user_place'],
                                    $row['checkout_reason'],
                                    $row['project_name'],
                                    $row['last_checkout'],
                                    $row['expected_checkin_date'],
                                    $row['notes'],
                                    $row['allocation_type'],
                                    'fail',
                                    "Chosen user is not in active status , so unable to checkout this device '" . $row['serial'] . "'"
                                ];
                                continue;
                            }
                            if ($user->checkLastWorkingDate()) {
                                $fail++;
                                $fail_msgs[] = "This user's last working date has already completed. Hence unable to check out this device '" . $row['serial'] . "'";
                                $data[] = [
                                    $row['serial'],
                                    $row['device_name'],
                                    $row['checkout_to'],
                                    $row['checkout_user_place'],
                                    $row['checkout_reason'],
                                    $row['project_name'],
                                    $row['last_checkout'],
                                    $row['expected_checkin_date'],
                                    $row['notes'],
                                    $row['allocation_type'],
                                    'fail',
                                    "This user's last working date has already completed. Hence unable to check out this device '" . $row['serial'] . "'"
                                ];
                                continue;
                            }
                        }
                    } catch (\Exception $e) {
                        $fail++;
                        $fail_msgs[] = "Please provide the correct username for the device '" . $row['serial'] . "'";
                        $data[] = [
                            $row['serial'],
                            $row['device_name'],
                            $row['checkout_to'],
                            $row['checkout_user_place'],
                            $row['checkout_reason'],
                            $row['project_name'],
                            $row['last_checkout'],
                            $row['expected_checkin_date'],
                            $row['notes'],
                            $row['allocation_type'],
                            'fail',
                            "Please provide the correct username for the device '" . $row['serial'] . "'"
                        ];
                        continue;
                    }
                } else if ($row['checkout_to'] == "Place") {
                    try {
                        if ($row['checkout_user_place']) {
                            $thisPlaceId  = Place::where('place', 'like', $row['checkout_user_place'])->where('company_id', $device->company_id)->first()->id;
                            $device->assigned_to = $thisPlaceId;
                            $device->status_id = 6;
                            $device->stock_place = null;
                        }
                    } catch (\Exception $e) {
                        $fail++;
                        $fail_msgs[] = "Please provide the correct place name for the device '" . $row['serial'] . "' Because either the place does not exist or the company of this device does not have this place.";
                        $data[] = [
                            $row['serial'],
                            $row['device_name'],
                            $row['checkout_to'],
                            $row['checkout_user_place'],
                            $row['checkout_reason'],
                            $row['project_name'],
                            $row['last_checkout'],
                            $row['expected_checkin_date'],
                            $row['notes'],
                            $row['allocation_type'],
                            'fail',
                            "Please provide the correct place name for the device '" . $row['serial'] . "' Because either the place does not exist or the company of this device does not have this place.",
                        ];
                        continue;
                    }
                } else {
                    $fail++;
                    $fail_msgs[] = "Please provide the text 'User' or 'Place' in assigned for field";
                    $data[] = [
                        $row['serial'],
                        $row['device_name'],
                        $row['checkout_to'],
                        $row['checkout_user_place'],
                        $row['checkout_reason'],
                        $row['project_name'],
                        $row['last_checkout'],
                        $row['expected_checkin_date'],
                        $row['notes'],
                        $row['allocation_type'],
                        'fail',
                        "Please provide the text 'User' or 'Place' in assigned for field"
                    ];
                    continue;
                }

                if ($row['project_name']) {
                    try {
                        if ($row['project_name']) {

                            $thisProjectId  = Project::where('name', 'like', $row['project_name'])->first()->id;
                            $device->project_name = $thisProjectId;
                        }
                    } catch (\Exception $e) {
                        $fail++;
                        $fail_msgs[] = "Please provide the correct project name or leave it as blank for the device'" . $row['serial'] . "'";
                        $data[] = [
                            $row['serial'],
                            $row['device_name'],
                            $row['checkout_to'],
                            $row['checkout_user_place'],
                            $row['checkout_reason'],
                            $row['project_name'],
                            $row['last_checkout'],
                            $row['expected_checkin_date'],
                            $row['notes'],
                            $row['allocation_type'],
                            'fail',
                            "Please provide the correct project name or leave it as blank for the device'" . $row['serial'] . "'"
                        ];
                        continue;
                    }
                }

                if ($row['last_checkout']) {
                    try {
                        if (strtolower($row['last_checkout']) == "null") {
                            $device->last_checkout = null;
                        } elseif ($row['last_checkout'] && gettype($row['last_checkout']) != "string") {
                            $device->last_checkout = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['last_checkout'])->format('Y-m-d');
                        } else {
                            $fail++;
                            $fail_msgs[] = "The record : '" . $row['serial'] . "' has not checkout the devices due to invalid date format. Date can be empty or valid Excel Date Format.";
                            $data[] = [
                                $row['serial'],
                                $row['device_name'],
                                $row['checkout_to'],
                                $row['checkout_user_place'],
                                $row['checkout_reason'],
                                $row['project_name'],
                                $row['last_checkout'],
                                $row['expected_checkin_date'],
                                $row['notes'],
                                $row['allocation_type'],
                                'fail',
                                "The record : '" . $row['serial'] . "' has not checkout the devices due to invalid date format. Date can be empty or valid Excel Date Format."
                            ];
                            continue;
                        }
                    } catch (\Exception $e) {
                        $fail++;
                        $fail_msgs[] = "The record : '" . $row['serial'] . "' has not checkout the devices due to invalid date format. Date can be empty or valid Excel Date Format.";
                        $data[] = [
                            $row['serial'],
                            $row['device_name'],
                            $row['checkout_to'],
                            $row['checkout_user_place'],
                            $row['checkout_reason'],
                            $row['project_name'],
                            $row['last_checkout'],
                            $row['expected_checkin_date'],
                            $row['notes'],
                            $row['allocation_type'],
                            'fail',
                            "The record : '" . $row['serial'] . "' has not checkout the devices due to invalid date format. Date can be empty or valid Excel Date Format."
                        ];
                        continue;
                    }
                }

                if ($row['expected_checkin_date']) {
                    try {
                        if (strtolower($row['expected_checkin_date']) == "null") {
                            $device->expected_checkin = null;
                        } elseif ($row['expected_checkin_date'] && gettype($row['expected_checkin_date']) != "string") {
                            $device->expected_checkin = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['expected_checkin_date'])->format('Y-m-d');
                        } else {
                            $fail++;
                            $fail_msgs[] = "The record : '" . $row['serial'] . "' has not checkout the devices due to invalid date format. Date can be empty or valid Excel Date Format.";
                            $data[] = [
                                $row['serial'],
                                $row['device_name'],
                                $row['checkout_to'],
                                $row['checkout_user_place'],
                                $row['checkout_reason'],
                                $row['project_name'],
                                $row['last_checkout'],
                                $row['expected_checkin_date'],
                                $row['notes'],
                                $row['allocation_type'],
                                'fail',
                                "The record : '" . $row['serial'] . "' has not checkout the devices due to invalid date format. Date can be empty or valid Excel Date Format."
                            ];
                            continue;
                        }
                    } catch (\Exception $e) {
                        $fail++;
                        $fail_msgs[] = "The record : '" . $row['serial'] . "' has not checkout the devices due to invalid date format. Date can be empty or valid Excel Date Format.";
                        $data[] = [
                            $row['serial'],
                            $row['device_name'],
                            $row['checkout_to'],
                            $row['checkout_user_place'],
                            $row['checkout_reason'],
                            $row['project_name'],
                            $row['last_checkout'],
                            $row['expected_checkin_date'],
                            $row['notes'],
                            $row['allocation_type'],
                            'fail',
                            "The record : '" . $row['serial'] . "' has not checkout the devices due to invalid date format. Date can be empty or valid Excel Date Format."
                        ];
                        continue;
                    }
                }

                if ($row['notes']) {
                    $device->notes = strtolower($row['notes']) != "null" ? $row['notes'] : null;
                }

                if ($row['allocation_type']) {
                    $allocationType = AssetAllocationType::where('name', 'like', $row['allocation_type'])->first();
                    if (empty($allocationType)) {
                        $fail++;
                        $fail_msgs[] = "The record for  '" . $row['serial'] . "' has not updated due to invalid allocation type value.";
                        $data[] = [
                            $row['serial'],
                            $row['device_name'],
                            $row['checkout_to'],
                            $row['checkout_user_place'],
                            $row['checkout_reason'],
                            $row['project_name'],
                            $row['last_checkout'],
                            $row['expected_checkin_date'],
                            $row['notes'],
                            $row['allocation_type'],
                            'fail',
                            "The record : '" . $row['serial'] . "' has not updated."
                        ];
                        continue;
                    }
                }

                $device->accepted = null;
                $device->chkin_log_id = null;

                if ($device->save()) {
                    if (in_array(config('app.client'), ["rolepermission", "knightfrank", "rashmi"])) {
                        if (!empty($oldStatus) && !empty($device->status_id)) {
                            CommonHelper::updateStatusCounts($oldStatus, $device->status_id, $device);
                        }
                    }
                    $data[] = [
                        $row['serial'],
                        $row['device_name'],
                        $row['checkout_to'],
                        $row['checkout_user_place'],
                        $row['checkout_reason'],
                        $row['project_name'],
                        $row['last_checkout'],
                        $row['expected_checkin_date'],
                        $row['notes'],
                        $row['allocation_type'],
                        'success',
                        "Chosen device has checked out successfully"
                    ];
                    $allocationType = AssetAllocationType::where('name', 'like', $row['allocation_type'])->first();
                    $success++;
                    $log = new Actionlog();
                    $log->asset_id = $device->id;
                    $log->asset_type = "hardware";
                    $log->assigned_for = $device->assigned_for;
                    $log->checkedout_to = $device->assigned_to;
                    $log->reason_id = $reasons[$row['checkout_reason']] ?? null;
                    $log->interact_id = 10;
                    $log->interact_type = "i4";
                    $log->interact_module = "m1";
                    $log->project_id = $device->project_name;
                    $log->user_id = Auth::user()->id;
                    $log->action_type = "Checkout";
                    $log->note = $device->notes;
                    $log->access_code = sha1(time());
                    $log->allocation_type_id = $allocationType->id;
                    $log->created_at = !empty($row['last_checkout']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['last_checkout'])->format('Y-m-d') : now()->format('Y-m-d H:i:s');
                    $log->save();

                    $device->chkout_log_id = $log->id;
                    $device->accepted = "pending";
                    $device->save();
                } else {
                    $fail++;
                    $fail_msgs[] = "The record : '" . $row['serial'] . "' not able to checkout.";
                    $data[] = [
                        $row['serial'],
                        $row['device_name'],
                        $row['checkout_to'],
                        $row['checkout_user_place'],
                        $row['checkout_reason'],
                        $row['project_name'],
                        $row['last_checkout'],
                        $row['expected_checkin_date'],
                        $row['notes'],
                        $row['allocation_type'],
                        'fail',
                        "The record : '" . $row['serial'] . "' not able to checkout."
                    ];
                }
            }

            $return['fail'] = $fail;
            $return['success'] = $success;
            $return['fail_msgs'] = $fail_msgs;
            $this->data = $return;
        } catch (\Exception $e) {
            Log::error("device bulk Checkout error: " . $e->getMessage());
        }

        $file_name = $this->request->file('import_file');
        $given_file_original_name = preg_replace('@[^0-9a-z\.]+@i', '', $file_name->getClientOriginalName());
        if (!empty($data)) {
            foreach ($data as &$row) {
                if (isset($row[5]) && gettype($row[5]) != 'string' && $row[5] !== null) {
                    $row[5] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[5])->format('n/j/Y');
                }
                if (isset($row[6]) && gettype($row[6]) != 'string' && $row[6] !== null) {
                    $row[6] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[6])->format('n/j/Y');
                }
            }
            unset($row);
        }
        $defaultKkeys = array('Serial', 'Name', 'Checkout To', 'Checkout User/Place', 'Checkout Reason', 'Project Name', 'Last Checkout', 'Expected Checkin Date', 'Notes', 'Allocation Type');
        $succ_fail = array('Success/Fail', 'Message');
        $keys = array_merge($defaultKkeys, $succ_fail);
        $name = 'DeviceBulkCheckoutFormat_' . date('dmYHis') . '.xlsx';
        $doc_path = Excel::store(new DeviceImportStore($data, $keys), $name, 'bulk_documents');

        $log = new BulkActions();

        $log->action_type = 1;
        $log->module_id = 1;
        $log->created_at = date("Y-m-d H:i:s");
        $log->doc_path = $name;
        $log->doc_name = $given_file_original_name;
        $log->tot_success = $success;
        $log->tot_failure = $fail;
        $log->user_id = Auth::user()->id;
        $log->save();

        if (config('app.socket_enabled')) {
            CommonHelper::sendDeviceCountToSocket();
        }

        $return['fail'] = $fail;
        $return['success'] = $success;
        $return['fail_msgs'] = $fail_msgs;
        $this->data = $return;
    }

    public function model(array $row)
    {
        $invalied_key = false;
        $required_keys = ['serial', 'device_name', 'checkout_to', 'checkout_user_place', 'checkout_reason', 'project_name', 'last_checkout', 'expected_checkin_date', 'notes', 'allocation_type'];
    }

    public function onError(\Throwable $e)
    {
        // Handle the exception how you'd like.
    }
}
