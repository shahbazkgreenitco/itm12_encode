<?php

namespace App\Http\Controllers\Auth\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Response;
use Validator;
use Auth;
use DB;
use App\Models\StatusBoard;
use App\Models\StatusBoardGroup;
use App\Models\StatusBoardItem;
use App\Models\StatusBoardIncident;
use App\Helpers\Old;

class StatusBoardController extends Controller
{
    public function getIndex()
    {
        $groups = StatusBoardGroup::where('is_enabled', '=', 1)->get();
        
        $group = array();
        $incidents = array();
        
        $SB = new StatusBoard;

        $board_info = DB::select("select count(*) as tot_items, IFNULL(sum(case when status = 1 then 1 else 0 end), 0) as tot_active, IFNULL(sum(case when status = 2 then 1 else 0 end), 0) as tot_per_iss, IFNULL(sum(case when status = 3 then 1 else 0 end), 0) as tot_par_out, IFNULL(sum(case when status = 4 then 1 else 0 end), 0) as tot_major from status_board_items where is_enabled = 1");
        $board_info = DB::select("select count(*) as tot_items, IFNULL(sum(case when status = 1 then 1 else 0 end), 0) as tot_active, sum(case when status = 2 then 1 else 0 end) as tot_per_iss, sum(case when status = 3 then 1 else 0 end) as tot_par_out, sum(case when status = 4 then 1 else 0 end) as tot_major from status_board_items where is_enabled = 1");
        // $dashboard_details = $SB->getDashboardTots();
        foreach($groups as $grp)
        {
            $items = StatusBoardItem::select('id','title','status')->where('group_id', '=', $grp->id)->where('is_enabled', '=', 1)->get();
            $group[] = array(
                'title' => $grp->title,
                'items' => $items,
                'total_items' => count($items)
            );
        }
        
        for($i=0; $i>-7; $i--)
        {
            $date = date('Y-m-d', strtotime("{$i} days"));
            $incs = $SB->getIncidentsByDate($date);
            $incidents[] = array(
                'date' => date('jS M Y', strtotime("{$i} days")),
                'entries' => $incs
            );
        }

        return response()->json([
            "status"    => 'success',
            "msg"       => '',
            "data"      => ['group' => $group,'incedents' => $incidents,'IiemStatus' => Old::status_board_component_statuses(),'dashboard_details'=> $board_info]
            ]);
    }

    public function publicStatusboard()
    {
        // $user = Sentry::getUser();
        $groups = StatusBoardGroup::where('is_enabled', '=', 1)->get();
        
        $group = array();
        $incidents = array();
        
        $SB = new StatusBoard;
        // $dashboard_details = $SB->getDashboardTots();
        $board_info = DB::select("select count(*) as tot_items, IFNULL(sum(case when status = 1 then 1 else 0 end), 0) as tot_active, IFNULL(sum(case when status = 2 then 1 else 0 end), 0) as tot_per_iss, IFNULL(sum(case when status = 3 then 1 else 0 end), 0) as tot_par_out, IFNULL(sum(case when status = 4 then 1 else 0 end), 0) as tot_major from status_board_items where is_enabled = 1");
        foreach($groups as $grp)
        {
            $items = StatusBoardItem::where('group_id', '=', $grp->id)->where([['is_enabled', '=', 1],['is_public','=',1]])->get();
            $group[] = array(
                'title' => $grp->title,
                'items' => $items,
                'total_items' => count($items)
            );
        }
        
        for($i=0; $i>-7; $i--)
        {
            $date = date('Y-m-d', strtotime("{$i} days"));
//            $incs = StatusBoardIncident::where('created_at', 'like', $date . '%')->where('is_enabled', '=', 1)->orderBy('id', 'desc')->get();
            $incs = $SB->getPublicIncidentsByDate($date);
            $incidents[] = array(
                'date' => date('jS M Y', strtotime("{$i} days")),
                'entries' => $incs
            );
        }

        return response()->json([
            "status"    => 'success',
            "msg"       => '',
            "data"      => ['group' => $group,'incedents'=> $incidents,'IiemStatus' => Old::status_board_component_statuses(),'dashboard_details'=> $board_info]
            ]);
        
        
        // return view("statusBoard.public-status-board")
        //     ->with('groups', $group)
        //     ->with('comp_statuses', Old::status_board_component_statuses())
        //     ->with('inci_statuses', Old::status_board_incident_statuses())
        //     ->with('inci_statuses_char', Old::status_board_incident_statuses_char())
        //     ->with('incidents', $incidents);
    }

    public function getItem()
    {
        return View('statusBoard.item')->withItems(StatusBoardItem::all());
    }

    public function getNewItem()
    {
        return View('statusBoard.edit-item')
            ->withItem(new StatusBoardItem)
            ->withCompStatus(Old::status_board_component_statuses())
            ->withGroups(StatusBoardGroup::where(array('is_enabled'=>1))->pluck('title', 'id'));
    }

    
    public function postNewItem(Request $request)
    {
        $data = $request->all();
        $sbi = new StatusBoardItem;
        
        $sbi->title = $data['title'];
        $sbi->status = $data['status'];
        $sbi->is_enabled = isset($data['is_enabled']) && $data['is_enabled']==1 ? 1 : 0;
        $sbi->group_id = $data['group_id'];
        
        $validator = Validator::make($data, array(
            'title' => 'required|string|min:1|max:80|unique:status_board_items,title',
            'status' => 'required|numeric',
            'group_id' => 'required|numeric'
        ));
        
        if($validator->fails())
        {
            return back()->withInput()->withErrors($validator);
        }
        
        if( $sbi->save() )
        {
            return Redirect('status-board-item-new')->with('success', 'New item has been stored successfully.');
        }
        
        return Redirect('status-board-item-new')->with('error', 'Something went wrong. Try again with valid values.');
    }

    public function getEditItem($item_id)
    {
        $record = StatusBoardItem::find($item_id);
        if(empty($record))  return redirect('status-board-items');

        return View('statusBoard.edit-item')
            ->with('item', $record )
            ->with('compStatus', Old::status_board_component_statuses())
            ->with('groups', StatusBoardGroup::where(array('is_enabled'=>1))->pluck('title', 'id'));
    }
    
    public function postEditItem(Request $request,$item_id)
    {
        $data = $request->all();
        $sbi = StatusBoardItem::find($item_id);
        
        $sbi->title = $data['title'];
        $sbi->status = $data['status'];
        $sbi->is_enabled = isset($data['is_enabled']) && $data['is_enabled']==1 ? 1 : 0;
        $sbi->group_id = $data['group_id'];
        $sbi->is_public = $data['is_public'];
        
        $validator = Validator::make($data, array(
            'title'         => 'required|string|min:1|max:80',
            'status'        => 'required|numeric',
            'group_id'      => 'required|numeric',
            'is_public'     => 'required|min:0|max:1',
        ));
        
        if($validator->fails())
        {
            return back()->withInput()->withErrors($validator);
        }
        
        if( $sbi->save() )
        {
            return Redirect('status-board-item-edit/' . $item_id)->with('success', 'Item has updated successfully.');
        }
        
        return Redirect('status-board-item-edit/' . $item_id)->with('error', 'Something went wrong. Try again with valid values.');
    }

    public function deleteItem($id){
        // $splr = Supplier::where('id','=',$id)->withCount(['assets','asset_maintenances','licenses']);
        $record = StatusBoardItem::where('id','=',$id)->withCount(['incident'])->first();

        if(empty($record))
            return response()->json(['status' => 'error', 'msg' => 'Some problem in system!!']);

        // if( !Auth::user()->isSuperUser() && !Company::checkUserAccess($record) )
            // return response()->json(['status' => 'error', 'msg' => 'Insufficient permission !!']);

        if($record->incident_count)
            return response()->json(['status' => 'error', 'msg' => 'Some incidents are attached with this item . System is unable to delete this item !!']);

        $record->delete();
            return response()->json(['status' => 'success', 'msg' => 'Item has deleted successfully!']);
    }

    public function deleteGroup($id){
        // $splr = Supplier::where('id','=',$id)->withCount(['assets','asset_maintenances','licenses']);
        $record = StatusBoardGroup::where('id','=',$id)->withCount(['item'])->first();


        // if( !Auth::user()->isSuperUser() && !Company::checkUserAccess($record) )
            // return response()->json(['status' => 'error', 'msg' => 'Insufficient permission !!']);
        if(empty($record))
            return response()->json(['status' => 'error', 'msg' => 'Some problem in system!!']);
        if($record->item_count)
            return response()->json(['status' => 'error', 'msg' => 'Some items are attached with this group . System is unable to delete this group !!']);

        $record->delete();
            return response()->json(['status' => 'success', 'msg' => 'Group has deleted successfully!']);
    }

    public function getGroups()
    {
        return view("statusBoard.groups")->withGroups(StatusBoardGroup::all());
    }
    
    public function getNewGroup()
    {
        return view("statusBoard.edit-group")->withGroup(new StatusBoardGroup);
    }
    
    public function postNewGroup(Request $request)
    {
        $data = $request->all();
        
        $sbg = new StatusBoardGroup;
        
        $sbg->title = $data['title'];
        $sbg->is_enabled = isset($data['is_enabled']) && $data['is_enabled']==1 ? 1 : 0;
        
        $validator = Validator::make($data, array( 
            'title' => 'required|string|min:1|max:80|unique:status_board_groups,title',
        ));
        
        if($validator->fails())
        {
            return back()->withInput()->withErrors($validator);
            // return Redirect::back()->withInput()->withErrors($validator);
        }
        
        unset($data['_token']);
        
        if( $sbg->save($data) )
        {
            return redirect('status-board-groups-new')->with('success', 'New group has been stored successfully.');;
            // return Redirect('admin/status-board/groups')->with('success', 'New group has been stored successfully.');
        }
        
        return Redirect('status-board-groups-new')->with('error', 'Something went wrong. Try again with valid values.');
    }
    
    public function getEditGroup($group_id)
    {
        $record = StatusBoardGroup::find($group_id);
            if(empty($record))  return redirect('status-board-groups');
        return View('statusBoard.edit-group')->withGroup($record);
    }
    
    public function postEditGroup(Request $request,$group_id)
    {
        $data = $request->all();
        
        $sbg = StatusBoardGroup::find($group_id);

        $sbg->title = $data['title'];
        $sbg->is_enabled = isset($data['is_enabled']) && $data['is_enabled']==1 ? 1 : 0;
        
        $validator = Validator::make($data, array(
            'title' => 'required|string|min:1|max:80'
        ));
        
        if($validator->fails())
        {
            return back()->withInput()->withErrors($validator);
        }
        
        unset($data['_token']);
        
        if( $sbg->save() )
        {
            return Redirect('status-board-groups-edit/'.$group_id)->with('success', 'Group has updated successfully.');
        }
        
        return Redirect('status-board-groups-edit/'.$group_id)->with('error', 'Something went wrong. Try again with valid values.');
    }

    public function getNewIncident()
    {
        $sb = new StatusBoard;
        $groups = $sb->getGroups();
        $items = $sb->getItemsLists();
        $group_items = Old::group_wise_order_items($groups, $items);
        return View('statusBoard.edit-incident')
            ->with('incident', new StatusBoardIncident)
            ->with('inci_status', Old::status_board_incident_statuses())
            ->with('group_items', $group_items);
    }

    public function getAvailableItemStatus($item_id)
    {
        $sb = new StatusBoard;
        
        $get_item = $sb->getItems('is_enabled = 1 and id = '. $item_id, ['status']);
        
        $cur_status = '';
        if(count($get_item)) {
            $cur_status = $get_item[0]->status;
        }
        
        $statuses = Old::status_board_component_statuses();
        
        return Response::json([
            'cur_status' => $cur_status,
            'status_opts' => $statuses
        ]);
    }

    public function postNewIncident(Request $request)
    {
        $data = $request->all();
        
        $validator = Validator::make($data, array(
            'subject' => 'required|string|min:1|max:80',
            'note' => 'required|string|min:1|max:500',
            'status' => 'required|numeric',
            'item_status' => 'required|numeric',
            'item_id' => 'numeric'
        ));
        
        if($validator->fails())
        {
            return back()->withInput()->withErrors($validator);
        }
        
        $sb = new StatusBoard;

        if( $sb->addIncident($data) ) 
        {
            $sb->updateItem(['status' => $data['item_status']], $data['item_id']);
            return Redirect('status-board-incident-new')->with('success', 'New incident has been created successfully.');
        }
        
        return Redirect('status-board-incident-new')->with('error', 'Something went wrong. Try again with valid values.');
    }

    // FU: Jul 19 2016
    public function getOpenIncidents()
    {
        $sb = new StatusBoard;
        $incidents = $sb->getOpenIncidents();
        return View('statusBoard.open-incidents')
        ->with('inci_statuses', Old::status_board_incident_statuses())
        ->with('incidents', $incidents);
    }

    public function getEditIncident($incident_id)
    {
        $sb = new StatusBoard;
        $getIncident = $sb->getIncident($incident_id);
        

        if(empty( $getIncident[0])) {
            return Redirect('status-board-open-incidents')->with('error', 'Incident not found');
        }

        $incident = $getIncident[0];
        
//        $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $incident[0]->updated_at);
//        $datetime1 = new DateTime;
//        echo $datetime->format('Y-m-d h:i a');
//        echo '<br/>';
//        echo $datetime1->format('Y-m-d h:i a');
        
        $groups = $sb->getGroups();
        $items = $sb->getItemsLists();
        $group_items = Old::group_wise_order_items($groups, $items);
        
        return View('statusBoard.edit-incident')
            ->with('incident', $incident)
            ->with('inci_status', Old::status_board_incident_statuses())
            ->with('group_items', $group_items);
    }

    public function postEditIncident(Request $request,$incident_id)
    {
        $data = $request->all();
        
        $validator = Validator::make($data, array(
            'note' => 'required|string|min:1|max:500',
            'status' => 'required|numeric',
            'item_status' => 'required|numeric',
            'item_id' => 'numeric'
        ));
        
        if($validator->fails())
        {
            return back()->withInput()->withErrors($validator);
        }
        
        if($data['id'] != $incident_id) {
            return Redirect('status-board-open-incidents')->with('error', 'Invalid Access');
        }
        
        $sb = new StatusBoard;
        $getIncident = $sb->getIncident($incident_id);
        
        if(! $getIncident) {
            return Redirect('status-board-open-incidents')->with('error', 'Incident not found');
        }

        $incident = $getIncident[0];
        $date = date('Y-m-d H:i:s');

        $part1 = array(
            'note' => $data['note'],
            'status' => $data['status'],
            'created_at' => $date
        );

        $part2 = array(
            'status' => $data['status'],
            'updated_at' => $date
        );
        
        if( $sb->saveHistoryEdits($part1, $incident->history_id) && $sb->saveIncidentEdits($part2, $incident_id) )
        {
            $sb->updateItem(['status' => $data['item_status']], $incident->item_id);
            return Redirect('status-board-open-incidents')->with('success', 'IncidentEdits has been saved successfully.');
        }
        
        return Redirect('status-board-open-incidents')->with('error', 'Something went wrong. Try again with valid values.');
    }


    public function getIncidentHistory($id)
    {
        $sb = new StatusBoard;
        $history = $sb->getIncidentHistory($id);

        // print_r($history);        die;
        $incident = $sb->getIncidentOnly($id);
        $inci_statuses = Old::status_board_incident_statuses();
        $inci_statuses_char = Old::status_board_incident_statuses_char();
        return View('statusBoard.incedent-history')->with(compact('history', 'incident', 'inci_statuses', 'inci_statuses_char'));
    }

    public function getFixedIncidents()
    {
        $sb = new StatusBoard;
        $incidents = $sb->getFixedIncidents();

        // print_r($incidents);die;

        return View('statusBoard.fixed-incidents')
        ->with('inci_statuses', Old::status_board_incident_statuses())
        ->with('incidents', $incidents);
    }

    
    public function getUpdateIncident($incident_id)
    {
        $sb = new StatusBoard;
        $getIncident = $sb->getIncident($incident_id);
        
        if(! $getIncident) {
            return Redirect('status-board-open-incidents')->with('error', 'Incident not found');
        }

        $incident = $getIncident[0];
        
        $groups = $sb->getGroups();
        $items = $sb->getItemsLists();
        $group_items = Old::group_wise_order_items($groups, $items);
        
        return View('statusBoard.update-incident')
            ->with('incident', $incident)
            ->with('inci_status', Old::status_board_incident_statuses())
            ->with('group_items', $group_items);
    }


    public function postUpdateIncident(Request $request,$incident_id)
    {
        $data = $request->all();

        $validator = Validator::make($data, array(
            'note' => 'required|string|min:1|max:500',
            'status' => 'required|numeric',
            'id' => 'required|numeric',
            'item_status' => 'required|numeric'
        ));
        
        if($validator->fails())
        {
            return back()->withInput()->withErrors($validator);
        }
        
        if($data['id'] != $incident_id) {
            return Redirect('status-board-open-incidents')->with('error', 'Invalid Access');
        }
        
        $sb = new StatusBoard;
        $getIncident = $sb->getIncident($incident_id);
        
        if(! $getIncident) {
            return Redirect('status-board-open-incidents')->with('error', 'Incident not found');
        }
    
        $incident = $getIncident[0];
        $date = date('Y-m-d H:i:s');

        // add new incident event in history
        $part1 = array(
            'iid' => $incident->id,
            'note' => $data['note'],
            'status' => $data['status'],
            'created_at' => $date
        );

        $hid = $sb->addIncidentHistory($part1);

        if(! $hid) {
            return Redirect('status-board-open-incidents')->with('error', 'Something went wrong. Please try again.');
        }

        // update incident main record in incident table 
        $part2 = array(
            'status' => $data['status'],
            'history_id' => $hid,
            'updated_at' => $date
        );

        if( $sb->saveIncidentEdits($part2, $incident->id) ) {
            $sb->updateItem(['status' => $data['item_status']], $incident->item_id);
            return Redirect('status-board-open-incidents')->with('success', 'Incident Status has updated successfully.');    
        }
        return Redirect('status-board-open-incidents')->with('error', 'Something went wrong. Please try again.');
    }


    public function getArchivedIncidents()
    {
        $sb = new StatusBoard;
        $incidents = $sb->getArchivedIncidents();
        return View('statusBoard.archived-incedent')
        ->with('inci_statuses', Old::status_board_incident_statuses())
        ->with('incidents', $incidents);
    }

}
