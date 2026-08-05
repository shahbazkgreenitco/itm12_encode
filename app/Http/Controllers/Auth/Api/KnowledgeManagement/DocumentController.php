<?php

namespace App\Http\Controllers\Auth\Api\KnowledgeManagement;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeManagement\Document;
use App\Models\KnowledgeManagement\Category;
use App\Models\KnowledgeManagement\KDAttachment;
use App\Models\KnowledgeManagement\Tags;
use App\Helpers\Common as CommonHelper;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Mail;use Validator;
use App\Models\Ticket\Config;
use App\Models\Ticket\Privilege;
use DB;
use Storage;
use Image;
use Auth;

class DocumentController extends Controller
{
    public function ajaxdocumentList(Request $request) {
        try {
            $return = ["total" => 0, "filtered" => 0, "data" => []];
            $req = $request->all();

            $page = $request->index ? $request->index : 0;
            $take = $request->list_size ? $request->list_size : 10;
            $skip = $page * $take;

            $db = Document::select('knowledge_document.id', 'knowledge_document.title','knowledge_document.created_at', 'knowledge_document_category.category_name',
                'knowledge_document.content', 'knowledge_document.updated_at', 'knowledge_document.card_img', 'knowledge_document.tags', 'dep.name as dep_name', 'knowledge_document.status','knowledge_document.status','knowledge_document.created_by', 'knowledge_document.starred')
                ->addSelect(DB::raw('DATE_FORMAT(knowledge_document.updated_at, "%d %b %Y %h:%i %p") as updated_at_format'))
                ->leftJoin('departments as dep', 'knowledge_document.department_id', '=', 'dep.id')
                ->leftJoin('companies as comp', 'dep.company_id', '=', 'comp.id')
                ->leftjoin('knowledge_document_category', 'knowledge_document.parent_category_id', '=', 'knowledge_document_category.id');

            $current_user = Auth::user();
            if($current_user->isSuperUser()) {
                $db->whereIn("status", [1,2,3]);
            } elseif($current_user->hasPermissionTo('DeviceRead') || $current_user->hasPermission("service_tickets")) {
                $db->whereIn("status", [1,3]);
            } else {
                $db->whereIn("status", [1]);
            }

            if($current_user->hasAnyRole(['Admin', 'Technician']) && $current_user->hasPermission("service_tickets")) {
                $config = Config::first();
                if($config->department_config == 1) {
                    $prevDep = Privilege::where('user_id', $current_user->id)->pluck('department_id')->toArray();
                    $db->whereIn('knowledge_document.department_id', count($prevDep) > 0 ? $prevDep : [0]);
                }
            }

            if (!empty($req['tag'])) {
                $tagText = "";
                foreach ($req['tag'] as $tag) {
                    $tagText = $tag;
                }
                $db->where("tags", "like", "%" . $tagText . "%");
            }
            $return['total'] = $db->count();
            $return['filtered'] = $return['total'];

            $is_searching = false;
            if(isset($req["filters"])) {
                $filters = $req["filters"];
                if(isset($filters["status"]) && $filters['status'] && $filters['status'] != "null" && !empty(json_decode($filters['status']))) {
                    $db->whereIn("t.status_id", json_decode($filters['status']));
                }
                if (isset($filters["category"]) && $filters['category'] && $filters['category'] != "null" && !empty(json_decode($filters['category']))) {
                    // $categoryFilter = is_array($filters['category']) ? $filters['category'] : [$filters['category']];
                    $db->whereIn("knowledge_document_category.id", json_decode($filters['category']));
                }
                if(isset($filters["tag"]) && $filters['tag'] && $filters['tag'] != "null" && !empty(json_decode($filters['tag']))) {
                    foreach (json_decode($filters['tag']) as $value) {
                        $db->whereRaw('FIND_IN_SET("'. $value .'", knowledge_document.tags)');
                    }

                }
                if (isset($filters["department"]) && $filters['department'] && $filters['department'] != "null" && !empty(json_decode($filters['department']))) {
                    $db->where("knowledge_document.department_id", json_decode($filters['department']));
                }
                if (isset($filters["problem_category"]) && $filters['problem_category'] && $filters['problem_category'] != "null" && !empty(json_decode($filters['problem_category']))) {
                     $db->whereIn('knowledge_document.parent_category_id', json_decode($filters['problem_category']));
                }
                if(isset($filters["sub_category"]) && $filters['sub_category'] && $filters['sub_category'] != "null" && !empty(json_decode($filters['sub_category']))) {
                    $db->whereIn("knowledge_document.sub_category_id",json_decode($filters['sub_category']));
                }
                if(isset($filters["status_access"]) && $filters['status_access'] && $filters['status_access'] != "null" && !empty(json_decode($filters['status_access']))) {
                    $db->whereIn("knowledge_document.status",json_decode($filters['status_access']));
                }
                if(isset($filters["star"]) && $filters['star'] && $filters['star'] != "null"  && !empty(json_decode($filters['star']))) {
                    if($filters['star'] == 2) {
                        $db->whereRaw('FIND_IN_SET("'. $current_user->id .'", knowledge_document.starred)');
                    } else {
                        $db->whereNull("knowledge_document.starred");
                    }
                }

                $based_on_possible = ['1'=>'knowledge_document.created_at', '2'=>'knowledge_document.updated_at'];
                if(isset($filters["based_on"]) && $filters['based_on'] && $filters['based_on'] != "null" && $filters['based_on'] >= 1 && $filters['based_on'] <= 2 ) {
                    if(isset($filters["daterange"]) && $filters["daterange"] && $filters["daterange"] != "null") {
                        $daterange = explode(" - ", $filters["daterange"]);
                        $from_date = date("Y-m-d H:i:s", strtotime($daterange[0]));
                        $to_date = date("Y-m-d H:i:s", strtotime($daterange[1]));
                        if($from_date && $to_date) {
                            $whereStr = sprintf('(%1$s >= "%2$s" and %1$s <= "%3$s")', $based_on_possible[$filters['based_on']], $from_date, $to_date);
                            $db->whereRaw($whereStr);
                        }
                    }
                }
                $is_searching = true;
            }

            if( isset($req["search"]) && $req["search"] != '' ) {
                $search_key = $req["search"];
                $db->where(function ($query) use ($search_key) {
                    $query->where('knowledge_document.title', 'like', '%' . $search_key . '%')
                          ->orWhere('knowledge_document_category.category_name', 'like', '%' . $search_key . '%')
                          ->orWhere('knowledge_document.updated_at', 'like', '%' . $search_key . '%');
                    });
                $is_searching = true;
            }

            if($is_searching) {
                $return['filtered'] = $db->count();
            }

            $fields = [1 => 'knowledge_document.id', 2 => 'knowledge_document.content', 3 => 'knowledge_document.title', 4 => 'knowledge_document.parent_category_id', 5 => 'knowledge_document.tags', 6 => 'knowledge_document.card_img', 7 => 'knowledge_document.status'];

            if (isset($req["order_by"]) && array_key_exists($req["order_by"], $fields) && in_array($req["order_dir"], [0, 1])) {
                $dir = $req["order_dir"] == 1 ? "desc" : "asc";
                $db->orderBy($fields[$req["order_by"]], $dir);
            }

            $db->skip($skip);
            $db->take($take);
            $data = $db->get();
            foreach ($data as $key => $kd) {
                $tags = Tags::WhereIn('id', explode(',', $kd->tags))->get();
                $data[$key]['tag'] = $tags;
            }

            $return['current_index'] = (int)$request->index;
            $return['is_prev_index'] = $skip > 0 ? 1 : 0;
            $return['is_next_index'] = $return['filtered'] > ($skip + $take) ? 1 : 0;

            $return["data"] = $data;
        } catch(\Exception $e) {
            Log::error($e->getMessage());
        }
        return response()->json($return);
    }

    public function createDocuments(Request $request) {
        $return = ["status" => "fail", "msg" => trans('fail')];
        $input = $request->all();

        try {
            DB::beginTransaction();
            $rules = [
                'title' => 'required|max:256',
                'status'=>'required',
                'content' => 'required',
                'parent_category_id'=>'required',
                'card_img' =>'sometimes|mimes:jpeg,bmp,png,gif,jpg'
            ];
            $msg = [
                'title.required' => trans('content.knowledge_document.please_add_title'),
                'content.required' => trans('content.knowledge_document.Please_add_content'),
                'tags' => trans('content.knowledge_document.Please_add_tags')
            ];

            $validator = Validator::make($request->all(), $rules, $msg);
            if ($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }
            $kd = new Document();
            $data = $request->only( "title", "content", "status","tags","parent_category_id");
            $kd ->fill($data);
            $kd->card_img = null;
            if($request->file('card_img')) {
                $file = $request->file('card_img');
                $kd = new Document();
                $uploaded_img = $request->card_img;
                $Image = str_random(12) . str_random(12) . '.' . $uploaded_img->getClientOriginalExtension();
                $path = public_path("uploads/article/" . $Image);
                Image::make($uploaded_img->getRealPath())->resize(300, null, function($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->save($path);
                $kd->card_img = $Image;
            }
            $tag1 = $data['tags'];
            $tags = explode(",", $tag1);
            $tagIds = [];
            foreach ($tags as $tag) {
                $tagCheck = Tags::where("id", $tag)->first();
                if(isset($tagCheck) && $tagCheck->count() > 0){
                    array_push($tagIds,$tagCheck->id);
                } else {
                    $tagCreate = Tags::create(["tags" => $tag]);
                    array_push($tagIds,$tagCreate->id);
                }
            }
            $tags = implode(',', $tagIds);
            $data['tags'] = $tags;
            $data['card_img'] = isset($Image)? $Image : NULL;
            $kd = new Document();
            $kd->fill($data);
            $kd->save();
            DB::commit();
            $return["status"] = "success";
            $return["msg"] = trans("content.knowledge_document.article_added");
            return response()->json($return);
        }
        catch(\Exception $e) {
            DB::rollback();
            echo $e;
            Log::error("createDocument error: ". $e->getMessage());
            return response()->json($return);
        }
    }

    public function editDocuments(Request $request,$id) {
        $return = ["status" => "fail", "msg" => trans('fail')];
        $input = $request->all();
        $return = array(
            "status" => "fail",
            "msg" => "Upload has not success"
        );
        try {
            DB::beginTransaction();

            $data = Document::where('id', $id)->first();
            $tag1 = $data->tags;
            $tags = explode(",", $tag1);
            $tagIds = [];
            foreach($tags as $tag) {
                 $tagCheck = Tags::where("id", $tag)->first();
                 if(isset($tagCheck) && $tagCheck->count() > 0) {
                     array_push($tagIds, $tagCheck);
                 } else {
                     $tagCreate = Tags::create(["tags" => $tag]);
                     array_push($tagIds, $tagCreate);
                 }
            }

            $data['tags'] = $tagIds;
            $return['status'] = 'success';
            $return['msg'] = '';
            $return['data'] =  $data;
            DB::commit();
            return response()->json($return);
        }
        catch(\Exception $e) {
            DB::rollback();
            Log::error($e->message());
        }
        return response()->json($return);
    }

    public function updateDocuments(Request $request) {
        $return = ["status" => "fail", "msg" => trans('fail')];
        $input = $request->all();
        try {
            $rules = [
                'title' => 'required|max:256',
//                'tags' =>'required',
                'status'=>'required',
                'content' => 'required',
                'parent_category_id'=>'required',
                'card_img' =>'sometimes|mimes:jpeg,bmp,png,gif'
            ];
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }
            DB::beginTransaction();
            $kd = new Document();
            $data = $request->only( "title", "content", "status","tags","parent_category_id");
            $kd ->fill($data);
            $kd->card_img = null;
            if($request->file('card_img')){
                $file = $request->file('card_img');
                $kd = new Document();
                $uploaded_img = $request->card_img;
                $Image = str_random(12) . str_random(12) . '.' . $uploaded_img->getClientOriginalExtension();
                $path = public_path("uploads/article/" . $Image);
                Image::make($uploaded_img->getRealPath())->resize(300, null, function($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->save($path);
                $kd->card_img = $Image;
            }
            $tagsArray = $data['tags'];
            $tagIds = [];
            foreach($tagsArray as $tag) {
                $tagCheck = Tags::where("id", $tag)->first();

                if(isset($tagCheck) && $tagCheck->count() > 0) {
                    array_push($tagIds, $tagCheck->id);
                } else {
                    $tagCreate = Tags::create(["tags" => $tag]);
                    array_push($tagIds, $tagCreate->id);
                }
            }

            $tags = implode(',', $tagIds);
            $kd = Document::where('id', $request->id)->first();
            $data['tags'] = $tags;
            $data['card_img'] = isset($Image)? $Image : NULL;
            $kd->fill($data);
            $kd->save();
            DB::commit();
            $return['status'] = 'success';
            $return['msg'] = trans("content.knowledge_document.article_update");
            $return['data'] =  $kd;
            Log::info("updateDocuments knowledge document id:" . $kd->id. " user_id:" . Auth::user()->id . " : " . json_encode($request->all()));
        }
        catch(\Exception $e) {
            DB::rollback();
            Log::error("updateDocuments error: " . $e->getMessage());
        }
        return response()->json($return);
    }
    
    public function deleteDocuments(Request $request, $id) {
        $return = array(
            "status" => "failure",
            "msg" => "Unable to delete given details"
        );
        try {
            DB::beginTransaction();
            $kd = Document::where("id","=", $id)->first();
            if(!$kd) {
                return response()->json($return);
            }
            $kd->delete();
            DB::commit();
            $return["status"] = "success";
            $return["msg"] =  trans("content.knowledge_document.article_delete_success");
        }
        catch(\Exception $e) {
            DB::rollback();
            echo($e);
            Log::error($e->message());
        }
        return response()->json($return);
    }

    public function ajaxcategoryList(Request $request) {
        try {
            DB::enableQueryLog();
            $return = ["total"=>0,"filtered"=>0,"data"=>[]];
            $req = $request->all();
            $db = Category::with('subCategory')->where('parent_category_id', 0);
            $return['total'] = $db->count();
            $return['filtered'] = $return['total'];

            $page = $request->input("page", 1);
            $take = $request->input("size", 10);
            $skip = ($page * $take) - $take;
            if($return["filtered"] < $skip) {
                $page = 1;
                $skip = 0;
            }

            $db->skip($skip);
            $db->take($take);
            $return["data"] = $db->get();
            $return["page"] = $page;

        }
        catch(\Exception $e) {
            Log::error($e->getMessage());
        }
        return response()->json($return);
    }

    public function ajaxarticleList(Request $request) {
        try {
            DB::enableQueryLog();
            $return = ["total"=>0,"filtered"=>0,"data"=>[]];
            $req = $request->all();
            $db = Document::select('knowledge_document.id','knowledge_document.content','knowledge_document.title','knowledge_document.updated_at','knowledge_document.card_img','knowledge_document.tags');

            if( isset($req["search"]) && $req["search"] != '' ) {
                $search_key = $req["search"];
                $db->Where('title', 'like', '%' . $search_key . '%');
            }
        
            $return['total'] = $db->count();
            $return['filtered'] = $return['total'];

            $page = $request->input("page", 1);
            $take = $request->input("size", 10);
            $skip = ($page * $take) - $take;
            if($return["filtered"] < $skip) {
                $page = 1;
                $skip = 0;
            }

            $db->skip($skip);
            $db->take($take);
            $data = $db->get();
            foreach($data as $key => $kd){
                $tags = Tags::WhereIn('id',explode(',', $kd->tags))->get();
                $data[$key]['tag'] = $tags;
            }
            $return["data"] = $data;
            $return["page"] = $page;

        }
        catch(\Exception $e) {
            Log::error($e->getMessage());
        }
        return response()->json($return);
    }

    public function createCategory(Request $request) {
        $return = ["status" => "fail", "msg" => trans('fail')];
        $input = $request->all();
        try {
            $rules = [
                'category_name' => 'required|max:256',
                'parent_category_id'=>'required',
                'content' => 'required'
            ];
            $msg = [
                'category_name.required' => trans("content.knowledge_document.Please_add_Category_Name"),
                'content.required' => trans("content.knowledge_document.Please_add_content"),
            ];

            $validator = Validator::make($request->all(), $rules, $msg);
            if ($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }

            DB::beginTransaction();
            $data = $request->only( "category_name", "content","parent_category_id");
            $kc = new Category();
            $kc->fill($data);
            $kc->save();
            DB::commit();
            $return["status"] = "success";
            $return["msg"] = trans("content.knowledge_document.category_add");
            return response()->json($return);
        }
        catch(\Exception $e) {
            DB::rollback();
            Log::error("createCategory error: ". $e->getMessage());
            return response()->json($return);
        }
    }

    public function deleteCategory(Request $request, $id) {
        $return = array(
            "status" => "failure",
            "msg" => "Unable to delete given details"
        );
        try {
            DB::beginTransaction();
            $sc = Category::where("id","=", $id)->first();
            if(!$sc) {
                return response()->json($return);
            }
            $sc->delete();
            DB::commit();
            $return["status"] = "success";
            $return["msg"] = trans("content.knowledge_document.category_delete");
        }
        catch(\Exception $e) {
            DB::rollback();
            echo($e);
            Log::error($e->message());
        }
        return response()->json($return);
    }

    public function editCategory(Request $request,$id) {
        $return = ["status" => "fail", "msg" => trans('fail')];
        $input = $request->all();
        $return = array(
            "status" => "fail",
            "msg" => "Upload has not success"
        );
        try {
            $kc = Category::select('knowledge_document_category.id','knowledge_document_category.category_name','knowledge_document_category.parent_category_id','knowledge_document_category.content')
                ->where('knowledge_document_category.id', '=', $id)->first();

            if($kc) {
                $return['status'] = 'success';
                $return['msg'] = '';
                $return['data'] =  $kc;
            }
        }
        catch(\Exception $e) {
            echo($e);
            Log::error($e->message());
        }
        return response()->json($return);
    }

    public function updateCategory(Request $request,$id) {
        $return = ["status" => "fail", "msg" => trans('fail')];
        $input = $request->all();
        $return = array(
            "status" => "fail",
            "msg" => "Upload has not success"
        );
        try {
            $rules = [
                'category_name' => 'required|max:256',
                'parent_category_id'=>'required',
                'content' => 'required'
            ];
            $msg = [
                'category_name.required' => 'Add Category Name',
                'content.required' => trans("content.knowledge_document.Please_add_content"),
            ];

            $validator = Validator::make($request->all(), $rules, $msg);
            if ($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }
            DB::beginTransaction();
            $data = $request->only( "category_name", "content", "parent_category_id");
            $kc = Category::select('knowledge_document_category.id','knowledge_document_category.category_name','knowledge_document_category.parent_category_id','knowledge_document_category.content')
                ->where('knowledge_document_category.id', '=', $id)->first();
            $kc->fill($data);
            $kc->save();
            DB::commit();
            $return['status'] = 'success';
            $return['msg'] = 'Category Updated Successfully!';
            $return['data'] =  $kc;
        }
        catch(\Exception $e) {
            DB::rollback();
            echo($e);
            Log::error($e->message());
        }
        return response()->json($return);
    }

    public function createParentcategory(Request $request) {
        $return = ["status" => "fail", "msg" => trans('fail')];
        $input = $request->all();
        try {
            $rules = [
                'category_name' => 'required|max:256',
                'content' => 'required'
            ];
            $msg = [
                'category_name.required' => trans("content.knowledge_document.Please_add_Category_Name"),
                'content.required' => trans("content.knowledge_document.Please_add_content"),
            ];

            $validator = Validator::make($request->all(), $rules, $msg);
            if ($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }
            DB::beginTransaction();
            $data = $request->only( "category_name", "content");
            $kc = new Category();
            $kc->fill($data);
            $kc->save();
            DB::commit();
            $return["status"] = "success";
            $return["msg"] = trans("content.knowledge_document.parent_add");
            return response()->json($return);
        }
        catch(\Exception $e) {
            DB::rollback();
            echo $e;
            Log::error("create ParentCategory error: ". $e->getMessage());
            return response()->json($return);
        }
    }

    public function deleteParentcategory(Request $request, $id) {
        $return = array(
            "status" => "failure",
            "msg" => "Unable to delete given details"
        );
        try {
            DB::beginTransaction();
            $kc = Category::where("id","=", $id)->first();
            if(!$kc) {
                return response()->json($return);
            }
            $kc->delete();
            DB::commit();
            $return["status"] = "success";
            $return["msg"] = trans("content.knowledge_document.parent_delete");
        }
        catch(\Exception $e) {
            DB::rollback();
            echo($e);
            Log::error($e->message());
        }
        return response()->json($return);
    }

    public function editParentcategory(Request $request,$id) {
        $return = ["status" => "fail", "msg" => trans('fail')];
        $input = $request->all();
        $return = array(
            "status" => "fail",
            "msg" => "Upload has not success"
        );
        try {
            $kc = Category::select('knowledge_document_category.id','knowledge_document_category.category_name','knowledge_document_category.parent_category_id','knowledge_document_category.content')
                ->where('knowledge_document_category.id', '=', $id)->first();

            if($kc) {
                $return['status'] = 'success';
                $return['msg'] = '';
                $return['data'] =  $kc;
            }
        }
        catch(\Exception $e) {
            echo($e);
            Log::error($e->message());
        }
        return response()->json($return);
    }

    public function updateParentcategory(Request $request,$id) {
        $return = ["status" => "fail", "msg" => trans('fail')];
        $input = $request->all();
        $return = array(
            "status" => "fail",
            "msg" => "Upload has not success"
        );
        try {
            $rules = [
                'category_name' => 'required|max:256',
                'parent_category_id'=>'required',
                'content' => 'required'
            ];
            $msg = [
                'category_name.required' => trans("content.knowledge_document.Please_add_Category_Name"),
                'content.required' => trans("content.knowledge_document.Please_add_content"),
            ];

            $validator = Validator::make($request->all(), $rules, $msg);
            if ($validator->fails()) {
                $v = $validator->errors()->toArray();
                $e = array_shift($v);
                $return["msg"] = $e[0];
                return response()->json($return);
            }
            DB::beginTransaction();
            $data = $request->only( "category_name", "content", "parent_category_id");
            $kc = Category::select('knowledge_document_category.id','knowledge_document_category.category_name','knowledge_document_category.parent_category_id','knowledge_document_category.content')
                ->where('knowledge_document_category.id', '=', $id)->first();
            $kc->fill($data);
            $kc->save();
            DB::commit();
            $return['status'] = 'success';
            $return['msg'] = trans("content.knowledge_document.parent_update");
            $return['data'] =  $kc;
        }
        catch(\Exception $e) {
            DB::rollback();
            echo($e);
            Log::error($e->message());
        }
        return response()->json($return);
    }

    public function ajaxPublicDocumentList(Request $request) {
        try {
            DB::beginTransaction();
            $return = ["total"=>0,"filtered"=>0,"data"=>[]];
            $req = $request->all();
            
            $db = Document::select('knowledge_document.id','knowledge_document.title','kc.category_name as category_name','knowledge_document.tags','knowledge_document.content','knowledge_document.updated_at','knowledge_document.card_img');            
            $db->leftJoin('knowledge_document_category as kc', 'knowledge_document.parent_category_id', '=', 'kc.id');
            $db->where("knowledge_document.status", 1);
            $db->whereNull('knowledge_document.deleted_at');

            if(!empty($req['category'])) {
                $db->whereIn('knowledge_document.parent_category_id', $req['category']);
            }
           
            if( isset($req["search"]) && $req["search"] != '' ) {
                $search_key = $req["search"];
                $db->Where('title', 'like', '%' . $search_key . '%')
                    ->orWhere('category_name', 'like', '%' . $search_key . '%');
            }
        
            $return['total'] = $db->count();
            $return['filtered'] = $return['total'];

            $page = $request->input("page", 1);
            $take = $request->input("size", 12);
            $skip = ($page * $take) - $take;
            if($return["filtered"] < $skip) {
                $page = 1;
                $skip = 0;
            }
            $db->skip($skip);
            $db->take($take);
            $data = $db->get();
            foreach($data as $key => $kd){
                $tags = Tags::WhereIn('id',explode(',', $kd->tags))->get();
                $data[$key]['tag'] = $tags;
            }
            DB::commit();
            $return["data"] = $data;
            $return["page"] = $page;
        }
        catch(\Exception $e) {
            DB::rollback();
            echo($e);
            Log::error($e->message());
        }
        return response()->json($return);
    }

    public function ajaxArticleDetails(Request $request) {
        $return = ["status" => "fail", "msg" => trans('fail')];
        $rules = [
            'id' => 'required|exists:knowledge_document,id',
        ];
        $msg = [];

        $validator = Validator::make($request->all(), $rules, $msg);
        if ($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        if(! Auth::user()->hasPermissionTo('KnowledgeDocumentArticleView')) {
            $return["msg"] = trans('content.user_fields.Permission_denied');
            return response()->json($return);
        }
        $attachment_path = KDAttachment::getUrl('');
        $attachment_view = KDAttachment::getViewUrl();
        $kd = Document::select('knowledge_document.id', 'knowledge_document.title', 'knowledge_document.content','pc.name as category', 'knowledge_document.tags',DB::raw('DATE_FORMAT(knowledge_document.created_at, "%d %b %Y %h:%i %p") as created_at_format'), DB::raw('DATE_FORMAT(knowledge_document.updated_at, "%d %b %Y %h:%i %p") as updated_at_format'))
            ->leftJoin('tkt_problem_categories as pc', 'knowledge_document.parent_category_id', '=', 'pc.id')
            ->where('knowledge_document.id', $request->id)->first();

        $tags = Tags::select('tags')->whereIn('knowledge_document_tags.id',explode(',',$kd->tags))->get();
        $kd->attachments = KDAttachment::where('kd_id','=',$kd->id)->whereNull('following_id')->select('id', 'original_file_name as name', 'extension as ext',DB::raw('case when id is not null then concat_ws("", "'.$attachment_path.'/",id) else "" end as attach_file_path'),DB::raw('case when id is not null then concat_ws("", "'.$attachment_view.'/",id) else "" end as attach_view'), DB::raw('case when thumbnail is not null then 1 else 0 end as thumb'))->get();
        $return['data'] = $kd;
        $return['tags'] = $tags;
        $return['status'] = "success";
        $return['msg'] = "";
        return response()->json($return);
    }

    public function KnowledgeDocumentAttachment(Request $request) {
        $return = array("status" => "fail", "msg" => "Upload has not success");

        $rules = [
            'kd_id' => 'required|integer|exists:kd_attachments,id',
            'attachment' => 'required|file',
            'tmp_id' => 'sometimes|nullable|string|max:20'
        ];

        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        if(! $request->hasFile('attachment') || !$request->file('attachment')->isValid()) {
            return response()->json($return);
        }

        $file = $request->file('attachment');

        $filePath = date("Y").'/'.date('m').'/'.date('d');
        $checkFolderPath = CommonHelper::attachmentFolderStructure('kd_attach', $filePath);
        if(!$checkFolderPath) {
            return response()->json($return);
        }
        $a = new KDAttachment();
        $a->kd_id = $request->ticket_id;
        $a->original_file_name = $file->getClientOriginalName();
        $a->extension = $file->getClientOriginalExtension();
        $a->file_name = $request->file('attachment')->store($filePath, "kd_attach");
        $a->uploader_id = Auth::user()->id;
        $a->tmp_id = $request->input("tmp_id", null);

        if(!$a->file_name || !$a->save()) {
            return response()->json($return);
        }

        $return["data"] = $a->only("id", "original_file_name");
        $return["status"] = "success";
        $return["msg"] = "";
        return response()->json($return);
    }

    /* to remove the attachment */
    public function KnowledgeDocumentAttachmentRemove(Request $request) {
        $return = array("status" => "fail", "msg" => "Unable to remove attachment");

        $rules = [
            'id' => 'required|integer|exists:kd_attachments,id'
        ];

        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()) {
            $v = $validator->errors()->toArray();
            $e = array_shift($v);
            $return["msg"] = $e[0];
            return response()->json($return);
        }

        $a = KDAttachment::find($request->id);
        Storage::disk('kd_attach')->delete($a->file_name);
        $a->delete();

        $return["status"] = "success";
        $return["msg"] = "Attachment removed";
        return response()->json($return);
    }

    public function download($id) {
        $a = KDAttachment::find($id);
        if(!$a || !Storage::disk('kd_attach')->exists($a->file_name)) {
            return redirect("/");
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'. $a->original_file_name .'"');
        echo Storage::disk('kd_attach')->get($a->file_name);
    }
}
