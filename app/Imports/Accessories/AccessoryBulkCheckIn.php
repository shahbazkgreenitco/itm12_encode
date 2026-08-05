<?php

namespace App\Imports\Accessories;

use App\Models\Actionlog;
use App\Models\BulkActions;
use App\Models\Accessory;
use App\Models\AccessoryUser;
use App\Models\User;
use App\Models\Device;
use App\Models\Place;
use App\Models\Label;
use App\Models\Currency;
use Carbon\Carbon;
use App\Helpers\Common as CommonHelper;
use Illuminate\Support\Str;
use Validator;
use Storage;
use Auth;
use Illuminate\Validation\Rule;
use App\Rules\ValidCarbon;
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
use Log;
use DB;

class AccessoryBulkCheckIn implements ToCollection, SkipsEmptyRows, WithHeadingRow, SkipsOnError
 {
    use Importable, SkipsErrors;
    public $data, $request;

    public function __construct( Request $request ) {
        $this->request = $request;
    }

    /**
    * @param Collection $collection
    */

    public function collection( Collection $collection ) {
        $return = [ 'msg'=>'Unable to checkout the given device list', 'status'=>'danger' ];
        try {
            $success = 0;
            $fail = 0;
            $fail_msgs = [];
            $data = [];
            $exception_break = false;
            $tot_insert_records = 0;
            $invalied_key = false;
            $companyId = CommonHelper::getAccessibleCompanyIds();
            $required_keys = [ 'batch_no', 'assigned_for', 'assigned_to', 'scrap_qty', 'note' ];

            foreach ( $collection as $key => $row ) {
                $row = CommonHelper::importColumnValidate( $row );
                $validation_rules = [
                    // 'batch_no' => 'required|string|clean_text_only|exists:accessories,batch_no|starts_with:a,A,ac,AC',
                    'batch_no' => 'required|clean_text_only|string|starts_with:a,A,ac,AC',
                    'assigned_for' => 'required',
                    'assigned_to' => 'required',
                    'scrap_qty' => 'nullable|integer|min:1',
                    'note' => [
                        Rule::requiredIf( fn () => config( 'app.client' ) === 'knightfrank' ),
                        'nullable',
                        'clean_text_only',
                        'max:255',
                    ],
                ];

                $validateTag = Validator::make( $row->toArray(), $validation_rules );
                if ( $validateTag->fails() ) {
                    $error_field = ( $validateTag->errors()->keys() )[ 0 ];
                    $fail++;
                    $batchNoText = !empty( $row[ 'batch_no' ] ) ? ' ( Batch No: ' . $row[ 'batch_no' ]. ')' : '';
                    $fail_msgs[] = 'Row ' . ( $key + 1 ) . ' failed: ' . $validateTag->errors()->first( $error_field ) . $batchNoText;
                    $data[] = [
                        $row[ 'batch_no' ],
                        $row[ 'assigned_for' ],
                        $row[ 'assigned_to' ],
                        $row[ 'scrap_qty' ],
                        $row[ 'note' ],
                        'fail',
                        $validateTag->errors()->first( $error_field )
                    ];
                    continue;
                }
                /* get accessory id from batch_no */
                if ( config( 'app.client' ) == 'etherealmachines' ) {
                    $desired_accessory_id = ( string ) str_ireplace( [ 'AC', 'A' ], '', $row[ 'batch_no' ] );
                } else {
                    $desired_accessory_id = ( string ) str_ireplace( 'A', '', $row[ 'batch_no' ] );
                }

                $accessory_col = Accessory::where( 'id', $desired_accessory_id )->whereNull( 'deleted_at' )->first();

                if ( !( $accessory_col ) ) {
                    $fail++;
                    $fail_msgs[] = 'Row ' . ( $key + 1 ) . ' failed: ' . trans( 'content.component_fields.the_record_not_found', [ 'batch_no' =>  $row[ 'batch_no' ] ] );
                    $data[] = [
                        $row[ 'batch_no' ],
                        $row[ 'assigned_for' ],
                        $row[ 'assigned_to' ],
                        $row[ 'scrap_qty' ],
                        $row[ 'note' ],
                        'fail',
                        "The record not found for the Batch No: '" . $row[ 'batch_no' ] . "'"
                    ];
                    continue;
                }

                if ( !in_array( $accessory_col->company_id, $companyId ) ) {
                    $fail++;
                    $fail_msgs[] = trans( 'content.accessory_fields.permission_denied_for_accessories_checkin', [ 'batch_no' =>  $row[ 'batch_no' ] ] );
                    $data[] = [
                        $row[ 'batch_no' ],
                        $row[ 'assigned_for' ],
                        $row[ 'assigned_to' ],
                        $row[ 'scrap_qty' ],
                        $row[ 'note' ],
                        'fail',
                        trans( 'content.accessory_fields.permission_denied_for_accessories_checkin' )
                    ];
                    Log::info( "Permission denied: You don't have access to checkin accessories (Batch No: " . $row[ 'batch_no' ] . ')' );
                    continue;
                }

                $assignForMap = [ 'User' => 1, 'Place' => 2, 'Device' => 3 ];
                $assign_for = $assignForMap[ $row[ 'assigned_for' ] ] ?? null;

                if ( isset( $assignForMap[ $row[ 'assigned_for' ] ] ) ) {
                    $assign_for = $assignForMap[ $row[ 'assigned_for' ] ];
                } else {
                    $fail++;
                    $fail_msgs[] = "Please provide the text 'User', 'Place' or 'Device' in the 'Assigned For' field";
                    $data[] = [
                        $row[ 'batch_no' ],
                        $row[ 'assigned_for' ],
                        $row[ 'assigned_to' ],
                        $row[ 'scrap_qty' ],
                        $row[ 'note' ],
                        'fail',
                        "Please provide the text 'User', 'Place' or 'Device' in the 'Assigned For' field"
                    ];
                    continue;
                }
                if ( $row[ 'assigned_for' ] == 'User' ) {
                    $user = User::where( 'username', 'like', $row[ 'assigned_to' ] )->where( 'company_id', $accessory_col->company_id )->first();
                    if ( empty( $user ) ) {
                        $fail++;
                        $fail_msgs[] = "User not found for that company '" . $row[ 'batch_no' ] . "'";
                        $data[] = [
                            $row[ 'batch_no' ],
                            $row[ 'assigned_for' ],
                            $row[ 'assigned_to' ],
                            $row[ 'scrap_qty' ],
                            $row[ 'note' ],
                            'fail',
                            "User not found for that company '" . $row[ 'batch_no' ] . "'"
                        ];
                        continue;
                    } elseif ( $user->checkoutBasicClearance() ) {
                        $fail++;
                        $fail_msgs[] = "User inactive. Cannot checkin '" . $row[ 'batch_no' ] . "'";
                        $data[] = [
                            $row[ 'batch_no' ],
                            $row[ 'assigned_for' ],
                            $row[ 'assigned_to' ],
                            $row[ 'scrap_qty' ],
                            $row[ 'note' ],
                            'fail',
                            "User inactive. Cannot checkin '" . $row[ 'batch_no' ] . "'"
                        ];
                        continue;
                    } elseif ( $user->checkLastWorkingDate() ) {
                        $fail++;
                        $fail_msgs[] = "User's last working date passed. Cannot checkin '" . $row[ 'batch_no' ] . "'";
                        $data[] = [
                            $row[ 'batch_no' ],
                            $row[ 'assigned_for' ],
                            $row[ 'assigned_to' ],
                            $row[ 'scrap_qty' ],
                            $row[ 'note' ],
                            'fail',
                            "User's last working date passed. Cannot checkin '" . $row[ 'batch_no' ] . "'"
                        ];
                        continue;
                    }
                    $assigned_to = $user->id;

                } elseif ( $row[ 'assigned_for' ] == 'Place' ) {
                    $place = Place::where( 'place', 'like', $row[ 'assigned_to' ] )->where( 'company_id', $accessory_col->company_id )->first();
                    if ( empty( $place ) ) {
                        $fail++;
                        $fail_msgs[] = "Place not found for that company '" . $row[ 'batch_no' ] . "'";
                        $data[] = [
                            $row[ 'batch_no' ],
                            $row[ 'assigned_for' ],
                            $row[ 'assigned_to' ],
                            $row[ 'scrap_qty' ],
                            $row[ 'note' ],
                            'fail',
                            "Place not found for that company '" . $row[ 'batch_no' ] . "'"
                        ];
                        continue;
                    }
                    $assigned_to = $place->id;
                } elseif ( $row[ 'assigned_for' ] == 'Device' ) {
                    $device = Device::where( 'asset_tag', 'like', $row[ 'assigned_to' ] )->where( 'company_id', $accessory_col->company_id )->first();
                    if ( empty( $device ) ) {
                        $fail++;
                        $fail_msgs[] = "Device not found for that company '" . $row[ 'batch_no' ] . "'";
                        $data[] = [
                            $row[ 'batch_no' ],
                            $row[ 'assigned_for' ],
                            $row[ 'assigned_to' ],
                            $row[ 'scrap_qty' ],
                            $row[ 'note' ],
                            'fail',
                            "Device not found for that company '" . $row[ 'batch_no' ] . "'"
                        ];
                        continue;
                    }

                    $assigned_to = $device->id;
                }

                $accessory = $accessory_col;
                $acc = AccessoryUser::where( 'accessory_id', $accessory_col->id )->where( 'assigned_for', $assign_for )->where( 'assigned_to', $assigned_to );
                $accessoryCheckin = $acc->first();
                if ( empty( $accessoryCheckin ) ) {
                    $fail++;
                    $fail_msgs[] = 'Row ' . ( $key + 1 ) . ' failed: ' . trans( 'content.accessory_fields.Accessory_currently', [ 'batch_no' =>  $row[ 'batch_no' ] ] ) . ' ( Batch No: ' . $row[ 'batch_no' ]. ')';
                    $data[] = [
                        $row[ 'batch_no' ],
                        $row[ 'assigned_for' ],
                        $row[ 'assigned_to' ],
                        $row[ 'scrap_qty' ],
                        $row[ 'note' ],
                        'fail',
                        "Accessory currently not available for checkin for the Batch No: '" . $row[ 'batch_no' ] . "'"
                    ];
                    continue;
                }

                $deleteStatus = false;
                if ( !empty( $row[ 'scrap_qty' ] ) ) {
                    if ( $row[ 'scrap_qty' ] > ( $accessory_col->qty - $accessory_col->scrap_qty ) || $row[ 'scrap_qty' ] > ( $accessory_col->placesCount()+ $accessory_col->devicesCount() + $accessory_col->usersCount() ) ) {
                        $fail++;
                        $fail_msgs[] = 'Row ' . ( $key + 1 ) . ' failed: Accessory scrap qty not available ' . ' ( Batch No: ' . $row[ 'batch_no' ]. ')' ;
                        $data[] = [
                            $row[ 'batch_no' ],
                            $row[ 'assigned_for' ],
                            $row[ 'assigned_to' ],
                            $row[ 'scrap_qty' ],
                            $row[ 'note' ],
                            'fail',
                            "Accessory scrap qty not available for the Batch No: '" . $row[ 'batch_no' ] . "'"
                        ];
                        continue;
                    }

                    $perticular = AccessoryUser::where( 'accessory_id', $accessory->id )->where( 'assigned_for', $assign_for )->where( 'assigned_to', $assigned_to );
                    $perticularCount = $perticular->count();

                    if ( $row[ 'scrap_qty' ] > $perticularCount ) {
                        $fail++;
                        $fail_msgs[] = 'Row ' . ( $key + 1 ) . ' failed: Accessory cannot be scrapped as the quantity exceeds the assigned quantity for '
                        . $row[ 'assigned_to' ] . ' for Batch No. ' . $row[ 'batch_no' ] . '.';

                        $data[] = [
                            $row[ 'batch_no' ],
                            $row[ 'assigned_for' ],
                            $row[ 'assigned_to' ],
                            $row[ 'scrap_qty' ],
                            $row[ 'note' ],
                            'fail',
                            'Accessory cannot be scrapped as the quantity exceeds the assigned quantity for '
                            . $row[ 'assigned_to' ] . ' for Batch No. ' . $row[ 'batch_no' ] . '.',
                        ];

                        continue;
                    }
                    ;

                    if ( $row[ 'scrap_qty' ] > 0 && ( $row[ 'scrap_qty' ] <= $perticularCount ) ) {
                        $accessory->scrap_qty =  $accessory->scrap_qty + $row[ 'scrap_qty' ];
                    }
                    $perticular->pluck( 'id' );
                    $deleteStatus = $perticular->delete();
                } else {
                    $deleteStatus = $acc->delete();
                }
                if ( !$deleteStatus ) {
                    $fail++;
                    $fail_msgs[] = 'Row ' . ( $key + 1 ) . ' failed: ' . trans( 'content.accessory_fields.Unable_to_checkin_back_the_accessory', [ 'batch_no' =>  $row[ 'batch_no' ] ] ) . ' ( Batch No: ' . $row[ 'batch_no' ]. ')';
                    $data[] = [
                        $row[ 'batch_no' ],
                        $row[ 'assigned_for' ],
                        $row[ 'assigned_to' ],
                        $row[ 'scrap_qty' ],
                        $row[ 'note' ],
                        'fail',
                        "Unable_to_checkin_back_the_accessory: '" . $row[ 'batch_no' ] . "'"
                    ];
                    continue;
                }

                if ( $accessory->save() ) {
                    $data[] = [
                        $row[ 'batch_no' ],
                        $row[ 'assigned_for' ],
                        $row[ 'assigned_to' ],
                        $row[ 'scrap_qty' ],
                        $row[ 'note' ],
                        'success',
                        'Chosen Accessory has checked In successfully'
                    ];
                    $success++;
                    $log = new Actionlog();
                    $log->asset_type = 'accessory';
                    $log->accessory_id = $accessory_col->id;
                    $log->note = $row[ 'note' ];
                    $log->checkedout_to = $accessoryCheckin->assigned_to;
                    $log->user_id = Auth::user()->id;
                    $log->action_type = 'checkin from';
                    $log->in_out_id = $accessoryCheckin->device_id;
                    $log->save();
                    DB::table( 'asset_logs' )->where( 'id', $accessoryCheckin->device_id )->update( [ 'in_out_id' => $log->id ] );
                }

            }
            $return[ 'fail' ] = $fail;
            $return[ 'success' ] = $success;
            $return[ 'fail_msgs' ] = $fail_msgs;
            $this->data = $return;
        } catch( \Exception $e ) {
            Log::error( 'accessory bulk Checkin error: ' . $e->getMessage() );
        }

        $file_name = $this->request->file( 'import_file' );
        $given_file_original_name = preg_replace( '@[^0-9a-z\.]+@i', '', $file_name->getClientOriginalName() );

        $defaultKkeys = array( 'Batch No', 'Assigned For', 'Assigned To', 'Scrap Qty', 'Note' );
        $succ_fail = array( 'Success/Fail', 'Message' );
        $keys = array_merge( $defaultKkeys, $succ_fail );
        $name = 'AccessoryBulkCheckinFormat_'.date( 'dmYHis' ).'.xlsx';
        $doc_path = Excel::store( new AccessoryImportStore( $data, $keys ), $name, 'bulk_documents' );

        $log = new BulkActions();

        $log->action_type = 2;
        $log->module_id = 3;
        $log->created_at = date( 'Y-m-d H:i:s' );
        $log->doc_path = $name;
        $log->doc_name = $given_file_original_name;
        $log->tot_success = $success;
        $log->tot_failure = $fail;
        $log->user_id = Auth::user()->id;
        $log->save();

        $return[ 'fail' ] = $fail;
        $return[ 'success' ] = $success;
        $return[ 'fail_msgs' ] = $fail_msgs;
        $this->data = $return;
    }

    public function model( array $row ) {
        $invalied_key = false;
        $required_keys = [ 'batch_no', 'scrap_qty', 'note' ];

    }

    public function onError( \Throwable $e ) {
        // Handle the exception how you'd like.
    }
}
