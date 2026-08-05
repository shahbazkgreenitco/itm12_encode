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
use App\Models\Settings;
use App\Models\ThresholdSettings;
use App\Models\Threshold;
use App\Models\ThresholdAlertSettings;
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
use App\Mail\AccessoryThreshouldNotification;
use Log;
use App\Models\License;
use App\Models\LicenseSeat;

class AccessoryBulkCheckout implements ToCollection, SkipsEmptyRows, WithHeadingRow, SkipsOnError
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
            $soldLabel = Label::getSoldLabel()->id;
            $pendingLabel = Label::getPendingLabel()->id;
            $scrapLabel = Label::getScrapLabel()->id;
            $repairLabel = Label::getRepairLabel()->id;
            $lostLabel = Label::getLostLabel()->id;
            $outForRepairLabel = Label::getOutForRepairLabel()->id;

            $required_keys = [ 'batch_no', 'assigned_for', 'assigned_to', 'expected_checkin', 'note' ];

            foreach ( $collection as $key => $row ) {

                $row_data = $row->only( 'batch_no', 'assigned_for', 'assigned_to', 'expected_checkin', 'note' )->toArray();
                $row_data[ 'expected_checkin' ] = ( $row[ 'expected_checkin' ] && gettype( $row[ 'expected_checkin' ] ) != 'string' ) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject( $row[ 'expected_checkin' ] )->format( 'Y-m-d' ) : null;
                $validateTag = Validator::make( $row_data, [
                    'batch_no' => 'required|string|starts_with:a,A',
                    'assigned_for' => [
                        'required',
                        'string',
                        Rule::in( [ 'user', 'place', 'device', 'User', 'Place', 'Device', 'USER', 'PLACE', 'DEVICE' ] )
                    ],
                    'assigned_to' => [
                        'required',
                        'string',
                        'clean_text_only'
                    ],
                    'expected_checkin' => [
                        'nullable',
                        // new ValidCarbon
                    ],
                    //'note' => [ 'nullable', 'clean_text_only' ]
                    'note' => [
                        Rule::requiredIf( fn () => config( 'app.client' ) === 'knightfrank' ),
                        'nullable',
                        'clean_text_only',
                        'max:2000',
                    ],
                ], [
                    'batch_no.required' => 'batch_no required',
                    'batch_no.string' => 'valid batch_no required',
                    'batch_no.starts_with' => 'valid batch_no required',
                    // 'assigned_for.required' => 'assigned_for required',
                    'assigned_for.required' => 'assigned_for must any one of user/place/device',
                    'assigned_for.string' => 'assigned_for must any one of user/place/device',
                    'assigned_to.required' => 'assigned_to required',
                    'assigned_to.string' => 'assigned_to must be valid',
                    'expected_checkin.date' => 'expected_checkin must be valid date format'
                ] );

                if ( $validateTag->fails() ) {
                    $error_field = ( $validateTag->errors()->keys() );
                    $fail++;
                    foreach ( $error_field as $key => $value ) {
                        $fail_msgs[] = trans( 'content.accessory_fields.empty_or_validation_fail', [ 'batch_no' =>$value ] ).' '.$row[ 'batch_no' ];
                    }
                    $data[] = [
                        $row[ 'batch_no' ],
                        $row[ 'assigned_for' ],
                        $row[ 'assigned_to' ],
                        $row[ 'expected_checkin' ],
                        $row[ 'note' ],
                        'fail',
                        trans( 'content.accessory_fields.empty_or_validation_fail', [ 'batch_no' => $row[ 'batch_no' ] ] ),
                    ];
                    Log::info( 'accessory bulk Checkout error: '.trans( 'content.accessory_fields.empty_or_validation_fail', [ 'batch_no' =>$row[ 'batch_no' ] ] ) );
                    continue;
                }

                /* get accessory id from batch_no */
                if ( config( 'app.client' ) == 'etherealmachines' ) {
                    $desired_accessory_id = ( string ) str_ireplace( [ 'AC', 'A' ], '', $row[ 'batch_no' ] );
                } else {
                    $desired_accessory_id = ( string ) str_ireplace( 'A', '', $row[ 'batch_no' ] );
                }

                try {
                    $accessory = Accessory::findOrFail( $desired_accessory_id );
                    if ( !in_array( $accessory->company_id, $companyId ) ) {
                        $fail++;
                        $fail_msgs[] = trans( 'content.accessory_fields.permission_denied_for_accessories_checkout', [ 'batch_no' =>  $row[ 'batch_no' ] ] );
                        $data[] = [
                            $row[ 'batch_no' ],
                            $row[ 'assigned_for' ],
                            $row[ 'assigned_to' ],
                            $row[ 'expected_checkin' ],
                            $row[ 'note' ],
                            'fail',
                            trans( 'content.accessory_fields.permission_denied_for_accessories_checkout', [ 'batch_no' =>  $row[ 'batch_no' ] ] )
                        ];
                        Log::info( "Permission denied: You don't have access to checkout accessories (Batch No: " . $row[ 'batch_no' ] . ')' );
                        continue;
                    }
                    if ( $accessory->availableQty() < 1 ) {
                        throw new \Exception( 'Accessory have not quantity to checkout' );
                    }
                } catch( \Exception $e ) {
                    $fail++;
                    $fail_msgs[] = trans( 'content.accessory_fields.batch_no_invalid_values', [ 'batch_no' =>  $row[ 'batch_no' ] ] );
                    $data[] = [
                        $row[ 'batch_no' ],
                        $row[ 'assigned_for' ],
                        $row[ 'assigned_to' ],
                        $row[ 'expected_checkin' ],
                        $row[ 'note' ],
                        'fail',
                        $e->getMessage()
                    ];
                    Log::info( 'accessory bulk Checkout error: '. $e->getMessage() );
                    continue;
                }

                try {
                    $target_checkout_to = null;
                    $target_assigned = null;
                    $target_location_id = null;

                    $target_audience = trim( strtolower( $row[ 'assigned_for' ] ) );
                    $audience = null;

                    /* prepare the data for accessory_users table */
                    $attach_data[ 'expected_checkin' ] = ( $row[ 'expected_checkin' ] && gettype( $row[ 'expected_checkin' ] ) != 'string' ) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject( $row[ 'expected_checkin' ] )->format( 'Y-m-d' ) : null;
                    $attach_data[ 'user_id' ] = Auth::user()->id;

                    switch( $target_audience ) {
                        case 'user':
                        $get_audience = User::where( 'username', 'like', $row[ 'assigned_to' ] )->where( 'company_id', $accessory->company_id )->get();

                        if ( ! $get_audience->count() ) {
                            throw new \Exception( trans( 'content.accessory_fields.desire_user_not_found_for_that_company', [ 'user' => $row[ 'assigned_to' ] ] ) );
                        }

                        $audience = $get_audience->first();

                        if ( $audience->checkoutBasicClearance() ) {
                            throw new \Exception( trans( 'content.accessory_fields.desire_user_not_active_status' ).' '.$row[ 'assigned_to' ] );
                        }

                        if ( $audience->checkLastWorkingDate() ) {
                            throw new \Exception( trans( 'content.accessory_fields.desire_user_left_the_job' ).' '.$row[ 'assigned_to' ] );
                        }

                        $target_checkout_to = 1;
                        $target_assigned = $audience->id;
                        $target_location_id = $audience->location_id;

                        $attach_data[ 'assigned_to' ] = $target_assigned;
                        $attach_data[ 'assigned_for' ] = $target_checkout_to;
                        break;

                        case 'device':
                        $get_audience = Device::where( 'asset_tag', 'like', $row[ 'assigned_to' ] )->where( 'company_id', $accessory->company_id )->get();

                        if ( ! $get_audience->count() ) {
                            throw new \Exception( trans( 'content.accessory_fields.desired_device_not_found_for_company', [ 'device' => $row[ 'assigned_to' ] ] ) );
                        }

                        $audience = $get_audience->first();

                        if ( $audience->status->sold == 1 ) {
                            throw new \Exception( trans( 'content.accessory_fields.Device_is_sold' ).' '.$row[ 'assigned_to' ] );
                        }

                        if ( $audience->status->stolen_item == 1 ) {
                            throw new \Exception( trans( 'content.accessory_fields.Device_is_lost' ).' '.$row[ 'assigned_to' ] );
                        }

                        $target_checkout_to = 3;
                        $target_assigned = $audience->id;
                        $target_location_id = $audience->rtd_location_id;

                        $attach_data[ 'assigned_to' ] = $target_assigned;
                        $attach_data[ 'assigned_for' ] = $target_checkout_to;
                        break;

                        case 'place':
                        $get_audience = Place::where( 'place', 'like', $row[ 'assigned_to' ] )->where( 'company_id', $accessory->company_id )->get();

                        if ( ! $get_audience->count() ) {
                            throw new \Exception( trans( 'content.accessory_fields.Desired_Place_not_found' ).' '.$row[ 'assigned_to' ] );
                        }

                        $audience = $get_audience->first();

                        $target_checkout_to = 2;
                        $target_assigned = $audience->id;
                        $target_location_id = $audience->location_id;

                        $attach_data[ 'assigned_to' ] = $target_assigned;
                        $attach_data[ 'assigned_for' ] = $target_checkout_to;
                        break;
                    }

                    /* update log */
                    $log = new Actionlog();
                    $log->asset_type = 'accessory';
                    $log->checkedout_to = $target_assigned;
                    $log->location_id = $target_location_id;
                    $log->assigned_to_type = $target_checkout_to;
                    $log->assigned_for = $target_checkout_to;
                    $log->accessory_id = $accessory->id;
                    $log->note = $row[ 'note' ];
                    $log->user_id = Auth::user()->id;
                    $log->action_type = 'checkout';
                    $log->save();
                    $attach_data[ 'device_id' ] = $log->id;
                    if ( $target_checkout_to == 3 ) {
                        $accessory->devices()->attach( $accessory->id, $attach_data );
                    } elseif ( $target_checkout_to == 2 ) {
                        $accessory->places()->attach( $accessory->id, $attach_data );
                    } elseif ( $target_checkout_to == 1 ) {
                        $accessory->users()->attach( $accessory->id, $attach_data );
                    }
                    $accessory->touch();
                    // if ( !empty( $accessory->id ) ) {
                    //     $accDetails = Accessory::where( 'id', '=', $accessory->id )->select( 'id', 'category_id' )->first();
                    //     $this->notifythresholdAlert( $accDetails->id, $accDetails->category_id );
                    // }
                } catch( \Exception $e ) {
                    $fail++;
                    $fail_msgs[] = trans( 'content.accessory_fields.batch_no_invalid_values', [ 'batch_no' =>  $row[ 'batch_no' ] ] );
                    $data[] = [
                        $row[ 'batch_no' ],
                        $row[ 'assigned_for' ],
                        $row[ 'assigned_to' ],
                        $row[ 'expected_checkin' ],
                        $row[ 'note' ],
                        'fail',
                        $e->getMessage()
                    ];
                    Log::info( 'accessory bulk Checkout error: ' .$e->getMessage() );
                    continue;
                }

                $data[] = [
                    $row[ 'batch_no' ],
                    $row[ 'assigned_for' ],
                    $row[ 'assigned_to' ],
                    $row[ 'expected_checkin' ],
                    $row[ 'note' ],
                    'success',
                    ''
                ];

                $success++;

            }
            $return[ 'fail' ] = $fail;
            $return[ 'success' ] = $success;
            $return[ 'fail_msgs' ] = $fail_msgs;
            $this->data = $return;
        } catch( \Exception $e ) {
            Log::error( 'accessory bulk Checkout error: ' . $e->getMessage() );
        }

        $file_name = $this->request->file( 'import_file' );
        $given_file_original_name = preg_replace( '@[^0-9a-z\.]+@i', '', $file_name->getClientOriginalName() );
        if ( !empty( $data ) ) {
            foreach ( $data as &$row ) {
                if ( isset( $row[ 3 ] ) && gettype( $row[ 3 ] ) != 'string' && $row[ 3 ] !== null ) {
                    $row[ 3 ] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject( $row[ 3 ] )->format( 'n/j/Y' );
                }
            }
            unset( $row );
        }
        $defaultKkeys = array( 'Batch No', 'Assigned For', 'Assigned To', 'Expected Checkin', 'Note' );
        $succ_fail = array( 'Success/Fail', 'Message' );
        $keys = array_merge( $defaultKkeys, $succ_fail );
        $name = 'AccessoryBulkCheckoutFormat_'.date( 'dmYHis' ).'.xlsx';
        $doc_path = Excel::store( new AccessoryImportStore( $data, $keys ), $name, 'bulk_documents' );

        $log = new BulkActions();

        $log->action_type = 1;
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
        $required_keys = [ 'batch_no', 'assigned_for', 'assigned_to', 'expected_checkin', 'note' ];

    }

    public function notifythresholdAlert( $acc_id, $category_id ) {
        $alertnotify = [];
        $alertmail = [];
        if ( Settings::first()->alerts_enabled == 1 ) {
            $alertnotify = CommonHelper::getGlobalAlertEmail();
        }
        //Get Consumable Details By Id
        $ThresholdSettings = ThresholdSettings::first();
        $accDetails = Accessory::where( 'id', $acc_id )->first();
        $accThreshold = $accDetails->accessory_thresholds;
        $totAccThreshold = $accDetails->qty;
        $total_chkout_acc = Accessory::getCheckoutAccTotalById( $acc_id )[ 0 ]->total_checkouts;
        $availableSingleAcc = $totAccThreshold - $total_chkout_acc;

        //Get Details By Category
        $availableAccessory = $thresouldCatValue = 0;
        if ( !empty( $category_id ) ) {
            $thresouldDtl = Threshold::where( 'cat_id', $category_id )->first();
            $totalAccData = Accessory::getCatAccessoriesTotal( $category_id );
            $totalScrapAccData = Accessory::getCatAccessoriesTotalScrap( $category_id );
            $totalCheckoutData = Accessory::getCheckoutAccessoriesTotalByCat( $category_id );
            $total_accessories = !empty( $totalAccData ) ? $totalAccData[ 0 ]->total_accessories : 0;
            $total_scrap_qty_accessories = !empty( $totalScrapAccData ) ? $totalScrapAccData[ 0 ]->total_scrap_qty_accessories : 0;
            $total_chkout_accessories = !empty( $totalCheckoutData ) ? $totalCheckoutData[ 0 ]->total_checkouts : 0;
            $availableAccessory = $total_accessories - ( $total_chkout_accessories + $total_scrap_qty_accessories );
            $thresouldCatValue = !empty( $thresouldDtl ) ? $thresouldDtl->threshold : 0;
        }
        $thresholdUserEmails = ThresholdAlertSettings::where( 'threshold_alert_settings.asset_id', $acc_id )
        ->where( 'threshold_alert_settings.asset_type', 2 )
        ->join( 'users', 'users.id', '=', 'threshold_alert_settings.user_id' )
        ->pluck( 'users.email' )
        ->toArray();
        $alertmail = [];
        if ( $ThresholdSettings->threshold_enabled && $ThresholdSettings->alerts_enabled ) {
            if ( $ThresholdSettings->send_alerts == 1 ) {
                $alertmail[] = $ThresholdSettings->email;
            }
            if ( !empty( $thresholdUserEmails ) ) {
                $alertmail = array_merge( $alertmail, $thresholdUserEmails );
            }
        }

        if ( config( 'mail.service_enabled' ) && !empty( $alertmail ) && ( $accThreshold > 0 ) && ( $availableSingleAcc <= $accThreshold ) ) {
            Mail::to( $alertmail )->cc( $alertnotify )->send( new AccessoryThreshouldNotification( $accDetails, $accThreshold, $availableSingleAcc ) );
        } elseif ( config( 'mail.service_enabled' ) && !empty( $alertmail ) && ( $thresouldCatValue >= 0 ) && ( $availableAccessory <= $thresouldCatValue ) ) {
            $catDetail = Category::find( $category_id );
            Mail::to( $alertmail )->cc( $alertnotify )->send( new ThreshouldNotification( $catDetail, $thresouldCatValue, $availableAccessory ) );
            $thresouldDtl->notify_count = $thresouldDtl->notify_count + 1 ;
            $thresouldDtl->last_notified_date = date( 'Y-m-d' );
            $thresouldDtl->save();
        }
    }

    public function onError( \Throwable $e ) {
        // Handle the exception how you'd like.
    }
}
