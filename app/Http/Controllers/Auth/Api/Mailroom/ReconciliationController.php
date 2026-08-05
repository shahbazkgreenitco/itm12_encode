<?php

namespace App\Http\Controllers\Auth\Api\Mailroom;

use App\Http\Controllers\Controller;
use App\Models\Mailroom\Parcel;
use App\Models\Mailroom\ParcelReconciliation;
use App\Models\Mailroom\Status;
use Illuminate\Validation\Rule;
use App\Helpers\Common as CommonHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Exception;

class ReconciliationController extends Controller
{
    public function parcelReconciliation(Request $request)
    {
        $return = [
            'status' => 'fail',
            'msg' => 'Unable to reconcile parcels.',
        ];

        $data = $request->all();

        if (isset($data['parcel_id'][0]) && is_string($data['parcel_id'][0])) {
            $decoded = json_decode($data['parcel_id'][0], true);
            if (is_array($decoded)) {
                $data['parcel_id'] = $decoded;
            }
        }


        $rules = [
            'parcel_id' => 'required|array|min:1',
            'parcel_id.*' => ['integer', Rule::exists('mailroom_parcels', 'id')],
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],
        ];

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $firstError = array_shift($errors);
            $return['msg'] = $firstError[0] ?? 'Invalid input data.';
            Log::info('parcelReconciliation validation failed', [$return]);
            return response()->json($return, 422);
        }

        try {
            DB::beginTransaction();

            $userId = $data['user_id'];
            $currentTime = Carbon::now();
            foreach ($data['parcel_id'] as $parcelId) {
                $reconciliation = new ParcelReconciliation();
                $reconciliation->parcel_id = $parcelId;
                $reconciliation->reconciled_by = $userId;
                $reconciliation->reconciled_at = $currentTime;
                $reconciliation->reconcilled_date_time = $currentTime;
                $reconciliation->save();
            }

            $status = Status::where('status_name', 'Pending')->value('id');
            if (!$status) {
                $status = Status::orderBy('id', 'asc')->value('id');
            }

            $parcels = Parcel::whereIn('id', $data['parcel_id'])->get();

            foreach ($parcels as $parcel) {
                $oldStatus = $parcel->status_id;
                $parcel->status_id = $status;
                $parcel->save();

                CommonHelper::parcelHistory([
                    'parcel_id' => $parcel->id,
                    'updated_by' => $userId,
                    'old_status' => $oldStatus,
                    'action_type' => 6,
                ], $parcel);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'msg' => 'Parcels reconciled successfully and history recorded.',
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Parcel reconciliation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'fail',
                'msg' => 'Something went wrong while reconciling parcels.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
