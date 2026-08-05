<?php

namespace App\Http\Controllers\Auth\Api;

use Log;
use Auth;
use Validator;
use App\Models\UserDetails;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserDetailController extends Controller
{
    public function updateUserDetails(Request $request) {
        $return =['status' => 'fail', 'msg' =>'Something went wrong'];
        try {
            $data = $request->only('user_id','app_version','platform_type');
            $rules = [
                'user_id' => 'required|integer',
                'app_version' => 'required',
                'platform_type' => 'required|in:1,2',
            ];

            $messages = [
                'user_id.required' => 'User id field is required',
                'app_version.required' => 'App version field is required',
                'platform_type.required' => 'Platform type field is required',
            ];
            $validate = Validator::make($data, $rules, $messages);

            if ($validate->fails()) {
                $v = $validate->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }

            $return =['status' => 'Success', 'msg' =>'App version updated successfully'];
            $version = UserDetails::where('user_id',$request->user_id)->first();
            if(empty($version)){
                $version = new UserDetails();
                $version->user_id = $request->user_id;
                $return =['status' => 'Success', 'msg' =>'App version added successfully'];
            }
            $version->app_version = $request->app_version;
            $version->platform_type = $request->platform_type;
            $version->save();           
        } Catch(\Exception $e) {
            Log::error("updateUserDetails API: ", $e->getMessage());
        }
        return ($return);
    }   
}
