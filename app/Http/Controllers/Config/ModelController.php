<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ModelHistory;
use App\Models\Model;
use App\Models\Manufacture;
use App\Models\Category;
use App\Models\Depreciation;
use App\Models\CustomFieldset;
use App\Models\Setting;
use App\Exports\ModelExport;
use Validator;
use Storage;
use Auth;
// use Image;
use Maatwebsite\Excel\Facades\Excel;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use DB;



class ModelController extends Controller
{
    public function getIndex()
    {
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if (! Auth::user()->hasPermissionTo('ModelRead')) {
            return redirect('dashboard')->with("msg", $return);
        }
        return view("models.index")->with("model", new Model);
    }

    public function exportModelDetails(Request $request)
    {
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if (! Auth::user()->hasPermissionTo('ModelDownload')) {
            return redirect('dashboard')->with("msg", $return);
        }
        $req = $request->all();
        $return = array(
            "draw" => date('is')
        );

        $fields = array(
            '1' => 'mnu.name',
            '2' => 'm.name',
            '3' => 'm.modelno',
            '4' => 'dep.name',
            '5' => 'cat.name',
            '6' => 'ass.tot',
            '7' => 'm.eol',
        );

        $db = DB::table('models as m');
        $db->leftJoin('manufacturers as mnu', 'mnu.id', '=', 'm.manufacturer_id');
        $db->leftJoin('categories as cat', 'cat.id', '=', 'm.category_id');
        $db->leftJoin('depreciations as dep', 'dep.id', '=', 'm.depreciation_id');

        $db->leftJoin(DB::raw('(SELECT model_id, count(id) as tot FROM `assets` where deleted_at is null group by model_id) ass'), function ($j) {
            $j->on('m.id', '=', 'ass.model_id');
        });

        $db->select('m.name as model_name', 'm.modelno as model_no', 'mnu.name as mnu_name', 'dep.name as dep_name', 'cat.name as cat_name', 'm.eol as eol', 'ass.tot');
        $db->orderBy('m.id', 'desc');

        if (isset($req["deletedRecords"]) && $req["deletedRecords"] == "true") {
            $db->whereNotNull('m.deleted_at');
        } else {
            $db->whereNull('m.deleted_at');
        }
        if ($request->q) {
            $request_filters = base64_decode($request->q);
            $filters = json_decode($request_filters);
            if (isset($filters)) {
                $req["search"] = $filters->search;
            }
            if (isset($req["search"]) && $search_key = trim($req["search"])) {
                $whereStr = sprintf('(m.name LIKE "%%%1$s%%" OR m.modelno LIKE "%%%1$s%%" OR mnu.name LIKE "%%%1$s%%" OR dep.name LIKE "%%%1$s%%" OR cat.name LIKE "%%%1$s%%" OR m.eol LIKE "%%%1$s%%" OR ass.tot LIKE "%%%1$s%%")', $search_key);
                $db->whereRaw($whereStr);
                $return['recordsFiltered'] = $db->count();
            }
        }

        $data = $db->get();

        $lineArray = [];
        foreach ($data as $key => $val) {

            $lineArray[] = [
                $val->mnu_name,
                $val->model_name,
                $val->model_no,
                $val->dep_name,
                $val->cat_name,
                $val->tot,
                $val->eol,

            ];
        }
        return Excel::download(new ModelExport($lineArray), 'Model Details.xlsx');
    }

    public function ajaxIndex(Request $request)
    {

        // $records = Model::withTrashed()->with(['manufacturer','category','depreciation'])->withCount('assets');

        // if($request->deletedRecords == 'true') $records->whereNotNull('deleted_at');
        // if($request->deletedRecords == 'true') $records->onlyTrashed();
        // if($request->deletedRecords == 'false') $records->whereNull('deleted_at');
        // $records->whereNotNull('deleted_at');

        $req = $request->all();
        // $return = array(
        //     "draw" => date('is')
        // );

        $fields = [
            'a.id' => 'm.id',
            'a.mnu_name'   => 'mnu.name',
            'a.model_name' => 'm.name',
            'a.model_no'   => 'm.modelno',
            'a.tot'        => 'ass.tot',
            'a.dep_name'   => 'dep.name',
            'a.cat_name'   => 'cat.name',
            'a.eol'   => 'm.eol',
        ];

        $records = DB::table('models as m');
        $records->leftJoin('manufacturers as mnu', 'mnu.id', '=', 'm.manufacturer_id');
        $records->leftJoin('categories as cat', 'cat.id', '=', 'm.category_id');
        $records->leftJoin('depreciations as dep', 'dep.id', '=', 'm.depreciation_id');
        $records->leftJoin(DB::raw('(SELECT model_id, count(id) as tot FROM `assets` where deleted_at is null group by model_id) ass'), function ($j) {
            $j->on('m.id', '=', 'ass.model_id');
        });

        $records->select('m.id', 'm.name as model_name', 'm.modelno as model_no', 'mnu.name as mnu_name', 'dep.name as dep_name', 'cat.name as cat_name', 'm.eol as eol', 'ass.tot', 'm.image_thumbnail', 'm.deleted_at');
        if (isset($req["deletedRecords"]) && $req["deletedRecords"] == "true") {
            $records->whereNotNull('m.deleted_at');
        } else {
            $records->whereNull('m.deleted_at');
        }

        $return['recordsTotal'] = $records->count();
        $return['recordsFiltered'] = $return['recordsTotal'];

        if (isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"])) {
            $whereStr = sprintf('(m.name like "%%%1$s%%" or m.modelno like "%%%1$s%%" or mnu.name like "%%%1$s%%" or dep.name like "%%%1$s%%" or cat.name like "%%%1$s%%" or eol like "%%%1$s%%")', $search_key);
            $records->whereRaw($whereStr);
            $return['recordsFiltered'] = $records->count();
        }

        if ($request->filled('order')) {
            $order = $request->input('order.0');
            if ($order) {
                $columnIndex = $order['column'];
                $column = $request->input('columns.' . $columnIndex . '.data');
                $direction = strtolower($order['dir']);
                if (isset($fields[$column]) && in_array($direction, ['asc', 'desc'])) {
                    $records->orderBy($fields[$column], $direction);
                }
            } else {
                $records->orderBy('m.id', 'desc');
            }
        }

        $skip = 0;
        $take = 10;
        if (isset($req["start"]) && isset($req["length"])) {
            $skip = (int) $req["start"];
            $take = (int) $req["length"];
        }
        $records->skip($skip);
        $records->take($take);

        $data = $records->get();
        $return['data'] = array();
        foreach ($data as $d) {
            $return['data'][] = array('a' => $d);
        }
        return response()->json($return);
    }


    public function getFormData()
    {
        return response()->json([
            'manufacturers' => Manufacture::select('id', 'name')->get(),
            'categories' => Category::where('category_type', 'asset')->select('id', 'name')->get(),
            'depreciations' => Depreciation::select('id', 'name')->get(),
            'fieldsets' => CustomFieldset::select('id', 'name')->get(),
        ]);
    }




    public function addModel(Request $request)
    {
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];

        if (!Auth::user()->hasPermissionTo('ModelAdd')) {
            return response()->json($return);
        }

        if ($request->isMethod('post')) {

            $rules = [
                'name'            => 'required|clean_text_only',
                'manufacturer_id' => 'required',
                'category_id'     => 'required',
                'depreciation_id' => 'sometimes',
                'modelno'         => 'max:100|clean_text_only',
                'eol'             => 'nullable|numeric',
                'fieldset_id'     => '',
                'model_image'     => 'sometimes|image',
            ];

            $messages = [
                'name.required' => 'Please provide Name of asset.',
                'manufacturer_id.required' => 'Please select manufacture.',
                'depreciation_id.required' => 'Please select depreciation.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'msg' => $validator->errors()->first()
                ]);
            }

            $objModel = new Model;
            $data = $request->all();

            if (isset($data['model_image'])) {
                $attachment = $data['model_image'];
                unset($data['model_image']);

                $ModelImageName = uniqid('', true);

                $objModel->image = $ModelImageName . '.' . $request->file('model_image')->getClientOriginalExtension();

                Storage::disk('model')->put(
                    $objModel->image,
                    file_get_contents($request->file('model_image')->getRealPath())
                );

                $uploaded_img = $request->file('model_image');
                $resizedModelImage = $ModelImageName . '_thumbnail.' . $uploaded_img->getClientOriginalExtension();

                $path = public_path("uploads/models/" . $resizedModelImage);

                // Image::make(public_path("uploads/models/" . $objModel->image))
                //     ->resize(100, null, function ($constraint) {
                //         $constraint->aspectRatio();
                //         $constraint->upsize();
                //     })
                //     ->save($path);
                // $objModel->image_thumbnail = $resizedModelImage;
                $manager = new ImageManager(new Driver());
                $image = $manager->read(public_path("uploads/models/" . $objModel->image));
                $image->scale(width: 100);
                if ($image->width() < 100) {
                } else {
                    $image->scale(width: 100);
                }
                $image->save($path);
                $objModel->image_thumbnail = $resizedModelImage;
            }
            $objModel->name            = $data["name"];
            $objModel->user_id         = Auth::id();
            $objModel->created_by      = Auth::id();
            $objModel->manufacturer_id = $data["manufacturer_id"];
            $objModel->category_id     = $data["category_id"];
            $objModel->modelno         = $data["modelno"];
            $objModel->depreciation_id = $data["depreciation_id"];
            $objModel->eol             = $data["eol"];
            // $objModel->fieldset_id     = $data["fieldset_id"];
            $objModel->fieldset_id = $data["fieldset_id"] ?? null;

            if (isset($request->fieldset_id)) {
                $catFieldsSet = Category::where('id', $data["category_id"])->first();
                if (isset($catFieldsSet->fieldset_id) && $catFieldsSet->fieldset_id == $request->fieldset_id) {
                    return response()->json([
                        'status' => 'error',
                        'msg' => trans('content.device_fields.custom_field_is_already_used_for_this_category')
                    ]);
                }
                $fieldset_id = Setting::select('custom_fieldset_id')
                    ->where('custom_fieldset_id', $request->fieldset_id)
                    ->first();
                if (!empty($fieldset_id)) {
                    return response()->json([
                        'status' => 'error',
                        'msg' => trans('content.licenses_fields.custom_field_is_already_used_for_global_setting')
                    ]);
                }
            }
            if ($objModel->save()) {
                return response()->json([
                    'status' => 'success',
                    'msg' => trans('config.model_fields.add_success')
                ]);
            }
        }
        return response()->json([
            'status' => 'error',
            'msg' => trans('config.model_fields.invalid_request')
        ]);
    }



    public function ajaxGetModel(Request $request, $id)
    {
        $return = [
            'status' => 'failure',
            'msg' => 'Unable to get model'
        ];
        try {
            $model = Model::find($id);

            if (!$model) return response()->json($return);

            $return['status'] = 'success';
            $return['msg'] = null;
            $return['data'] = $model->only([
                'id',
                'name',
                'manufacturer_id',
                'category_id',
                'modelno',
                'depreciation_id',
                'eol',
                'fieldset_id',
                'image_thumbnail'
            ]);
            return response()->json($return);
        } catch (\Exception $e) {
            return response()->json($return);
        }
    }


    public function ajaxEditModel(Request $request, $id)
    {
        $return = [
            'status' => 'failure',
            'msg' => 'Unable to update model'
        ];

        if (!Auth::user()->hasPermissionTo('ModelEdit')) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }

        try {

            $objModel = Model::find($id);
            if (empty($objModel)) {
                return response()->json([
                    'status' => 'error',
                    'msg' => 'Model not found'
                ]);
            }

            $rules = [
                'name'            => 'required|clean_text_only',
                'manufacturer_id' => 'required',
                'category_id'     => 'required',
                'depreciation_id' => 'sometimes',
                'modelno'         => 'max:100|clean_text_only',
                'eol'             => 'nullable|numeric',
                'fieldset_id'     => '',
                'model_image'     => 'sometimes|image',
            ];

            $messages = [
                'name.required' => 'Please provide Name of asset.',
                'manufacturer_id.required' => 'Please select manufacture.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'msg' => $validator->errors()->first()
                ]);
            }

            $data = $request->all();

            $newRecord = $objModel->replicate();
            $newRecord->setTable('model_history');
            $newRecord->model_id = $id;
            $newRecord->save();
            if (isset($data['image_delete']) && $data['image_delete'] == '1') {
                $path = public_path('/uploads/models/' . $objModel->image);
                $thumb = public_path('/uploads/models/' . $objModel->image_thumbnail);
                if (file_exists($path)) unlink($path);
                if (file_exists($thumb)) unlink($thumb);

                $objModel->image = null;
                $objModel->image_thumbnail = null;
            }

            if ($request->hasFile('model_image')) {
                $ModelImageName = uniqid('', true);
                $objModel->image = $ModelImageName . '.' . $request->file('model_image')->getClientOriginalExtension();
                Storage::disk('model')->put(
                    $objModel->image,
                    file_get_contents($request->file('model_image')->getRealPath())
                );
                $uploaded_img = $request->file('model_image');
                $resizedModelImage = $ModelImageName . '_thumbnail.' . $uploaded_img->getClientOriginalExtension();
                $path = public_path("uploads/models/" . $resizedModelImage);

                // Image::make(public_path("uploads/models/" . $objModel->image))
                //     ->resize(100, null, function ($constraint) {
                //         $constraint->aspectRatio();
                //         $constraint->upsize();
                //     })
                //     ->save($path);

                // $objModel->image_thumbnail = $resizedModelImage;
                $manager = new ImageManager(new Driver());
                $image = $manager->read(public_path("uploads/models/" . $objModel->image));
                $image->scale(width: 100);
                if ($image->width() < 100) {
                } else {
                    $image->scale(width: 100);
                }
                $image->save($path);
                $objModel->image_thumbnail = $resizedModelImage;
            }

            if (isset($request->fieldset_id)) {
                $catFieldsSet = Category::where('id', $data["category_id"])->first();
                if (isset($catFieldsSet->fieldset_id)) {
                    if ($catFieldsSet->fieldset_id == $request->fieldset_id) {
                        return response()->json([
                            'status' => 'error',
                            'msg' => trans('content.device_fields.custom_field_is_already_used_for_this_category')
                        ]);
                    }
                }

                $fieldset_id = Setting::select('custom_fieldset_id')
                    ->where('custom_fieldset_id', $request->fieldset_id)
                    ->first();

                if (!empty($fieldset_id)) {
                    return response()->json([
                        'status' => 'error',
                        'msg' => trans('content.licenses_fields.custom_field_is_already_used_for_global_setting')
                    ]);
                }
            }

            $objModel->name = $data["name"];
            $objModel->manufacturer_id = $data["manufacturer_id"];
            $objModel->category_id = $data["category_id"];
            $objModel->modelno = $data["modelno"];
            $objModel->updated_by = Auth::id();
            $objModel->depreciation_id = isset($data["depreciation_id"]) && $data["depreciation_id"] != "null" ? $data["depreciation_id"] : null;
            $objModel->eol = $data["eol"];
            $objModel->fieldset_id = $data["fieldset_id"] ?? null;

            if ($objModel->save()) {
                return response()->json([
                    'status' => 'success',
                    'msg' => trans('config.model_fields.edit_success')
                ]);
            }
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json([
                'status' => 'error',
                'msg' => trans('config.model_fields.something_went_wrong')
            ]);
        }
    }

    // public function addModel(Request $request)
    // {
    //     $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
    //     if (! Auth::user()->hasPermissionTo('ModelAdd')) {
    //         return redirect('dashboard')->with("msg", $return);
    //     }
    //     if ($request->isMethod('post')) {

    //         $rules = [
    //             'name'              => 'required|clean_text_only',
    //             'manufacturer_id'   => 'required',
    //             'category_id'       => 'required',
    //             'depreciation_id'   => 'sometimes',
    //             'modelno'           => 'max:100|clean_text_only',
    //             // 'depreciation_id' => 'sometimes',
    //             'eol'               => 'nullable|numeric',
    //             'fieldset_id'       => '',
    //             'model_image'       => 'sometimes|image',
    //         ];
    //         $messages = [
    //             'name.required' => 'Please provide Name of asset.',
    //             'manufacturer_id.required' => 'Please select manufacture.',
    //             'depreciation_id.required' => 'Please select depreciation.',
    //         ];
    //         $validator = Validator::make($request->all(), $rules, $messages);

    //         if ($validator->fails()) {
    //             return back()->withInput()->withErrors($validator->messages());
    //         } else {
    //             $objModel = new Model;
    //             $data = $request->all();
    //             if (isset($data['model_image'])) {
    //                 $attachment = $data['model_image'];
    //                 unset($data['model_image']);
    //                 $ModelImageName = uniqid('', true);
    //                 $objModel->image =  $ModelImageName . '.' . $request->file('model_image')->getClientOriginalExtension();
    //                 $stat = Storage::disk('model')->put(
    //                     $objModel->image,
    //                     file_get_contents($request->file('model_image')->getRealPath())
    //                 );

    //                 $uploaded_img = $request->file('model_image');
    //                 $resizedModelImage = $ModelImageName . '_thumbnail.' . $uploaded_img->getClientOriginalExtension();
    //                 $path = public_path("uploads/models/" . $resizedModelImage);
    //                 Image::make(public_path("uploads/models/" . $objModel->image))->resize(100, null, function ($constraint) {
    //                     $constraint->aspectRatio();
    //                     $constraint->upsize();
    //                 })->save($path);
    //                 $objModel->image_thumbnail = $resizedModelImage;
    //             }
    //             $objModel->name                 = $data["name"];
    //             $objModel->user_id                = Auth::user()->id;
    //             $objModel->created_by           = Auth::user()->id;
    //             $objModel->manufacturer_id      = $data["manufacturer_id"];
    //             $objModel->category_id          = $data["category_id"];
    //             $objModel->modelno              = $data["modelno"];
    //             $objModel->depreciation_id      = $data["depreciation_id"];
    //             $objModel->eol                  = $data["eol"];
    //             $objModel->fieldset_id          = $data["fieldset_id"];

    //             if (isset($request->fieldset_id)) {
    //                 $catFieldsSet = Category::where('id', $data["category_id"])->first();
    //                 if (isset($catFieldsSet->fieldset_id)) {
    //                     if ($catFieldsSet->fieldset_id == $request->fieldset_id) {
    //                         $return["msg"] = trans('content.device_fields.custom_field_is_already_used_for_this_category');
    //                         return redirect()->action('ModelController@addModel')->with("msg", $return);
    //                     }
    //                 }
    //                 $fieldset_id = Setting::select('custom_fieldset_id')->where('custom_fieldset_id', $request->fieldset_id)->first();
    //                 if (!empty($fieldset_id)) {
    //                     $return["msg"] = trans('content.licenses_fields.custom_field_is_already_used_for_global_setting');
    //                     return redirect()->action('ModelController@addModel')->with("msg", $return);
    //                 }
    //             }
    //             if ($objModel->save()) {
    //                 // if(!empty($attachment)){
    //                 //     // $data_attach['original_file_name'] = $request->file('image')->getClientOriginalName();
    //                 //     $data_attach['original_file_name'] = $request->file('model_image')->getClientOriginalName();
    //                 //     $data_attach['suplier_id'] = $objModel->id;
    //                 //     //    echo $path = $request->image->store('tkt');

    //                 //     $data_attach['file_name'] = uniqid('',true).'.'.$request->file('image')->getClientOriginalExtension();

    //                 //     $transfer_stat = Storage::disk('supplier')->put($data_attach['file_name'],
    //                 //         file_get_contents($request->file('image')->getRealPath())
    //                 //     );

    //                 //     $data_attach['extension'] = $request->file('image')->getClientOriginalExtension();
    //                 //     $objSupplier->addSupplierAttachment($data_attach);
    //                 //     // $serviceTicket->addTicketAttachment($data_attach);
    //                 // }

    //                 $return["msg"] = "Model has been added successfully";
    //                 $return["status"] = "success";

    //                 return redirect()->action('ModelController@getIndex')->with("msg", $return);
    //             }
    //         }
    //     }

    //     return view("models.add")
    //         ->with("Category", new Category)
    //         ->with("Manufacture", new Manufacture)
    //         ->with("Depreciation", new Depreciation)
    //         ->with("CustomFieldset", new CustomFieldset);
    // }





    // public function editModel($id, Request $request)
    // {
    //     $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
    //     if (! Auth::user()->hasPermissionTo('ModelEdit')) {
    //         return redirect('dashboard')->with("msg", $return);
    //     }
    //     $objModel = Model::find($id);
    //     if (empty($objModel))
    //         return redirect('models');
    //     if ($request->isMethod('post')) {

    //         $rules = [
    //             'name'              => 'required|clean_text_only',
    //             'manufacturer_id'   => 'required',
    //             'category_id'       => 'required',
    //             'depreciation_id'   => 'sometimes',
    //             'modelno'           => 'max:100|clean_text_only',
    //             // 'depreciation_id' => 'sometimes',
    //             'eol'               => 'numeric',
    //             'fieldset_id'       => '',
    //             'model_image'       => 'sometimes|image',
    //         ];
    //         $messages = [
    //             'name.required' => 'Please provide Name of asset.',
    //             'manufacturer_id.required' => 'Please select manufacture.',
    //         ];
    //         $validator = Validator::make($request->all(), $rules, $messages);

    //         if ($validator->fails()) {
    //             return back()->withInput()->withErrors($validator->messages());
    //         } else {
    //             // echo '<pre>';                print_r($request->file('model_image'));die;
    //             $data = $request->all();

    //             // Store Model History
    //             $objModel = Model::findOrFail($id);
    //             $newRecord = $objModel->replicate();
    //             $newRecord->setTable('model_history');
    //             $newRecord->model_id = $id;
    //             $newRecord->save();

    //             if (isset($data['image_delete'])) {
    //                 if ($data['image_delete'] == '1') {
    //                     $path = public_path('/uploads/models/' . $objModel->image);
    //                     $path_thumbnail = public_path('/uploads/models/' . $objModel->image_thumbnail);
    //                     if (file_exists($path)) {
    //                         unlink($path);
    //                         unlink($path_thumbnail);
    //                     }
    //                     $objModel->image = null;
    //                     $objModel->image_thumbnail = null;
    //                 }
    //             }
    //             if (isset($data['model_image'])) {
    //                 $attachment = $data['model_image'];
    //                 unset($data['model_image']);
    //                 $ModelImageName = uniqid('', true);
    //                 $objModel->image =  ($ModelImageName . '.' . $request->file('model_image')->getClientOriginalExtension());
    //                 $stat = Storage::disk('model')->put(
    //                     $objModel->image,
    //                     file_get_contents($request->file('model_image')->getRealPath())
    //                 );
    //                 $uploaded_img = $request->file('model_image');
    //                 $resizedModelImage = $ModelImageName . '_thumbnail.' . $uploaded_img->getClientOriginalExtension();
    //                 $path = public_path("uploads/models/" . $resizedModelImage);
    //                 Image::make(public_path("uploads/models/" . $objModel->image))->resize(100, null, function ($constraint) {
    //                     $constraint->aspectRatio();
    //                     $constraint->upsize();
    //                 })->save($path);
    //                 $objModel->image_thumbnail = $resizedModelImage;
    //             }
    //             if (isset($request->fieldset_id)) {
    //                 $catFieldsSet = Category::where('id', $data["category_id"])->first();
    //                 if (isset($catFieldsSet->fieldset_id)) {
    //                     if ($catFieldsSet->fieldset_id == $request->fieldset_id) {
    //                         $return["msg"] = trans('content.device_fields.custom_field_is_already_used_for_this_category');
    //                         return redirect()->action([ModelController::class, 'editModel'], ['id' => $id])->with("msg", $return);
    //                     }
    //                 }
    //                 $fieldset_id = Setting::select('custom_fieldset_id')->where('custom_fieldset_id', $request->fieldset_id)->first();
    //                 if (!empty($fieldset_id)) {
    //                     $return["msg"] = trans('content.licenses_fields.custom_field_is_already_used_for_global_setting');
    //                     return redirect()->action([ModelController::class, 'editModel'], ['id' => $id])->with("msg", $return);
    //                 }
    //             }
    //             $objModel->name = $data["name"];
    //             $objModel->manufacturer_id = $data["manufacturer_id"];
    //             $objModel->category_id = $data["category_id"];
    //             $objModel->modelno = $data["modelno"];
    //             $objModel->updated_by = Auth::user()->id;
    //             $objModel->depreciation_id = $data["depreciation_id"];
    //             $objModel->eol = $data["eol"];
    //             $objModel->fieldset_id = $data["fieldset_id"];

    //             if ($objModel->save()) {
    //                 if (!empty($attachment)) {
    //                     //     // $data_attach['original_file_name'] = $request->file('image')->getClientOriginalName();
    //                     //     $data_attach['original_file_name'] = $request->file('model_image')->getClientOriginalName();
    //                     //     $data_attach['suplier_id'] = $objModel->id;
    //                     //     //    echo $path = $request->image->store('tkt');


    //                     // $transfer_stat =

    //                     //     $data_attach['extension'] = $request->file('image')->getClientOriginalExtension();
    //                     //     $objSupplier->addSupplierAttachment($data_attach);
    //                     //     // $serviceTicket->addTicketAttachment($data_attach);
    //                 }

    //                 $return["msg"] = "Model has been updated successfully";
    //                 $return["status"] = "success";

    //                 return redirect('models')->with("msg", $return);
    //             }
    //         }
    //     }

    //     return view("models.edit")
    //         ->with("model", $objModel)
    //         ->with("Category", new Category)
    //         ->with("Manufacture", new Manufacture)
    //         ->with("Depreciation", new Depreciation)
    //         ->with("CustomFieldset", new CustomFieldset);
    // }

    public function deleteModelImage(Request $request, $id)
    {
        $return = [
            'status' => 'failure',
            'msg' => 'Fail to delete attachment'
        ];

        try {
            $objModel = Model::find($id);
            if ($objModel) {
                $imp_path = public_path('uploads/models/') . $objModel->image;
                if (file_exists($imp_path)) {
                    unlink($imp_path);
                }
                $thumb_path = public_path('uploads/models/') . $objModel->image_thumbnail;
                if (file_exists($thumb_path)) {
                    unlink($thumb_path);
                }

                $objModel->image = null;
                $objModel->image_thumbnail = null;
                $objModel->save();
            }

            $return = [
                'status' => 'success',
                'msg' => 'Image deleted successfully'
            ];
        } catch (\Exception $e) {
            Log::error("deleteModelImage: " . Auth::id() . " - " . $e->getMessage());
            return response()->json($return);
        }

        return response()->json($return);
    }
    public function deleteModel($modelId)
    {
        $return = ['status' => 'error', 'msg' => trans('content.user_fields.Permission_denied')];
        if (!Auth::user()->hasPermissionTo('ModelDelete')) {
            return response()->json($return);
        }
        // Check if the model exists
        if (is_null($model = Model::find($modelId))) {
            // Redirect to the blogs management page
            return response()->json(['status' => 'error', 'msg' => trans('config.model_fields.model_not_found')]);
        }

        // print_r($model->assets->count());        die;
        if ($model->assets->count() > 0) {
            // Throw an error that this model is associated with assets
            return response()->json(['status' => 'error', 'msg' =>  trans('config.model_fields.model_delete_unassign_assets')]);
        } else {
            // Delete the model
            $model->delete();

            // Redirect to the models management page
            return response()->json(['status' => 'success', 'msg' =>  trans('config.model_fields.modal_deleted_success')]);
        }
    }

    public function restoreModel($modelId)
    {
        $return = ['status' => 'error', 'msg' => trans('content.user_fields.Permission_denied')];
        if (!Auth::user()->hasPermissionTo('ModelRestore')) {
            return response()->json($return);
        }
        $model = Model::withTrashed()->where("id", "=", $modelId)->first();

        if (empty($model))
            return response()->json(['status' => 'error', 'msg' => trans('config.model_fields.model_restore_unsuccess')]);

        $model->restore();
        return response()->json(['status' => 'success', 'msg' => trans('config.model_fields.model_restore_success')]);
    }

    public function modelHistory($id)
    {
        $info_id = $id;
        $model_name = Model::where('id',$id)->pluck('name')->first();
        return view("models.history")->with(['id' => $info_id, 'name' => $model_name]);
    }

    public function ajaxModelHistory(Request $request)
    {
        $req = $request->all();
        $id = $request->_id;
        $fields = [
            "a.id" => "model_history.id",
            "a.manufacturer.name" => "manufacturers.name",
            "a.name" => "model_history.name",
            "a.modelno" => "model_history.modelno",
            "a.assets_count" => "assets_count",
            "a.depreciation.name" => "depreciations.name",
            "a.category.name" => "categories.name",
            "a.eol" => "model_history.eol",
            "a.user.full_name" => "users.first_name",
            "a.updated_at_formated" => "updated_at_formated",
        ];

        $records = ModelHistory::withTrashed()
            ->with(['manufacturer', 'category', 'depreciation', 'user'])
            ->whereIn('model_id', [$id])
            ->withCount('assets')
            ->leftjoin('manufacturers', 'model_history.manufacturer_id', 'manufacturers.id')
            ->leftjoin('categories', 'model_history.category_id', 'categories.id')
            ->leftjoin('depreciations', 'model_history.depreciation_id', 'depreciations.id')
            ->leftJoin('users', 'model_history.user_id', '=', 'users.id')
            ->addSelect(DB::raw('DATE_FORMAT(model_history.updated_at, "%d %b %Y %h:%i %p") as updated_at_formated'));

        if ($request->deletedRecords == 'true') {
            $records->onlyTrashed();
        } elseif ($request->deletedRecords == 'false') {
            $records->whereNull('deleted_at');
        }
        $return['recordsFiltered'] = $records->count();
        $return['recordsTotal'] = $records->count();

        if (isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"])) {
            $whereStr = sprintf(
                '(model_history.name like "%%%1$s%%" or model_history.modelno like "%%%1$s%%" or manufacturers.name like "%%%1$s%%" or depreciations.name like "%%%1$s%%" or categories.name like "%%%1$s%%" or eol like "%%%1$s%%")',
                $search_key
            );
            $records->whereRaw($whereStr);
            $return['recordsFiltered'] = $records->count();
        }
        if (isset($req['sorted_column_name']) && isset($fields[$req['sorted_column_name']]) && in_array($req['sorted_direction'], ['asc', 'desc'])) {
            $records->orderBy($fields[$req['sorted_column_name']], $req['sorted_direction']);
        }
        $data = $records->get();
        $return['data'] = array();
        foreach ($data as $d) {
            $return['data'][] = array('a' => $d);
        }
        // dd($data);
        return response()->json($return);
    }
}
