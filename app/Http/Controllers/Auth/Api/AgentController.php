<?php

namespace App\Http\Controllers\Auth\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Common as CommonHelper;
use App\Models\Agent;
use App\Models\Device;
use Storage;
use DB;

class AgentController extends Controller
{
    public function ajaxAgentClientList(Request $request) {
        try {
            $req = $request->all();
            $return = [
                'status' => 'fail',
                "draw" => date('is'),
                'msg' => 'Unable to get Agent',
            ];
    
            $fields = array(
                '0' => 'agent_one_place.agent_name',
                '1' => 'agent_one_place.os',
                '2' => 'agent_one_place.version',
                '5' => 'agent_one_place.updated_at',
            );
    
            $obj = Agent::select('agent_one_place.id','agent_one_place.agent_name','os.os_name','os.os_type','agent_one_place.version','agent_one_place.description','agent_one_place.original_file_name','agent_one_place.link','agent_one_place.file','agent_one_place.updated_at','agent_one_place.created_at','agent_one_place.uploaded_type',
            'agent_one_place.disable_days','agent_one_place.form_time','agent_one_place.to_time','agent_one_place.hour','agent_one_place.minute','agent_one_place.monthly','agent_one_place.update_hour',
            'agent_one_place.monthly_date','agent_one_place.monthly_numbers','agent_one_place.monthly_week','agent_one_place.monthly_day','agent_one_place.yearly','agent_one_place.yearly_months','agent_one_place.expiry_date',
            'agent_one_place.yearly_day','agent_one_place.yearly_months_week','agent_one_place.yearly_months_week_day','agent_one_place.yearly_months2','agent_one_place.retry','agent_one_place.monthly_month','agent_one_place.update_days','agent_one_place.device_id','agent_one_place.status',
            )
            ->leftJoin('agent_os_support as os', 'agent_one_place.os', '=', 'os.id');
            $obj->addSelect(DB::raw('case when agent_one_place.auto_check_type = 1 then "update" when agent_one_place.auto_check_type = 2 then "disable"  else "" end as auto_check_type'));
            $obj->addSelect(DB::raw('case when agent_one_place.disable_type = 1 then "permanent disable" when agent_one_place.disable_type = 2 then "remove agent"  when agent_one_place.disable_type = 3 then "remove agent if inactive for more than x day" when agent_one_place.disable_type = 4 then "form x-to x time" else "" end as disable_type'));
            $obj->addSelect(DB::raw('case when agent_one_place.update_type = 1 then "every time" when agent_one_place.update_type = 2 then "schedular"  when agent_one_place.update_type = 3 then "after x hrs" else "" end as update_type'));
            $obj->addSelect(DB::raw('case when agent_one_place.schedular_plan = 1 then "Weekly" when agent_one_place.schedular_plan = 2 then "Monthly"  when agent_one_place.schedular_plan = 3 then "Yearly" else "" end as schedular_plan'));
            $return['recordsTotal'] = $obj->count();
            $return['recordsFiltered'] = $return['recordsTotal'];
            $is_searching = false;
            if(isset($req["filters"])) {
                $filters = $req["filters"];
    
                $based_on_possible = ['1'=>'agent_one_place.created_at', '2'=>'agent_one_place.updated_at'];
                if(isset($filters["based_on"]) && $filters['based_on'] && $filters['based_on'] != "null" && $filters['based_on'] >= 1 && $filters['based_on'] <= 2 ) {
                    if(isset($filters["from_date"]) && $filters["from_date"] && $filters["from_date"] != "null") {
                        $from_date = CommonHelper::getDateAs($filters["from_date"], "Y-m-d", "d/m/Y");
                        $to_date = CommonHelper::getDateAs($filters["to_date"], "Y-m-d", "d/m/Y");
                        if($from_date && $to_date) {
                            $whereStr = sprintf('(date(%1$s) >= "%2$s" and date(%1$s) <= "%3$s")', $based_on_possible[$filters['based_on']], $from_date, $to_date);
                            $obj->whereRaw($whereStr);
                        }
                    }
                }
                $is_searching = true;
            }
            if (isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"])) {
    
                $whereStr = sprintf('(agent_one_place.agent_name like "%%%1$s%%" or os.os_name like "%%%1$s%%" or  agent_one_place.version like "%%%1$s%%" or  agent_one_place.original_file_name like "%%%1$s%%")', $search_key);
                $obj->whereRaw($whereStr);
                $is_searching = true;
            }
    
            if ($is_searching) {
                $return['recordsFiltered'] = $obj->count();
            }
    
            if (isset($req["order"][0]["column"]) && isset($fields[$req["order"][0]["column"]]) && in_array($req["order"][0]["dir"], ["asc", "desc"])) {
                $obj->orderBy($fields[$req["order"][0]["column"]], $req["order"][0]["dir"]);
            }
    

            $take = 10;
            $page = $request->input("page", 1);
            $take = $request->input("size", 10);
            $skip = ($page * $take) - $take;
            if( isset($req["start"]) && isset($req["length"]) ) {
                $skip = (int) $req["start"];
                $take = (int) $req["length"];
            }
    
            $obj->skip($skip);
            $obj->take($take);
    
            $obj = $obj->get();

            foreach ($obj as &$item) {
                if($item->uploaded_type != 1){
                    // $item->file_path = asset('storage/agents/'.$item->file);
                    $item->file_path = url('api/jx-agent-list-client-download/' . $item->id);
                }
                if($item->update_days != null){
                    $item->update_days  = json_decode($item->update_days);
                } 
                $arraySerial = [];
                if($item->device_id != null){
                    $getDevice = Device::whereIn('id', explode(',', $item->device_id))->select('serial')->get();
                    foreach ($getDevice as $key => $value) {
                      $arraySerial[] = $value->serial;
                    }
                    $item->device_id = $arraySerial;
                }
            }
    
            $return['data'] = $obj;
    
            $return['status'] = 'success';
            $return['msg'] = 'Agent fetched successfully.';
            return response()->json($return);
        } catch(\Exception $e) {
            Log::error("ajaxAgentClientList() error : " . $e->getMessage());
            return response()->json($return);
        }
    }

    public function ajaxAgentExpiryClientList(Request $request) {
        try {
            $req = $request->all();
            $return = [
                'status' => 'fail',
                "draw" => date('is'),
                'msg' => 'Unable to get Agent',
            ];
    
            $fields = array(
                '0' => 'agent_one_place.agent_name',
                '1' => 'agent_one_place.version',
            );
    
            $obj = Agent::select('agent_one_place.id','agent_one_place.agent_name','agent_one_place.version','agent_one_place.expiry_date',)
            ->leftJoin('agent_os_support as os', 'agent_one_place.os', '=', 'os.id');

            $return['recordsTotal'] = $obj->count();
            $return['recordsFiltered'] = $return['recordsTotal'];
            $is_searching = false;
            if (isset($req["search"]["value"]) && $search_key = trim($req["search"]["value"])) {
                $whereStr = sprintf('(agent_one_place.agent_name like "%%%1$s%%" or os.os_name like "%%%1$s%%" or  agent_one_place.version like "%%%1$s%%")', $search_key);
                $obj->whereRaw($whereStr);
                $is_searching = true;
            }
    
            if ($is_searching) {
                $return['recordsFiltered'] = $obj->count();
            }
    
            if (isset($req["order"][0]["column"]) && isset($fields[$req["order"][0]["column"]]) && in_array($req["order"][0]["dir"], ["asc", "desc"])) {
                $obj->orderBy($fields[$req["order"][0]["column"]], $req["order"][0]["dir"]);
            }
    

            $take = 10;
            $page = $request->input("page", 1);
            $take = $request->input("size", 10);
            $skip = ($page * $take) - $take;
            if( isset($req["start"]) && isset($req["length"]) ) {
                $skip = (int) $req["start"];
                $take = (int) $req["length"];
            }
    
            $obj->skip($skip);
            $obj->take($take);
    
            $obj = $obj->get();
    
            $return['data'] = $obj;
    
            $return['status'] = 'success';
            $return['msg'] = 'Agent fetched successfully.';
            return response()->json($return);
        } catch(\Exception $e) {
            Log::error("ajaxAgentClientList() error : " . $e->getMessage());
            return response()->json($return);
        }
    }


    public function ajaxAgentDownload($id) {
        $ad = Agent::find($id);
        if (!$ad || !Storage::disk('agent')->exists($ad->file)) {
            $return = ['status' => 'fail', 'msg' => "The specified file doesn't exist on the server."];
            return response()->json($return);
        }
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'. $ad->original_file_name .'"');
        echo Storage::disk('agent')->get($ad->file);
    }
}
