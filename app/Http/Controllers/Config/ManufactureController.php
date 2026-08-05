<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\Manufacture;
use App\Exports\Manufactures;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;  // v3


class ManufactureController extends Controller
{
    public function getIndex()
    {
        $return = ['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')];
        if (!Auth::user()->hasPermissionTo('ManufactureRead')) {
            return redirect('dashboard')->with("msg", $return);
        }
        return view("manufactures.index")->with("manufacture", new Manufacture);
    }

    /* to edit label in ajax call */
    public function ajaxGetManufacturer(Request $request, $id)
    {
        $return = [
            'status' => 'failure',
            'msg' => trans('content.manufacturer_fields.unable_to_get_manufacturer')
        ];

        $objMan = "";
        try {
            $objMan = Manufacture::find($id);

            if (empty($objMan)) {
                return response()->json($return);
            }

            $return['data'] = $objMan->only('name', 'id', 'attachment');
            $return['msg'] = null;
            $return['status'] = 'success';
            return response()->json($return);
        } catch (\Exception $e) {
            return response()->json($return);
        }
    }

    public function ajaxGetList(Request $request)
    {
        $req = $request->all();
        $return = array(
            "draw" => date('is'),
            "recordsTotal" => 0,
            "recordsFiltered" => 0,
            "data" => []
        );
        $fail_reurn = $return;

        $query_fields = 'select m.id, m.name, m.attachment, s.tot as tot from manufacturers as m left join (select mdl.manufacturer_id, count(d.id) as tot from assets as d join models as mdl on d.model_id = mdl.id where company_id is not null group by mdl.manufacturer_id) as s on m.id = s.manufacturer_id';
        $query_count = 'select count(*) as tot from manufacturers as m left join (select mdl.manufacturer_id, count(d.id) as tot from assets as d join models as mdl on d.model_id = mdl.id where company_id is not null group by mdl.manufacturer_id) as s on m.id = s.manufacturer_id';

        /* tot count */
        $query = $query_count;
        try {
            $results = DB::select($query);
            $return['recordsTotal'] = $results[0]->tot;
        } catch (\Exception $e) {
            return response()->json($fail_reurn);
        }

        $return['recordsFiltered'] = $return['recordsTotal'];

        $where = '';
        if (isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"])) {
            $where = ' where (m.name like "%' . $search_key . '%" or m.id like "%' . $search_key . '%") ';

            /* filtered count */
            try {
                $query = $query_count . $where;
                $results = DB::select($query);
                $return['recordsFiltered'] = $results[0]->tot;
            } catch (\Exception $e) {
                return response()->json($fail_reurn);
            }
        }

        $order = '';
        if (isset($req["order"][0]["dir"]) && in_array($req["order"][0]["dir"], ["asc", "desc"])) {
            $name = '';
            if ($req["order"][0]["column"] == 0) {
                $name = 'm.id';
            } else if ($req["order"][0]["column"] == 1) {
                $name = 'm.name';
            }
            if ($name != '') {
                $order = ' order by ' . $name . ' ' . $req["order"][0]["dir"] . ' ';
            }
        }

        $skip = 0;
        $take = 10;
        if (isset($req["start"]) && isset($req["length"])) {
            $skip = (int) $req["start"];
            $take = (int) $req["length"];
        }

        try {
            $query = $query_fields . $where . $order . ' limit ' .  $skip . ',' . $take;
            $results = DB::select($query);
            foreach ($results as $d) {
                $return['data'][] = array('a' => $d);
            }
        } catch (\Exception $e) {
            return response()->json($fail_reurn);
        }

        return response()->json($return);
    }

    /* to add manufacturer in ajax call */
    public function ajaxAdd(Request $request) {
        $return = [
            'status' => 'failure',
            'msg' => trans('content.manufacturer_fields.unable_to_add_manufacturer')
        ];
        if(!Auth::user()->hasPermissionTo('ManufactureAdd')) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }
        $data = $request->only('name','attachment');

        $rules = [
            'name' => 'required|string|max:50|unique:manufacturers,name|clean_text_only',
            // 'attachment' =>'nullable|mimes:jpeg,bmp,png,PNG,jpg',
        ];
        if($request->has("attachment") && $request->input("attachment") != "undefined") {
            $rules["attachment"] = 'mimes:jpeg,bmp,png,PNG,jpg|max:2000';
        }
        $messages = [
            'name.required' => trans('content.manufacturer_fields.please_provide_manufacturer_name'),
            'name.max' => trans('content.manufacturer_fields.please_enter_no_more_than_50_characters'),
            'attachment.max' => trans('content.manufacturer_fields.attachment_max_size'),
        ];
        $validator = Validator::make($data, $rules, $messages);

        if($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
           // return response()->json($return);
        }
        else {
            $objManufacture = new Manufacture;
            $objManufacture->name = $data["name"];
            $objManufacture->user_id = Auth::user()->id;
             if($request->hasFile('attachment')) {
                $uploaded_img = $request->attachment;
                $deviceImage = str::random(12) . str::random(12) . '.' .
                // $deviceImage = str_random(12) . str_random(12) . '.' .
                $uploaded_img->getClientOriginalExtension();
                $path = public_path("uploads/manufacturers/" . $deviceImage);
                if (!file_exists(public_path("uploads/manufacturers/"))) {
                    mkdir(public_path("uploads/manufacturers/"), 777, true);
                }
                // Image::make($uploaded_img->getRealPath())->resize(300, null, function($constraint) {
                //     $constraint->aspectRatio();
                //     $constraint->upsize();
                // })->save($path);
                 // Intervention v3
                $manager = new ImageManager(new Driver());
                $image = $manager->read($uploaded_img->getRealPath());
                $image->scale(width: 300);
                $image->save($path);
                $objManufacture->attachment  = $deviceImage;

            }
            else{
                $objManufacture->attachment  = null;
            }
            if($objManufacture->save()) {
                $return["msg"] = "Manufacturer has been added successfully";
                $return["status"] = "success";
            }
        }

        return response()->json($return);
    }

    /* to edit manufacturer in ajax call */
    public function ajaxEdit(Request $request, $id)
{
    $return = [
        'status' => 'failure',
        'msg' => trans('content.manufacturer_fields.unable_to_edit_manufacturer'),
    ];
    if (!Auth::user()->hasPermissionTo('ManufactureEdit')) {
        $return["msg"] = trans('content.user_fields.Permission_denied');
        return response()->json($return);
    }
    $objManufacture = "";
    try {
        $objManufacture = Manufacture::find($id);

        if (empty($objManufacture)) {
            $return["status"] = "error";
            $return["msg"] = trans('content.manufacturer_fields.record_deleted');
            return response()->json($return);
        }

        $data = $request->only('name', 'attachment');
            $rules = [
                'name' => [
                    'required',
                    'max:50',
                    'clean_text_only',
                    Rule::unique('manufacturers')->ignore($id)
                ],
                // 'attachment' =>'nullable|mimes:jpeg,bmp,png,PNG,jpg',
            ];
            if ($request->has("attachment") && $request->input("attachment") != "undefined") {
                $rules["attachment"] = 'mimes:jpeg,bmp,png,PNG,jpg|max:2000';
            }
            $messages = [
                'name.required' => trans('content.manufacturer_fields.please_provide_manufacturer_name'),
                'name.max' => trans('content.manufacturer_fields.please_enter_no_more_than_50_characters'),
                'attachment.max' => trans('content.manufacturer_fields.attachment_max_size')
            ];
            $validator = Validator::make($data, $rules, $messages);
            if ($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }
            $objManufacture->name = trim($data["name"]);
            // $objManufacture->attachment = null;
            if ($request->hasFile('attachment')) {    
                if ($objManufacture->attachment) {
                    $path = public_path('/uploads/manufacturers/' . $objManufacture->attachment);
                    if (file_exists($path)) {
                        unlink($path);
                    }
                }
                $uploaded_img = $request->attachment;
                $deviceImage = str::random(12) . str::random(12) . '.'
                . $uploaded_img->getClientOriginalExtension();
                $path = public_path("uploads/manufacturers/" . $deviceImage);
                if (!file_exists(public_path("uploads/manufacturers/"))) {
                    mkdir(public_path("uploads/manufacturers/"), 777, true);
                }
                // Image::make($uploaded_img->getRealPath())->resize(300, null, function ($constraint) {
                //     $constraint->aspectRatio();
                //     $constraint->upsize();
                // })->save($path);

                 $manager = new ImageManager(new Driver());
                $image = $manager->read($uploaded_img->getRealPath());
                $image->scale(width: 300);
                $image->save($path);

                $objManufacture->attachment  = $deviceImage;
            } elseif ($objManufacture->attachment && $request->input("delete_img", false)) {
                $path = public_path('/uploads/manufacturers/' . $objManufacture->attachment);
                if (file_exists($path)) {
                    unlink($path);
                }
                $objManufacture->attachment = null;
            }
            if ($objManufacture->save()) {
                $return["msg"] = trans('content.manufacturer_fields.manufacturer_updated_successfully');
                $return["status"] = "success";
            }
        } catch (\Exception $e) {
            dd($e);
            return response()->json($return);
        }

        return response()->json($return);
    }

    public function addManufacture(Request $request)
    {
        if ($request->isMethod('post')) {
            $rules = [
                'name' => 'required|clean_text_only',
            ];
            $messages = [
                'name.required' => trans('content.manufacturer_fields.please_provide_manufacturer_name')
            ];
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator->messages());
            } else {
                $data = $request->validate($rules);
                $objManufacture = new Manufacture;
                $objManufacture->name = $data["name"];
                $objManufacture->user_id = Auth::user()->id;
                if ($objManufacture->save()) {
                    $return["msg"] =trans('content.manufacturer_fields.manufacturer_added_successfully');;
                    $return["status"] = "success";
                }
                $request->session()->flash("msg", $return);
            }
        }
        return view("manufactures.add");
    }

    public function editManufacture($id, Request $request)
    {
        $objManufacture = Manufacture::find($id);
        $return = [
            'status' => 'failure',
            'msg' => 'Unable to find the manufacturer'
        ];
        if (empty($objManufacture)) {
            return redirect()->action("ManufactureController@getIndex")->with("msg", array(
                "status" => "warning",
                "msg" => trans('content.manufacturer_fields.no_manufacturer_found')
            ));
        }

        if ($request->isMethod('post')) {
            $rules = [
                // 'name' => 'required|string|max:50|unique:manufacturers,name',
                'name' => [
                    'clean_text_only',
                    'required',
                    'max:50',
                    Rule::unique('manufacturers')->ignore($id)
                ],
            ];
            $messages = [
                'name.required' =>  trans('content.manufacturer_fields.please_provide_manufacturer_name'),
                'name.max' => trans('content.manufacturer_fields.please_enter_no_more_than_50_characters'),
            ];
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator->messages());
            } else {
                $data = $request->validate($rules);

                $objManufacture->name = $data["name"];
                $objManufacture->user_id = Auth::user()->id;
                if ($objManufacture->save()) {
                    $return["msg"] = "Manufacturer has been updated successfully";
                    $return["status"] = "success";
                    return redirect()->action("ManufactureController@getIndex")->with("msg", $return);
                }

                $request->session()->flash("msg", $return);
            }
        }

        return view("manufactures.edit")->with("manufacture", $objManufacture);
    }

    public function deleteManufacture($id)
    {
        $return = array("status" => "error", "msg" => trans('content.user_fields.Permission_denied'));

        if (!Auth::user()->hasPermissionTo('ManufactureDelete')) {
            return response()->json($return);
        }
        $record = Manufacture::where('id', '=', $id)->withCount(['models', 'accessories', 'component', 'consumables', 'licenses'])->first();

        if (empty($record))
            return response()->json(['status' => 'error', 'msg' =>  trans('content.manufacturer_fields.manufacturer_not_exists')]);

        if ($record->accessories_count)
            return response()->json(['status' => 'error', 'msg' => trans('content.manufacturer_fields.accessories_exist')]);
        if ($record->consumables_count)
            return response()->json(['status' => 'error', 'msg' => trans('content.manufacturer_fields.consumables_exist')]);
        if ($record->component_count)
            return response()->json(['status' => 'error', 'msg' => trans('content.manufacturer_fields.components_exist')]);
        if ($record->licenses_count)
            return response()->json(['status' => 'error', 'msg' => trans('content.manufacturer_fields.licenses_exist')]);
        if ($record->models_count)
            return response()->json(['status' => 'error', 'msg' => trans('content.manufacturer_fields.models_exist')]);

        $record->delete();
        return response()->json(['status' => 'success', 'msg' => trans('content.manufacturer_fields.manufacturer_deleted_successfully')]);
    }

    public function getManufacturerByQuery(Request $request)
    {
        $return = array();
        $search = $request->input("search", "");
        $page = $request->input("page", 1);
        $skip = (($page * 20) - 20);

        $db = DB::table("manufacturers")->select("id", "name as text");
        if ($search) {
            $db->where("name", "like", "%" . $search . "%");
        }
        $db->whereNull("deleted_at");

        $count = $db->count();
        $db->skip($skip)->take(20);
        $result = $db->get();

        $return["pagination"] = array("more" => ($count - ($page * 20)) > 0 ? true : false);
        $return["results"] = count($result) ? $result->toArray() : [];
        return response()->json($return);
    }

    public function ManufactureExport(Request $request)
    {
        $return = array("status" => "danger", "msg" => trans('content.user_fields.Permission_denied'));
        if (! Auth::user()->hasPermissionTo('ManufactureDownload')) {
            return redirect('dashboard')->with("msg", $return);
        }
        $query = Manufacture::select('manufacturers.id', 'manufacturers.name');
        // ->leftJoin('models as mdl', 'mdl.manufacturer_id', "=",'manufacturers.id')
        // ->leftJoin('assets as d', 'd.model_id', "=",'mdl.id')
        // ->whereNotNull('d.company_id')
        // ->groupBy('mdl.manufacturer_id','manufacturers.id','manufacturers.name','manufacturers.created_at', 'manufacturers.updated_at');


        $return['recordsTotal'] = $query->count();
        $return['recordsFiltered'] = $query->count();

        if ($request->q) {
            $request_filters = base64_decode($request->q);
            $filters = json_decode($request_filters);
            $req = [];

            if (isset($filters->search)) {
                $req["search"] = $filters->search;
                $whereStr = sprintf('(manufacturers.id like "%%%1$s%%" or manufacturers.name like "%%%1$s%%")', $req["search"]);
                $query->whereRaw($whereStr);
            }
        }

        if ($search_key = trim($request->search)) {
            $return['recordsFiltered'] =  $return['recordsTotal'];
        }

        $results =  $query->get();
        $result = json_decode(json_encode($results, true), true);
        return Excel::download(new Manufactures($result), 'Manufactures.xlsx');
    }

    public function deleteManufactureAttachment(Request $request, $id)
    {
        $return = [
            'status' => 'failure',
            'msg' => trans('content.manufacturer_fields.unable_to_delete_image')
        ];
        try {
            $manufacture =  Manufacture::findOrFail($id);
            if ($manufacture && $manufacture->attachment) {
                $path = public_path('/uploads/manufacturers/' . $manufacture->attachment);
                if (file_exists($path)) {
                    unlink($path);
                }
                $manufacture->attachment = null;
                $manufacture->save();
                $return = [
                    'status' => 'success',
                    'msg' => trans('content.manufacturer_fields.image_deleted_successfully')
                ];
                return response()->json($return);
            }
        } catch (\Exception $e) {
            return response()->json($return);
        }
    }
}