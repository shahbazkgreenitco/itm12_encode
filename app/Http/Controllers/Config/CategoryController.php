<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomFieldset;
use App\Models\Category;
use App\Models\Component;
use App\Models\Threshold;
use App\Models\Setting;
use App\Models\Accessory;
use App\Models\License;
use App\Models\Consumable;
use App\Models\Device;
use App\Models\Model;
use App\Models\Procurement\AccountType;
use App\Models\Department;
use App\Helpers\Common as CommonHelper;
use Validator;
use Auth;
use DB;
use Storage;
// use Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver; // v3
use Log;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Categories;
use App\Models\User;

class CategoryController extends Controller
{
    public function getIndex()
    {
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if (!Auth::user()->hasPermissionTo('CategoryRead')) {
            return redirect('dashboard')->with("msg", $return);
        }
        $vd = [];
        $vd["account_types"] = AccountType::select('id', 'name as text')->orderBy('name')->get();
        return view("categories.index")->with("category", new Category)->with("vd", $vd)->with("CustomFieldset", new CustomFieldset);
    }

    /* ajax categories */
    public function ajaxCategories(Request $request)
    {
        $req = $request->all();
        $return = array(
            // "draw" => date('is')
        );

        $fields = array(
            'a.name' => 'c.name',
            'a.id' => 'c.id',
            'a.depart_name' => 'd.name',
            'a.category_type' => 'c.category_type',
            'a.account_type' => 'pac.name',
            'a.threshold_count' => 'threshold_count',
            'a.require_acceptance' => 'c.require_acceptance',
            'a.eula' => 'eula',
            'a.checkin_email' => 'c.checkin_email',
            'a.checkout_email_accept' => 'c.checkout_email_accept',
            'a.last_updated_at' => 'c.updated_at',
        );
        $return['recordsTotal'] = Category::count();
        $return['recordsFiltered'] = $return['recordsTotal'];
        $get_cat = DB::table('categories as c');
        $get_cat->leftJoin('thresholds as t', 'c.id', '=', 't.cat_id');
        $get_cat->leftJoin('procure_account_types as pac', 'c.account_type_id', '=', 'pac.id');
        $get_cat->leftJoin('departments as d', 'c.department_id', '=', 'd.id');
        $get_cat->select('c.id', 'c.name', 'c.checkin_email', 'c.checkout_email_accept', 'c.category_type', 'c.require_acceptance', 'pac.name as account_type', 'c.image as image_thumbnail', 'd.name as depart_name', 't.threshold as threshold_count');
        // $get_cat->addSelect(DB::raw('case when t.alerts_enabled = 1 then t.threshold else 0 end as threshold_count'));
        $get_cat->addSelect(DB::raw('case when c.use_default_eula = 1 then 1 when c.eula_text <> null and c.eula_text <> "" then 1 else 0 end as eula'));
        $mysqlDateTimeFormat = CommonHelper::mysqlDateTimeFormat('datetime', 'display');
        $get_cat->addSelect(DB::raw("DATE_FORMAT(c.updated_at, '{$mysqlDateTimeFormat}') as last_updated_at"));
        // if(Setting::getSettings()->department_config == 1) {
        //     $department_id = Auth::user()->asset_departments_id
        //     ? explode(',', Auth::user()->asset_departments_id)
        //     : [];
        //     $get_cat->whereIn('d.id',$department_id);
        // }
        $settings = Setting::getSettings();

        if ($settings && $settings->department_config == 1) {
            $department_id = Auth::user()->asset_departments_id
                ? explode(',', Auth::user()->asset_departments_id)
                : [];

            $get_cat->whereIn('d.id', $department_id);
        }
        $get_cat->whereNull('c.deleted_at');
        $return['recordsTotal'] = $get_cat->count();
        $return['recordsFiltered'] = $return['recordsTotal'];
        if (isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"])) {

            $whereStr = sprintf('(c.name like "%%%1$s%%" or c.category_type like "%%%1$s%%" or pac.name like "%%%1$s%%" or (case when t.alerts_enabled = 1 then t.threshold else 0 end) like "%%%1$s%%" or DATE_FORMAT(c.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
            $get_cat->whereRaw($whereStr);
            $return['recordsFiltered'] = $get_cat->count();
        }

        if (isset($req['sorted_column_name']) && isset($fields[$req['sorted_column_name']]) && in_array($req['sorted_direction'], ['asc', 'desc'])) {
            $get_cat->orderBy($fields[$req['sorted_column_name']], $req['sorted_direction']);
        }

        $skip = 0;
        $take = 10;
        if (isset($req["start"]) && isset($req["length"])) {
            $skip = (int) $req["start"];
            $take = (int) $req["length"];
        }
        $get_cat->skip($skip);
        $get_cat->take($take);

        $data = $get_cat->get();
        $return['data'] = array();
        foreach ($data as $d) {
            $d->category_type = strtoupper($d->category_type);
            $return['data'][] = array('a' => $d);
        }

        return response()->json($return);
    }

    /* ajax add category */
    public function ajaxAddCategory(Request $request)
    {
        $return = [
            'status' => 'failure',
            'msg' => 'Unable to add the category'
        ];
        if (! Auth::user()->hasPermissionTo('CategoryAdd')) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }

        $data = $request->only('name', 'category_type', 'eula_text', 'use_default_eula', 'require_acceptance', 'checkin_email', 'checkout_email_accept', 'threshold', 'account_type_id', 'service_cycle_period', 'fieldset_id', 'image', 'image_thumbnail', 'departments_id');
        $rules = [
            'name'  => 'required|clean_text_only|unique:categories,name,NULL,id,category_type,' . $data['category_type'],
            'category_type'   => 'required',
            'threshold' => 'nullable|numeric|min:0|max:50',
            'alerts_enabled' => 'nullable|max:1',
            'account_type_id' => 'nullable|exists:procure_account_types,id',
            'eula_text' => 'sometimes|clean_text_only',
            'service_cycle_period' => 'nullable|numeric',
            'category_image'       => 'sometimes|image'
        ];
        $messages = [
            'name.required' => 'Please provide Category Name.',
            'city.required' => 'Please provide threshold value .',
            'category_type.required' => 'Please select category type .',
        ];
        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
        } else {
            $objCategory = new Category;
            $data = $request->all();
            $objCategory->name = $data["name"];
            $objCategory->category_type = $data["category_type"];
            $objCategory->eula_text = $data["eula_text"];
            $objCategory->use_default_eula = (int) $request->use_default_eula;
            $objCategory->require_acceptance = (int) $request->require_acceptance;
            $objCategory->checkin_email = (int) $request->checkin_email;
            $objCategory->checkout_email_accept = (int) $request->checkout_email_accept;
            $objCategory->account_type_id = (int) $request->account_type_id;
            $objCategory->user_id = Auth::user()->id;
            $objCategory->created_by = Auth::user()->id;
            $objCategory->service_cycle_period = $data["service_cycle_period"] ? $data["service_cycle_period"] : null;
            $objCategory->department_id =  !empty($data["departments_id"]) ? $data["departments_id"] : null;
            $objCategory->fieldset_id = isset($data["fieldset_id"]) ? $data["fieldset_id"] : null;

            if (isset($data['category_image'])) {
                $attachment = $data['category_image'];
                unset($data['category_image']);
                $ModelImageName = uniqid('', true);
                $objCategory->image =  $ModelImageName . '.' . $request->file('category_image')->getClientOriginalExtension();
                $stat = Storage::disk('category')->put(
                    $objCategory->image,
                    file_get_contents($request->file('category_image')->getRealPath())
                );

                $uploaded_img = $request->file('category_image');
                $resizedModelImage = $ModelImageName . '_thumbnail.' . $uploaded_img->getClientOriginalExtension();
                $path = public_path("uploads/category/" . $resizedModelImage);
                // Image::make(public_path("uploads/category/" . $objCategory->image ))->resize(100, null, function($constraint) {
                //     $constraint->aspectRatio();
                //     $constraint->upsize();
                // })->save($path);
                $manager = new ImageManager(new Driver());
                $image = $manager->read($uploaded_img->getRealPath());
                $image->scale(width: 300);
                $image->save($path);

                $objCategory->image_thumbnail = $resizedModelImage;
            }
            if (isset($request->fieldset_id)) {
                switch ($request->category_type) {
                    case 'asset':
                        $fieldset_id = Setting::select('custom_fieldset_id')->where('custom_fieldset_id', $request->fieldset_id)->first();
                        break;
                    case 'accessory':
                        $fieldset_id = Setting::select('accessories_custom_fieldset_id')->where('accessories_custom_fieldset_id', $request->fieldset_id)->first();
                        break;
                    case 'license':
                        $fieldset_id = Setting::select('licence_custom_fieldset_id')->where('licence_custom_fieldset_id', $request->fieldset_id)->first();
                        break;
                    case 'component':
                        $fieldset_id = Setting::select('component_custom_fieldset_id')->where('component_custom_fieldset_id', $request->fieldset_id)->first();
                        break;
                    case 'consumable':
                        $fieldset_id = Setting::select('consumable_custom_fieldset_id')->where('consumable_custom_fieldset_id', $request->fieldset_id)->first();
                        break;
                    default:
                        $fieldset_id = [];
                        break;
                }
            }
            if (!empty($fieldset_id)) {
                $return["msg"] = trans('content.licenses_fields.custom_field_is_already_used_for_global_setting');
                return response()->json($return);
            }


            if ($objCategory->save()) {

                // task 1: To save the threshold value for new category
                $threshold = Threshold::firstOrCreate(array('cat_id' => $objCategory->id));
                $threshold->threshold = $request->threshold ? ((int) $request->threshold) : 0;
                $threshold->alerts_enabled = (int) $request->alerts_enabled;
                $threshold->save();

                $return["msg"] = "Category has been added successfully";
                $return["status"] = "success";
            }
        }

        return response()->json($return);
    }

    /* to get category in ajax call */
    public function ajaxGetCategory(Request $request, $id)
    {
        $return = [
            'status' => 'failure',
            'msg' => 'Unable to get the category'
        ];

        $objCategory = "";
        try {
            $objCategory = Category::find($id);

            if (empty($objCategory)) {
                return response()->json($return);
            }

            $return['data'] = $objCategory->only('id', 'name', 'category_type', 'eula_text', 'use_default_eula', 'require_acceptance', 'checkin_email', 'checkout_email_accept', 'account_type_id', 'service_cycle_period', 'fieldset_id', 'image', 'image_thumbnail', 'department_id');

            try {
                $thresold = Threshold::where("cat_id", "=", $objCategory->id)->firstOrFail();
                $return['data']['thresold'] = $thresold->threshold;
                $return['data']['alerts_enabled'] = $thresold->alerts_enabled;
                if ($return["data"]["department_id"] != '' && $return["data"]["department_id"] != null) {
                    $assetDepartments = Department::where("id", $return["data"]["department_id"])->select("id", "name as text")->get();
                    $return["dropdown"]["assetDepartments"] = (!empty($assetDepartments)) ? $assetDepartments->toArray() : '';
                }
            } catch (\Exception $e) {
                $return['data']['thresold'] = 0;
                $return['data']['alerts_enabled'] = null;
            }

            $return['msg'] = null;
            $return['status'] = 'success';
            return response()->json($return);
        } catch (\Exception $e) {
            return response()->json($return);
        }
    }

    /* to edit category in ajax call */
    public function ajaxEditCategory(Request $request, $id)
    {
        $return = [
            'status' => 'failure',
            'msg' => 'Unable to edit the category'
        ];
        if (! Auth::user()->hasPermissionTo('CategoryEdit')) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }
        $objCategory = "";
        try {
            $objCategory = Category::find($id);

            if (empty($objCategory)) {
                return response()->json($return);
            }

            $data = $request->only('name', 'category_type', 'eula_text', 'use_default_eula', 'require_acceptance', 'checkin_email', 'checkout_email_accept', 'threshold', 'account_type_id', 'service_cycle_period', 'fieldset_id', 'image', 'image_thumbnail', 'delete_img', 'departments_id');
            $rules = [
                'name' => 'required|clean_text_only|unique:categories,name,' . $id . 'NULL,id,category_type,' . $data['category_type'],
                'category_type'   => 'required',
                'threshold' => 'nullable|numeric|min:0|max:50',
                'alerts_enabled' => 'nullable|max:1',
                'account_type_id' => 'nullable|exists:procure_account_types,id',
                'eula_text' => 'sometimes',
                'service_cycle_period' => 'nullable|numeric',
                'fieldset_id' => 'nullable|numeric',
                'category_image'       => 'sometimes|image',
                'delete_img'        => 'sometimes|nullable|integer|min:0|max:1',
            ];
            $messages = [
                'name.required' => 'Please provide Category Name.',
                'city.required' => 'Please provide threshold value .',
                'category_type.required' => 'Please select category type .',
            ];
            $validator = Validator::make($data, $rules, $messages);

            if ($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }
            $data = $request->all();
            $objCategory = Category::findOrFail($id);
            if ($objCategory->category_type != $request->category_type) {
                $messages = [];

                if (Consumable::where('category_id', $id)->exists() && $request->category_type != "consumable") {
                    $messages[] = 'Consumable';
                }
                if (Accessory::where('category_id', $id)->exists() && $request->category_type != "accessory") {
                    $messages[] = 'Accessory';
                }
                if (License::where('category_id', $id)->exists() && $request->category_type != "license") {
                    $messages[] = 'License';
                }
                if (Component::where('category_id', $id)->exists() && $request->category_type != "component") {
                    $messages[] = 'Component';
                }
                if (
                    Device::join('models', 'assets.model_id', '=', 'models.id')
                    ->where('models.category_id', $id)->exists() && $request->category_type != "asset"
                ) {
                    $messages[] = 'Device';
                }

                if (count($messages) > 0) {
                    $modelList = implode(', ', $messages);
                    return response()->json(['msg' => "This Category is already used for: $modelList"]);
                }
            }

            $newRecord = $objCategory->replicate();
            $newRecord->setTable('categories_history');
            $newRecord->updated_by = Auth::user()->id;
            $newRecord->category_id = $id;
            unset($data["delete_img"]);

            $objCategory->name = $data["name"];
            $objCategory->category_type = $data["category_type"];
            $objCategory->eula_text = $data["eula_text"];
            $objCategory->use_default_eula = (int) $request->use_default_eula;
            $objCategory->require_acceptance = (int) $request->require_acceptance;
            $objCategory->checkin_email = (int) $request->checkin_email;
            $objCategory->checkout_email_accept = (int) $request->checkout_email_accept;
            $objCategory->account_type_id = (int) $request->account_type_id;
            $objCategory->user_id = Auth::user()->id;
            $objCategory->updated_by = Auth::user()->id;
            $objCategory->service_cycle_period = $data["service_cycle_period"] ? $data["service_cycle_period"] : null;
            $objCategory->department_id =  !empty($data["departments_id"]) ? $data["departments_id"] : null;
            $objCategory->fieldset_id = isset($data["fieldset_id"]) ? $data["fieldset_id"] : null;
            if (isset($data['category_image'])) {
                $attachment = $data['category_image'];
                unset($data['category_image']);
                $ModelImageName = uniqid('', true);
                $objCategory->image =  ($ModelImageName . '.' . $request->file('category_image')->getClientOriginalExtension());
                $stat = Storage::disk('category')->put(
                    $objCategory->image,
                    file_get_contents($request->file('category_image')->getRealPath())
                );
                $uploaded_img = $request->file('category_image');
                $resizedModelImage = $ModelImageName . '_thumbnail.' . $uploaded_img->getClientOriginalExtension();
                $path = public_path("uploads/category/" . $resizedModelImage);
                // Image::make(public_path("uploads/category/" . $objCategory->image))->resize(100, null, function ($constraint) {
                //     $constraint->aspectRatio();
                //     $constraint->upsize();
                // })->save($path);
                $manager = new ImageManager(new Driver());
                $image = $manager->read($uploaded_img->getRealPath());
                $image->scale(width: 300);
                $image->save($path);
                $objCategory->image_thumbnail = $resizedModelImage;
            } elseif ($request->input("delete_img", false)) {
                if ($objCategory->image && Storage::disk('category')->exists($objCategory->image)) {
                    Storage::disk('category')->delete($objCategory->image);
                }
                if ($objCategory->image_thumbnail) {
                    $path = public_path('/uploads/category/' . $objCategory->image_thumbnail);
                    if (file_exists($path)) {
                        unlink($path);
                    }
                }
                $objCategory->image = null;
                $objCategory->image_thumbnail = null;
            }

            if (isset($request->fieldset_id)) {
                switch ($request->category_type) {
                    case 'asset':
                        $fieldset_id = Setting::select('custom_fieldset_id')->where('custom_fieldset_id', $request->fieldset_id)->first();
                        break;
                    case 'accessory':
                        $fieldset_id = Setting::select('accessories_custom_fieldset_id')->where('accessories_custom_fieldset_id', $request->fieldset_id)->first();
                        break;
                    case 'license':
                        $fieldset_id = Setting::select('licence_custom_fieldset_id')->where('licence_custom_fieldset_id', $request->fieldset_id)->first();
                        break;
                    case 'component':
                        $fieldset_id = Setting::select('component_custom_fieldset_id')->where('component_custom_fieldset_id', $request->fieldset_id)->first();
                        break;
                    case 'consumable':
                        $fieldset_id = Setting::select('consumable_custom_fieldset_id')->where('consumable_custom_fieldset_id', $request->fieldset_id)->first();
                        break;
                    default:
                        $fieldset_id = [];
                        break;
                }
            }
            if (!empty($fieldset_id)) {
                $return["msg"] = trans('content.licenses_fields.custom_field_is_already_used_for_global_setting');
                return response()->json($return);
            }

            if ($objCategory->save()) {
                $newRecord->save();
                // task 1: To save the threshold value for category
                $threshold = Threshold::firstOrCreate(array('cat_id' => $objCategory->id));
                $threshold->threshold = $request->threshold ? ((int) $request->threshold) : 0;
                $threshold->alerts_enabled = (int) $request->alerts_enabled;
                $threshold->save();

                $return["msg"] = "Category has been updated successfully";
                $return["status"] = "success";
            }
        } catch (\Exception $e) {
            Log::error("ajaxEditCategory: uid:" . Auth::user()->id . " - " . $e->getMessage());
            return response()->json($return);
        }

        return response()->json($return);
    }

    public function addCategory(Request $request)
    {
        $category = new Category;

        if ($request->isMethod('post')) {

            $rules = [
                'name'  => 'required|unique:categories,name,NULL,id,category_type,' . $data['category_type'],
                'category_type'   => 'required',
                'threshold' => 'nullable|numeric|min:0|max:50',
                'eula_text' => 'sometimes'
            ];
            $messages = [
                'name.required' => 'Please provide Category Name.',
                'city.required' => 'Please provide threshold value .',
                'category_type.required' => 'Please select category type .',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator->messages());
                // return array('status'=>'Error','errors'=>$validator->errors()->getMessages());
            } else {

                $category->name                    = $request->name;
                $category->user_id                = Auth::user()->id;
                $category->category_type        = $request->category_type;
                $category->eula_text            = $request->eula_text;
                $category->use_default_eula     = (int) $request->use_default_eula;
                $category->require_acceptance   = (int) $request->require_acceptance;
                $category->checkin_email        = (int) $request->checkin_email;

                if ($category->save()) {

                    // task 1: To save the threshold value for new category
                    $threshold = Threshold::firstOrCreate(array('cat_id' => $category->id));
                    $threshold->threshold = $request->threshold ? ((int) $request->threshold) : 0;
                    $threshold->alerts_enabled = (int) $request->alerts_enabled;
                    $threshold->save();

                    $return = array(
                        "status" => "success",
                        "msg" => "Category has been added successfully"
                    );

                    $request->session()->flash("msg", $return);
                }
            }
        }
        return view("categories.add");
    }

    public function editCategory($id, Request $request)
    {
        $category = Category::find($id);

        if (empty($category)) {
            return redirect()->action("CategoryController@getIndex")->with("msg", array(
                "status" => "warning",
                "msg" => "No Category found for given data"
            ));
        }

        if ($request->isMethod('post')) {

            $rules = [
                'name'  => 'required|unique:categories,name,' . $id . 'NULL,id,category_type,' . $data['category_type'],
                'category_type'   => 'required',
                'threshold' => 'nullable|numeric|min:0|max:50',
                'eula_text' => 'sometimes'
            ];
            $messages = [
                'name.required' => 'Please provide Category Name.',
                'city.required' => 'Please provide threshold value .',
                'category_type.required' => 'Please select category type .',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator->messages());
                // return array('status'=>'Error','errors'=>$validator->errors()->getMessages());
            } else {

                $category->name                    = $request->name;
                $category->category_type        = $request->category_type;
                $category->eula_text            = $request->eula_text;
                $category->use_default_eula     = (int) $request->use_default_eula;
                $category->require_acceptance   = (int) $request->require_acceptance;
                $category->checkin_email        = (int) $request->checkin_email;
                $category->fieldset_id        = (int) $request->fieldset_id;

                if ($category->save()) {
                    // task 1: To save the threshold value for new category
                    $threshold = Threshold::firstOrCreate(array('cat_id' => $category->id));
                    $threshold->threshold = $request->threshold ? ((int) $request->threshold) : 0;
                    $threshold->alerts_enabled = (int) $request->alerts_enabled;
                    $threshold->save();

                    $return = array(
                        "status" => "success",
                        "msg" => "Category has been updated successfully"
                    );

                    $request->session()->flash("msg", $return);
                }
            }
        }

        return view("categories.edit")->with("category", $category)->with("CustomFieldset", new CustomFieldset);
    }

    public function deleteCategory($id)
    {
        $return = ['status' => 'error', 'msg' => trans('content.user_fields.Permission_denied')];
        if (! Auth::user()->hasPermissionTo('CategoryDelete')) {
            return response()->json($return);
        }
        $splr = Category::where('id', '=', $id)->withCount(['models', 'accessories', 'assets', 'components', 'consumables', 'licenses'])->first();

        if (empty($splr))
            return response()->json(['status' => 'error', 'msg' => 'Some problem in system!!']);

        if ($splr->models_count)
            return response()->json(['status' => 'error', 'msg' => 'Some model exists for this category.Please remove them and try again !!']);
        if ($splr->accessories_count)
            return response()->json(['status' => 'error', 'msg' => 'Some accessories exists for this category.Please remove them and try again !!']);
        if ($splr->assets_count)
            return response()->json(['status' => 'error', 'msg' => 'Some assets exists for this category.Please remove them and try again !!']);
        if ($splr->components_count)
            return response()->json(['status' => 'error', 'msg' => 'Some components exists for this category.Please remove them and try again !!']);
        if ($splr->consumables_count)
            return response()->json(['status' => 'error', 'msg' => 'Some consumables exists for this category.Please remove them and try again !!']);
        if ($splr->licenses_count)
            return response()->json(['status' => 'error', 'msg' => 'Some licenses exists for this category.Please remove them and try again !!']);

        $splr->delete();
        return response()->json(['status' => 'success', 'msg' => 'Category has been deleted successfully!']);
    }

    public function getCategoryByQuery(Request $request)
    {
        $return = array();
        $search = $request->input("term", "") ? $request->input("term", "") : $request->input("search", "");
        $page = $request->input("page", 1);
        $type = $request->input("type", "");
        $skip = (($page * 20) - 20);

        $db = DB::table("categories")->select("id", "name as text");
        $db->whereNull("deleted_at");
        if ($type != null) {
            $db->where("category_type", $type);
        }
        if ($type == 'consumable') {
            if ($request->has('department_id') && $request->has('location_id')) {
                $consUniqueCats = Consumable::where('department_id', $request->department_id)->where('location_id', $request->location_id)->whereNotNull('category_id')->where('qty', '>', 0);
                if (config('app.client') == "ltsct" && $request->has('category_type') && isset($request->category_type)) {
                    $consUniqueCats->where('_itm_consumable_type', 'like', $request->category_type . '%');
                }
                $consUniqueCat = $consUniqueCats->distinct()->pluck('category_id');
                $db->whereIn('id', $consUniqueCat);
            } else if ($request->has('all')) {
                $consUniqueCat = [];
            } else {
                $consUniqueCat = Consumable::whereNotNull('category_id')->where('qty', '>', 0)->distinct()->pluck('category_id');
                $db->whereIn('id', $consUniqueCat);
            }
        }
        if ($request->has('department_id')) {
            $db->where('department_id', $request->department_id);
        }
        if ($search) {
            $db->whereRaw("name like '%" . $search . "%'");
        }
        $count = $db->count();
        $db->skip($skip)->take(20);
        $db->orderBy('name', 'ASC');
        $result = $db->get();

        $return["pagination"] = array("more" => ($count - ($page * 20)) > 0 ? true : false);
        $return["results"] = count($result) ? $result->toArray() : [];
        return response()->json($return);
    }

    public function CategoryExport(Request $request)
    {
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if (! Auth::user()->hasPermissionTo('CategoryDownload')) {
            return redirect('dashboard')->with("msg", $return);
        }
        $mysqlDateFormat = CommonHelper::mysqlDateTimeFormat('datetime', 'excel');
        $query = Category::select('categories.id', 'categories.name', 'd.name as depart_name', 'categories.checkin_email', 'categories.category_type', 'categories.require_acceptance', 'categories.checkout_email_accept', 'pac.name as account_type')
            ->addSelect(DB::raw("DATE_FORMAT(categories.updated_at, '{$mysqlDateFormat}') as updated_at_format"))
            ->addSelect(DB::raw('case when t.alerts_enabled = 1 then t.threshold else 0 end as threshold_count'))
            ->addSelect(DB::raw('case when categories.use_default_eula = 1 then 1 when categories.eula_text <> null and categories.eula_text <> "" then 1 else 0 end as eula'))
            ->leftJoin('thresholds as t', 'categories.id', '=', 't.cat_id')
            ->leftJoin('procure_account_types as pac', 'categories.account_type_id', '=', 'pac.id')
            ->leftJoin('departments as d', 'categories.department_id', '=', 'd.id');
        if (Setting::getSettings()->department_config == 1) {
            $department_id = Auth::user()->asset_departments_id
                ? explode(',', Auth::user()->asset_departments_id)
                : [];
            $query->whereIn('d.id', $department_id);
        }
        $return['recordsTotal'] = $query->count();
        $return['recordsFiltered'] = $query->count();

        if ($request->q) {
            $request_filters = base64_decode($request->q);
            $filters = json_decode($request_filters);
            $req = [];

            if (isset($filters->search)) {
                $req["search"] = $filters->search;
            }
            if (isset($req["search"]) && $search_key = trim($req["search"])) {
                $whereStr = sprintf('(categories.name like "%%%1$s%%" or categories.category_type like "%%%1$s%%" or pac.name like "%%%1$s%%" or (case when t.alerts_enabled = 1 then t.threshold else 0 end) like "%%%1$s%%" or DATE_FORMAT(categories.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
                $query->whereRaw($whereStr);
                $return['recordsFiltered'] = $query->count();
            }
        }
        $results =  $query->whereNull("categories.deleted_at")->orderBy('categories.updated_at', 'desc')->get();
        $result = json_decode(json_encode($results, true), true);
        return Excel::download(new Categories($result), 'Categories.xlsx');
    }

    public function categoryHistory($id)
    {
        $info_id = $id;
        $name = Category::where('id',$id)->pluck('name')->first();
        return view("categories.history")->with(['id' => $info_id,'name'=>$name]);

    }

    public function ajaxCategoryHistory(Request $request)
    {
        $req = $request->all();
        // $return = array(
        //     "draw" => date('is')
        // );
        $id = $request->_id;
        $fields = array(
            'a.id' => 'c.id',
            'a.name' => 'c.name',
            'a.category_type' => 'c.category_type',
            'a.account_type' => 'c.account_type_id',
            'a.require_acceptance' => 'c.require_acceptance',
            'a.eula' => 'eula',
            'a.checkin_email' => 'c.checkin_email',
            'a.threshold_count' => 'threshold_count',
            'a.updated_by' => 'updated_by',
            'a.updated_at' => 'updated_at'
        );

        $get_cat = DB::table('categories_history as c')->whereIn('category_id', [$id]);
        $get_cat->leftJoin('thresholds as t', 'c.id', '=', 't.cat_id');
        $get_cat->leftJoin('users as u', 'c.updated_by', '=', 'u.id');
        $get_cat->leftJoin('procure_account_types as pac', 'c.account_type_id', '=', 'pac.id');
        $get_cat->select('c.id', 'c.name', 'c.checkin_email', 'c.category_type', 'c.require_acceptance', 'pac.name as account_type', 'c.updated_by', 'c.updated_at');
        $get_cat->addSelect(DB::raw('case when t.alerts_enabled = 1 then t.threshold else 0 end as threshold_count'));
        $get_cat->addSelect(DB::raw('case when c.use_default_eula = 1 then 1 when c.eula_text <> null and c.eula_text <> "" then 1 else 0 end as eula'));
        $mysqlDateTimeFormat = CommonHelper::mysqlDateTimeFormat('datetime', 'display');
        $get_cat->addSelect(DB::raw("DATE_FORMAT(c.updated_at, '{$mysqlDateTimeFormat}') as updated_at"));
        $get_cat->addSelect(DB::raw('concat(u.first_name, " ", u.last_name) as updated_by'));
        // $get_cat->latest('id','desc');
        $return['recordsTotal'] = $get_cat->count();
        $return['recordsFiltered'] = $return['recordsTotal'];
        if (isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"])) {
            $whereStr = sprintf('(u.first_name like  "%%%1$s%%" or u.last_name like  "%%%1$s%%" or c.id like  "%%%1$s%%" or c.name like "%%%1$s%%" or c.category_type like "%%%1$s%%" or pac.name like "%%%1$s%%" or (case when t.alerts_enabled = 1 then t.threshold else 0 end) like "%%%1$s%%" or DATE_FORMAT(c.updated_at, "%%d %%b %%Y %%h:%%i %%p") like "%%%1$s%%")', $search_key);
            $get_cat->whereRaw($whereStr);
            $return['recordsFiltered'] = $get_cat->count();
        }


        if (isset($req['sorted_column_name']) && isset($fields[$req['sorted_column_name']]) && in_array($req['sorted_direction'], ['asc', 'desc'])) {
            $get_cat->orderBy($fields[$req['sorted_column_name']], $req['sorted_direction']);
        }

        $skip = 0;
        $take = 10;
        if (isset($req["start"]) && isset($req["length"])) {
            $skip = (int) $req["start"];
            $take = (int) $req["length"];
        }
        $get_cat->skip($skip);
        $get_cat->take($take);

        $data = $get_cat->get();
        $return['data'] = array();
        foreach ($data as $d) {
            $d->category_type = strtoupper($d->category_type);
            $return['data'][] = array('a' => $d);
        }
        return response()->json($return);
    }

    public function getDataForCustomFormBaseOnType(Request $request)
    {
        $return = [];
        try {
            $search = $request->input("term", "") ? $request->input("term", "") : $request->input("search", "");
            $page = $request->input("page", 1);
            $type = $request->input("type", "");
            $skip = (($page * 20) - 20);

            $category = Category::find($request->cat_id);
            if (!$category) {
                return response()->json(['error' => 'Category not found'], 404);
            }

            $catType = $category->category_type;
            $path = '';
            if ($catType == "component") {
                $path = asset("uploads/component/");
                $table = "components";
            } elseif ($catType == "accessory") {
                $path = asset("uploads/accessories/");
                $table = "accessories";
            } elseif ($catType == "consumable") {
                $path = asset("uploads/consumable/");
                $table = "consumables";
            } elseif ($catType == "license") {
                $path = asset("uploads/license/");
                $table = "licenses";
            }

            $defaultImage = asset("uploads/default-image.jpg");
            $categoryImage = $category->image ? asset("uploads/category/" . $category->image) : $defaultImage;

            $db = DB::table($table)->select(
                "id",
                "name as text",
                DB::raw("IF(image IS NOT NULL AND image != '', CONCAT('$path/', image), '$categoryImage') as image")
            );
            if (config('app.client') == 'ltsct' && $request->has('category_type') && isset($request->category_type)) {
                $db->where('_itm_consumable_type', 'like', $request->category_type . '%');
            }
            $db->where('category_id', $request->cat_id)->where('location_id', $request->location_id);
            $db->whereNull("deleted_at");
            if ($search) {
                $db->whereRaw("name like ?", ["%$search%"]);
            }
            $count = $db->count();
            $db->skip($skip)->take(20);
            $db->orderBy('name', 'ASC');
            $result = $db->get();
            $return["pagination"] = ["more" => ($count - ($page * 20)) > 0];
            $return["results"] = count($result) ? $result->toArray() : [];
        } catch (\Exception $e) {
            Log::info('getDataForCustomFormBaseOnType error: ' . $e->getMessage());
        }
        return response()->json($return);
    }

    public function fetchAvailableItem(Request $request)
    {
        try {
            $category = Category::find($request->category);
            if (!$category) {
                return response()->json(['error' => 'Category not found'], 404);
            }

            $catType = $category->category_type;
            $avail = 0;
            if ($catType == "component") {
                $avail = Component::where('id', $request->item)->withCount(['users'])->first();
                $avail = $avail->qty - $avail->users_count;
            } elseif ($catType == "accessory") {
                $avail = Accessory::where('id', $request->item)->withCount(['users'])->first();
                $avail = $avail->qty - $avail->users_count;
            } elseif ($catType == "consumable") {
                $avail = Consumable::where('id', $request->item)->withCount(['users'])->first();
                $avail = $avail->qty - $avail->users_count;
            } elseif ($catType == "license") {
                $db = DB::table('licenses as a');
                $db->leftJoin(DB::raw('(SELECT license_id, count(id) as tot_seats FROM `license_seats` where deleted_at is null group by license_id) as lu1'), function ($j) {
                    $j->on('a.id', '=', 'lu1.license_id');
                });
                $db->leftJoin(DB::raw('(SELECT license_id, count(id) as used_seats FROM `license_seats` where (assigned_to is not null or asset_id is not null) and deleted_at is null group by license_id) as lu2'), function ($j) {
                    $j->on('a.id', '=', 'lu2.license_id');
                });

                $db->addSelect(DB::raw('case when lu2.used_seats is not null then (lu1.tot_seats - lu2.used_seats) else lu1.tot_seats end as remaining'));
                $db->where('a.id', $request->item);
                $data = $db->first();
                $avail = $data->remaining;
            }
            $return['type'] = $catType;
            $return['data'] = $avail;
        } catch (\Exception $e) {
            Log::error('fetchAvailableItem error: ' . $e->getMessage());
        }
        return response()->json($return);
    }
}
