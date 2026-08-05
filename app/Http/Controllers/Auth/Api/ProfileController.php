<?php

namespace App\Http\Controllers\Auth\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse as TraitsApiResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use App\Models\Location;
use Auth;
use Hash;
use Illuminate\Support\Str;
use Validator;
use Storage;
use Image;
use stdClass;
use Log;

class ProfileController extends Controller {
    use TraitsApiResponse;
    /* app common data for users*/
    public function getUserCommonData() {
        $compObj = Company::select("id", "name as text");
        if(!Auth::user()->isSuperUser() && !Settings::getSettings()->full_multiple_companies_support) {
            $compObj->where("id", "=", Auth::user()->company_id);
        }
        $vd = new stdClass;
        $locations = Location::select('id', 'name as text')->orderBy('name')->get()->toArray();
        $companies = $compObj->get()->toArray();

        $return['status'] = "success";
        $return['msg'] = '';
        $return['companies'] = $companies;
        $return['locations'] = $locations;
        return response()->json($return);
    }

    public function editProfile(Request $request) {

        $user = User::find(Auth::user()->id);

        $data = $request->only('first_name', 'last_name', 'location_id', 'website', 'avatar', 'gravatar');

        $rules = [
            'first_name'        => 'sometimes|required|max:100',
            'last_name'         => 'sometimes|required|max:100',
            'location_id'       => 'sometimes|required|exists:locations,id',
            'website'           => 'sometimes|max:100',
            'gravatar'          => 'sometimes|max:100',
            'avatar'            => 'sometimes|image',
        ];

        $messages = [
            'first_name.required'   => 'Please provide first name.',
            'last_name.required'    => 'Please provide last name.',
            'location_id.exists'    => 'Provided location not found.',
        ];
        
        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                "status"    => 'fail',
                "msg"       => 'Error in validation',
                "errors"    => $validator->messages()
            ]);
        }
        else {
            if($request->avatar) {
                $uploaded_img = $request->avatar;
                $profileImage = Str::random(12) . Str::random(12) . '.' . $uploaded_img->getClientOriginalExtension();
                $path = public_path("uploads/avatar/" . $profileImage);
                Image::make($uploaded_img->getRealPath())->resize(300, null, function($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->save($path);
                $user->avatar  = $profileImage;
                unset ($data['avatar']) ;
            }

            if( isset( $request->avatar_delete )) {
                if( $request->avatar_delete == '1') $user->avatar = '';
            }

            $user->fill($data);

            if ($user->save()) {
                $data = $user->only("first_name", "last_name", "username", "email", "phone", "jobtitle", "employee_num", "country", "gravatar", "location_id", "company_id", "manager_id", "department_id", "access_token", "website");
                $data["company_name"] = $user->companyProp("name");
                $data["location_name"] = $user->locationProp("name");
                $data["manager_name"] = $user->managerProp("username");
                $data["profile_img"] = $user->getProfileImg();
                $data["user_permissions"] = $user->getAllPermission();
                $data["local_profile_img"] = $user->getLocalProfile();
                return response()->json([
                    "status"    => 'success',
                    "msg"       => 'Profile has updated successfully',
                    "data" => $data
                ]);
            }
        }

    }

    public function changePassword(Request $request) {

        $data               = $request->only("current_password", "new_password");
        $current_password   = trim($data["current_password"]);
        $new_password       = trim($data["new_password"]);

        if (!$new_password  || !$current_password) {
            return response()->json([
                "status"    => 'fail',
                "msg"       => 'Invalid data provided !!',
            ]);
        }

        $user = User::find(Auth::user()->id);
        if (!Hash::check($current_password, $user->password)) {
            return response()->json([
                "status"    => 'fail',
                "msg"       => 'Current password is invalid !!',
            ]);
        }
        $user->setPassword($new_password);

        if ($user->save()) {
            return response()->json([
                "status"    => 'success',
                "msg"       => 'Password has updated successfully',
            ]);
        }

    }

    //ajax get user profile

    public function ajaxGetUserProfile(Request $request, $id) {

        $obj = "";
        try {
            $obj = User::findOrFail($id);
            if ($obj->activated == 0) {
                return $this->fail(null, 'User is  Inactive.');
            }
            $company = Company::findOrFail($obj->company_id);
            //$location = Location::findOrFail($obj->location_id);

            if( empty($obj) ) {
                return $this->fail(null, 'Unable to get the User');
            }
            $role = "";
            foreach($obj->roles->pluck('name') as $rolename) {
                $role .= $rolename;
            }

            $data = $obj->only('id','first_name','last_name','email','username','employee_num','website','avatar');
            $data["seat_no"] = (!empty($obj->userDetails) && isset($obj->userDetails->seat_no)) ? $obj->userDetails->seat_no : null;
            $data['company'] = $company->name;
            $data['location'] = $obj->locationProp("name");
            $data['role'] = $role;
            return $this->success([$data], null);
        }
        catch(\Exception $e) {
            return $this->fail(null,'Unable to get the User');
        }
    }

    // ajax to update user profile
    public function updateProfile(Request $request, $id) {
        $return = [
            'status' => 'failure',
            'msg' => 'Invalid Data'
        ];
        $obj = "";
        try {
            $obj = User::find($id);

            if( empty($obj) ) {
                return response()->json($return);
            }
        }
        catch(\Exception $e) {
            return response()->json($return);
        }
        try {
            $data = $request->only('first_name', 'last_name', 'website','location_id');

            $rules = [
                'first_name' => 'nullable|sometimes|string|min:3|max:15',
                'last_name' => 'nullable|sometimes|string|min:3|max:15',
                'website' => 'nullable|sometimes|url',
                'location_id' => 'nullable|sometimes|integer|min:1|exists:locations,id'
            ];

            $messages = [];
            $validator = Validator::make($data, $rules, $messages);

            if($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }

            $app_acc = Auth::user();
            $app_acc->fill($data);
            $app_acc->save();
            $return["data"] = $app_acc->only("id", "first_name", "last_name", "username", "email","location_id", "company_id", "website");
            $return["status"] = "success";
            $return["msg"] = "Profile updated successfully";
            $return["account"] = $app_acc->appSessionData();
            return response()->json($return);
        }
        catch(\Exception $e) {
            Log::error($e->getMessage());
            return response()->json($return);
        }
    }

}
