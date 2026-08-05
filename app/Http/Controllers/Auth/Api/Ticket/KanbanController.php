<?php

namespace App\Http\Controllers\Auth\Api\Ticket;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket\KanbanBoard;
use App\Models\Ticket\ProblemCategory;
use App\Models\Ticket\KanbanBoardItem;
use App\Models\Ticket\Status;
use App\Models\Ticket\Priority;
use App\Models\Ticket\CustomBoardItemTicket;
use App\Models\User;
use App\Models\Ticket\Ticket;
use App\Helpers\Common as CommonHelper;
use DB;
use Auth;
use Log;
use Storage;
use Validator;

class KanbanController extends Controller
{
    public function apiGetAccessBoardName(Request $request){
        try {
            if (!Auth::user()->hasPermissionTo('KanbanMyBoards')) {
                return response()->json([
                    'status'  => false,
                    'message' => trans('content.user_fields.Permission_denied')
                ], 403);
            }

            $userId = Auth::id();
            $companyIds = CommonHelper::getSelectedCompanyIds();

            $kanbanLists = KanbanBoard::select('kanban_boards.*')
                ->leftJoin('users', 'users.id', 'kanban_boards.created_by')
                ->orderBy('kanban_boards.updated_at', 'DESC')
                ->where(function ($query) use ($userId) {
                    $query->where('kanban_boards.created_by', $userId)
                        ->orWhereExists(function ($q) use ($userId) {
                            $q->select(DB::raw(1))
                                ->from('kanban_board_members')
                                ->whereRaw('kanban_board_members.board_id = kanban_boards.id')
                                ->where('kanban_board_members.user_id', $userId)
                                ->whereNull('kanban_board_members.deleted_at');
                        });
                });

            if (config('services.multiple_company.enabled')) {
                $kanbanLists->whereIn('kanban_boards.company_id', $companyIds);
            } else {
                $kanbanLists->where('kanban_boards.company_id', Auth::user()->company_id);
            }
            $kanbanLists = $kanbanLists->get();
            if ($kanbanLists->isEmpty()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'No Kanban boards available for this user.'
                ], 404);
            }
            return response()->json([
                'status' => true,
                'data'   => $kanbanLists
            ]);
        } catch (\Exception $e) {
            Log::error("apiGetAccessBoardName() error : " . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => trans('ticket.service_ticket_fields.something_went_wrong')
            ], 500);
        }
    }

    public function ajaxBoardList(Request $request) {
        $return = ['status' => 'failure', 'msg' => trans('ticket.service_ticket_fields.something_went_wrong')];

        try {
            $request_filters = json_decode(base64_decode($request->filters), true) ?? [];
            $filters = isset($request_filters['other_filters']) ? $request_filters['other_filters'] : [];
            Config()->set('database.connections.mysql.strict', false);
            DB::reconnect();
            $kanban_board = KanbanBoard::where('id', $request->id)->first(['department_id', 'problem_category_id', 'sub_category_id','company_id']);
            $department_ids = $kanban_board['department_id']? array_map('trim', explode(',', $kanban_board['department_id'])): [];
            $problem_category_ids = $kanban_board['problem_category_id'] ? array_map('trim',explode(',',$kanban_board['problem_category_id'])) : [];
            $sub_category_ids = $kanban_board['sub_category_id'] ? array_map('trim',explode(',',$kanban_board['sub_category_id'])) : [];
            
            $category_ids = [];
            foreach ($department_ids as $deptId) {
                $category_ids[$deptId] = [];
            }

            $parentCategories = ProblemCategory::whereIn('id', $problem_category_ids)
                ->whereNull('parent_id')
                ->get(['id', 'department_id']);

            foreach ($parentCategories as $pc) {
                if (isset($category_ids[$pc->department_id])) {
                    $category_ids[$pc->department_id][$pc->id] = [];
                }
            }

            $subCategories = ProblemCategory::whereIn('id', $sub_category_ids)
                ->whereNotNull('parent_id')
                ->get(['id', 'parent_id']);

            foreach ($subCategories as $sub) {
                foreach ($category_ids as $deptId => $pcs) {
                    if (isset($category_ids[$deptId][$sub->parent_id])) {
                        $category_ids[$deptId][$sub->parent_id][] = $sub->id;
                        break;
                    }
                }
            }

            if (isset($request->groupBy)) {
                if(! Auth::user()->hasPermissionTo('KanbanBoardGroupBy')) {
                    return response()->json(['status' => 'danger', 'msg' => trans('content.user_fields.Permission_denied')]);
                }
                $board_item_id = KanbanBoardItem::where('board_id', $request->id)->first(['id', 'item_type']);
                
                if(isset($board_item_id->item_type) && $board_item_id->item_type == 2){
                    switch ($request->groupBy) {
                        case 1:
                            $items = Status::select('id', 'name as Sprint_name')->where('company_id', $kanban_board->company_id)->orWhere('company_id', 0)->get();
                            $items->prepend((object)['id' => null,'Sprint_name' => 'No Status' ]);
                            break;
                        case 2:
                            $items = Priority::select('id', 'name as Sprint_name')->get();
                            $items->prepend((object)['id' => null,'Sprint_name' => 'No Priority']);
                            break;
                        case 3:
                            $assignUserIds = CustomBoardItemTicket::where('board_id', $request->id)->pluck('assign_to')->flatMap(fn($value) => explode(',', $value))->filter()->unique()->values();
                            $items = User::whereIn('id', $assignUserIds)->select('id', DB::raw('CASE WHEN users.displayName IS NOT NULL THEN users.displayName ELSE CONCAT(users.first_name, " ", users.last_name) END AS Sprint_name'))->get();
                            $items->prepend((object)['id' => null,'Sprint_name' => 'Not Assigned']);
                            break;
                        case 4: // Weekly
                            if ($request->groupby_date == 1) {
                                $items = CustomBoardItemTicket::select(
                                    DB::raw('YEAR(created_at) as year'),
                                    DB::raw('WEEK(created_at, 3) as week'),
                                    DB::raw("IFNULL(CONCAT('Week ', WEEK(created_at, 3),'(',YEAR(created_at),')'), 'No Week Range') as Sprint_name"),
                                    DB::raw("IFNULL(CONCAT(DATE_FORMAT(DATE_SUB(created_at, INTERVAL (WEEKDAY(created_at)) DAY), '%d-%m-%Y'), ' to ', DATE_FORMAT(DATE_ADD(DATE_SUB(created_at, INTERVAL (WEEKDAY(created_at)) DAY), INTERVAL 6 DAY), '%d-%m-%Y')),'No Week Range') as tooltip"),
                                    DB::raw('CONCAT(WEEK(created_at, 3), "", YEAR(created_at)) as id')
                                );
                            } elseif ($request->groupby_date == 2) {
                                $items = CustomBoardItemTicket::select(
                                    DB::raw('YEAR(updated_at) as year'),
                                    DB::raw('WEEK(updated_at, 3) as week'),
                                    DB::raw("IFNULL(CONCAT('Week ', WEEK(updated_at, 3),'(',YEAR(updated_at),')'), 'No Week Range') as Sprint_name"),
                                    DB::raw("IFNULL(CONCAT( DATE_FORMAT(DATE_SUB(updated_at, INTERVAL (WEEKDAY(updated_at)) DAY), '%d-%m-%Y'),' to ',DATE_FORMAT(DATE_ADD(DATE_SUB(updated_at, INTERVAL (WEEKDAY(updated_at)) DAY), INTERVAL 6 DAY), '%d-%m-%Y')),'No Week Range') as tooltip"),
                                    DB::raw('CONCAT(WEEK(updated_at, 3), "", YEAR(updated_at)) as id')
                                );
                            } elseif ($request->groupby_date == 3) {
                                $items = CustomBoardItemTicket::select(
                                    DB::raw('YEAR(expected_date) as year'),
                                    DB::raw('WEEK(expected_date, 3) as week'),
                                    DB::raw("IFNULL(CONCAT('Week ', WEEK(expected_date, 3),'(',YEAR(expected_date),')'), 'No Week Range') as Sprint_name"),
                                    DB::raw("IFNULL( CONCAT(DATE_FORMAT( DATE_SUB(expected_date, INTERVAL (WEEKDAY(expected_date)) DAY), '%d-%m-%Y'),' to ',DATE_FORMAT(DATE_ADD( DATE_SUB(expected_date, INTERVAL (WEEKDAY(expected_date)) DAY), INTERVAL 6 DAY), '%d-%m-%Y')),'No Week Range') as tooltip"),
                                    DB::raw('CONCAT(WEEK(expected_date, 3), "", YEAR(expected_date)) as id')
                                );
                            }
                            $items = $items->where('board_id', $request->id)->groupBy('year', 'week')->get();
                            break;

                        case 5: // Monthly
                            if($request->groupby_date == 1){
                                $items = CustomBoardItemTicket::select(
                                    DB::raw('YEAR(created_at) as year'),
                                    DB::raw('MONTH(created_at) as month'),
                                    DB::raw('CONCAT(MONTHNAME(created_at), " ", YEAR(created_at)) as Sprint_name'),
                                    DB::raw('CONCAT(MONTH(created_at), "", YEAR(created_at)) as id')
                                );
                            }elseif($request->groupby_date == 2){
                                $items = CustomBoardItemTicket::select(
                                    DB::raw('YEAR(updated_at) as year'),
                                    DB::raw('MONTH(updated_at) as month'),
                                    DB::raw('CONCAT(MONTHNAME(updated_at), " ", YEAR(updated_at)) as Sprint_name'),
                                    DB::raw('CONCAT(MONTH(updated_at), "", YEAR(updated_at)) as id')
                                );
                            }elseif($request->groupby_date == 3){
                                $items = CustomBoardItemTicket::select(
                                    DB::raw('YEAR(expected_date) as year'),
                                    DB::raw('MONTH(expected_date) as month'),
                                    DB::raw('CONCAT(COALESCE(MONTHNAME(expected_date), "No Month"), " ", COALESCE(YEAR(expected_date), "")) as Sprint_name'),
                                    DB::raw('CONCAT(MONTH(expected_date), "", YEAR(expected_date)) as id')
                                );
                            }
                            $items = $items->where('board_id',$request->id)->groupBy('year', 'month')->get();
                            break;

                        case 6: // Yearly
                            if($request->groupby_date == 1){
                                $items = CustomBoardItemTicket::select(
                                    DB::raw('YEAR(created_at) as year'),
                                    DB::raw('YEAR(created_at) as id'),
                                    DB::raw('YEAR(created_at) as Sprint_name'),
                                );
                            }elseif($request->groupby_date == 2){
                                $items = CustomBoardItemTicket::select(
                                    DB::raw('YEAR(updated_at) as year'),
                                    DB::raw('YEAR(updated_at) as id'),
                                    DB::raw('YEAR(updated_at) as Sprint_name'),
                                );
                            }elseif($request->groupby_date == 3){
                                $items = CustomBoardItemTicket::select(
                                    DB::raw('YEAR(expected_date) as year'),
                                    DB::raw('YEAR(expected_date) as id'),
                                    DB::raw('COALESCE(YEAR(expected_date), "No Year") as Sprint_name'),
                                );
                            }
                            $items = $items->where('board_id',$request->id)->groupBy('year')->get();
                            break;
                    }
                    $custom_statuses = CustomBoardItemTicket::where('board_id', $request->id)->pluck('status');
                    $data = Status::whereIn('id',$custom_statuses)->select('name','color_code')->get();
                    $custom_board_statuses = $data;
                } else if(isset($board_item_id->item_type) && $board_item_id->item_type == 1){
                    switch ($request->groupBy) {
                        case 1:
                            $items = Status::select('id', 'name as Sprint_name')->where('company_id', $kanban_board->company_id)->orWhere('company_id', 0)->get();
                            $items->prepend((object)['id' => null,'Sprint_name' => 'No Status' ]);
                            break;
                        case 2:
                            $items = Priority::select('id', 'name as Sprint_name')->get();
                            $items->prepend((object)['id' => null,'Sprint_name' => 'No Priority']);
                            break;
                        case 3:
                            $assignUserIds = Ticket::where('company_id', $kanban_board->company_id)->pluck('assigned_to')->unique()->values();
                            $items = User::whereIn('id', $assignUserIds->filter(fn($id) => $id != 0))->select('id',DB::raw("CASE  WHEN users.displayName IS NOT NULL AND users.displayName != '' THEN CONCAT(users.displayName,'(',users.username,')') ELSE CONCAT(users.first_name, ' ', users.last_name,'(',users.username,')') END AS Sprint_name"))->get();
                            $items->prepend((object)['id' => null, 'Sprint_name' => 'Not Assigned']);
                            if ($assignUserIds->contains(0)) {
                                $items->push((object)['id' => 0, 'Sprint_name' => 'System']);
                            }
                            break;
                        case 4:
                            if ($request->groupby_date == 1) {
                                $items = Ticket::select(
                                    DB::raw('YEAR(created_at) as year'),
                                    DB::raw('WEEK(created_at, 3) as week'),
                                    DB::raw("IFNULL(CONCAT('Week ', WEEK(created_at, 3),'(',YEAR(created_at),')'), 'No Week Range') as Sprint_name"),
                                    DB::raw("IFNULL(CONCAT(DATE_FORMAT(DATE_SUB(created_at, INTERVAL (WEEKDAY(created_at)) DAY), '%d-%m-%Y'), ' to ', DATE_FORMAT(DATE_ADD(DATE_SUB(created_at, INTERVAL (WEEKDAY(created_at)) DAY), INTERVAL 6 DAY), '%d-%m-%Y')),'No Week Range') as tooltip"),
                                    DB::raw('CONCAT(WEEK(created_at, 3), "", YEAR(created_at)) as id')
                                );
                            } elseif ($request->groupby_date == 2) {
                                $items = Ticket::select(
                                    DB::raw('YEAR(updated_at) as year'),
                                    DB::raw('WEEK(updated_at, 3) as week'),
                                    DB::raw("IFNULL(CONCAT('Week ', WEEK(updated_at, 3),'(',YEAR(updated_at),')'), 'No Week Range') as Sprint_name"),
                                    DB::raw("IFNULL(CONCAT( DATE_FORMAT(DATE_SUB(updated_at, INTERVAL (WEEKDAY(updated_at)) DAY), '%d-%m-%Y'),' to ',DATE_FORMAT(DATE_ADD(DATE_SUB(updated_at, INTERVAL (WEEKDAY(updated_at)) DAY), INTERVAL 6 DAY), '%d-%m-%Y')),'No Week Range') as tooltip"),
                                    DB::raw('CONCAT(WEEK(updated_at, 3), "", YEAR(updated_at)) as id')
                                );
                            } elseif ($request->groupby_date == 3) {
                                $items = Ticket::select(
                                    DB::raw('YEAR(resolved_at) as year'),
                                    DB::raw('WEEK(resolved_at, 3) as week'),
                                    DB::raw("IFNULL(CONCAT('Week ', WEEK(resolved_at, 3),'(',YEAR(resolved_at),')'), 'No Week Range') as Sprint_name"),
                                    DB::raw("IFNULL( CONCAT(DATE_FORMAT( DATE_SUB(resolved_at, INTERVAL (WEEKDAY(resolved_at)) DAY), '%d-%m-%Y'),' to ',DATE_FORMAT(DATE_ADD( DATE_SUB(resolved_at, INTERVAL (WEEKDAY(resolved_at)) DAY), INTERVAL 6 DAY), '%d-%m-%Y')),'No Week Range') as tooltip"),
                                    DB::raw('CONCAT(WEEK(resolved_at, 3), "", YEAR(resolved_at)) as id')
                                );
                            }
                            $items->where('company_id', $kanban_board->company_id);
                            $items = $items->groupBy('year', 'week')->get();
                            break;
                        case 5:
                            if($request->groupby_date == 1){
                                $items = Ticket::select(
                                    DB::raw('YEAR(created_at) as year'),
                                    DB::raw('MONTH(created_at) as month'),
                                    DB::raw('CONCAT(MONTHNAME(created_at), " ", YEAR(created_at)) as Sprint_name'),
                                    DB::raw('CONCAT(MONTH(created_at), "", YEAR(created_at)) as id')
                                );
                            }elseif($request->groupby_date == 2){
                                $items = Ticket::select(
                                    DB::raw('YEAR(updated_at) as year'),
                                    DB::raw('MONTH(updated_at) as month'),
                                    DB::raw('CONCAT(MONTHNAME(updated_at), " ", YEAR(updated_at)) as Sprint_name'),
                                    DB::raw('CONCAT(MONTH(updated_at), "", YEAR(updated_at)) as id')
                                );
                            }elseif($request->groupby_date == 3){
                                $items = Ticket::select(
                                    DB::raw('YEAR(resolved_at) as year'),
                                    DB::raw('MONTH(resolved_at) as month'),
                                    DB::raw('CONCAT(COALESCE(MONTHNAME(resolved_at), "No Month"), " ", COALESCE(YEAR(resolved_at), "")) as Sprint_name'),
                                    DB::raw('CONCAT(MONTH(resolved_at), "", YEAR(resolved_at)) as id')
                                );
                            }
                            $items->where('company_id', $kanban_board->company_id);
                            $items = $items->groupBy('year', 'month')->get();
                            break;
                        case 6:
                            if($request->groupby_date == 1){
                                $items = Ticket::select(
                                    DB::raw('YEAR(created_at) as year'),
                                    DB::raw('YEAR(created_at) as id'),
                                    DB::raw('YEAR(created_at) as Sprint_name'),
                                );
                            }elseif($request->groupby_date == 2){
                                $items = Ticket::select(
                                    DB::raw('YEAR(updated_at) as year'),
                                    DB::raw('YEAR(updated_at) as id'),
                                    DB::raw('YEAR(updated_at) as Sprint_name'),
                                );
                            }elseif($request->groupby_date == 3){
                                $items = Ticket::select(
                                    DB::raw('YEAR(resolved_at) as year'),
                                    DB::raw('YEAR(resolved_at) as id'),
                                    DB::raw('COALESCE(YEAR(resolved_at), "No Year") as Sprint_name'),
                                );
                            }
                            $items->where('company_id', $kanban_board->company_id);
                            $items = $items->groupBy('year')->get();
                            break;
                    }
                }
                $board_data = KanbanBoard::select('board_items_type', 'kanban_board_members.access_type')
                ->leftJoin('kanban_board_members', 'kanban_board_members.board_id', 'kanban_boards.id')
                ->where('kanban_board_members.user_id', Auth::id())
                ->where('kanban_boards.id', $request->id)
                ->whereNull('kanban_boards.deleted_at')
                ->whereNull('kanban_board_members.deleted_at')
                ->first();
                $board_item_id = KanbanBoardItem::where('board_id', $request->id)->value('id');

                foreach ($items as $item) {
                    $item->item_type = $board_data->board_items_type ?? null;
                    $item->board_id = $request->id;
                    $item->groupby_date = $request->groupby_date??'';
                    $item->archived = $request->archived;
                    $item->access_type = $board_data->access_type ?? null;
                    $item->groupBy = $request->groupBy;
                    $item->board_item_id = $board_item_id;
                }
                $groupBy = true;
            } else{
                $itemsQuery = KanbanBoardItem::select(['kanban_board_items.board_id','kanban_board_items.id','kanban_board_items.item_type','kanban_board_members.access_type','kanban_board_items.item_name','tkt_statuses.color_code'])
                ->addSelect(DB::raw("CASE WHEN kanban_board_items.item_type = 1 THEN tkt_statuses.name ELSE kanban_board_items.item_name END AS Sprint_name"))
                ->leftJoin('kanban_board_members', 'kanban_board_members.board_id', 'kanban_board_items.board_id')
                ->leftJoin('tkt_statuses', 'tkt_statuses.id', 'kanban_board_items.item_name')
                ->where('kanban_board_members.user_id',Auth::id())
                ->whereNull('kanban_board_members.deleted_at')
                ->groupBy('kanban_board_items.id', 'tkt_statuses.color_code');
            
        
                if ($request->id) {
                    $itemsQuery->where('kanban_board_items.board_id', $request->id);
                }
                $page = (int) $request->input("page", 1);
                $take = (int) $request->input("size", 10);

                $return['recordsFiltered'] = $itemsQuery->count();
                $items = $itemsQuery->orderBy('kanban_board_items.position', 'desc')->get();
                if($items[0]->item_type == 2){
                    $custom_statuses = CustomBoardItemTicket::where('board_id', $request->id)->pluck('status');

                    $data = Status::whereIn('id',$custom_statuses)->select('name','color_code')->get();
                    $items['custom_status'] = $data;
                }
            }          
            Config()->set('database.connections.mysql.strict', true);
            DB::reconnect();
            $return['mapped_categories'] = $category_ids;
            $return['status'] = 'success';
            $return['msg'] = trans('ticket.service_ticket_fields.fetch_successfully');
            $return['data'] = ['statuses' => $items];
            $return['custom_data'] = ['custom_statuses' => $custom_board_statuses ?? []];
            $return['grouped'] = isset($groupBy) ? $groupBy : false;
            $return['archived'] = (bool) $request->archived;

            return response()->json($return);
        } catch (\Exception $e) {
            Log::error("ajaxBoardList Api() error: " . $e->getMessage());
            return response()->json($return);
        }
    }
    public function apiGetBoardData(Request $request) {
        $page = $request->get('page', 1);
        $perPage = 10;
        $request_filters = json_decode(base64_decode($request->filters), true) ?? [];
        $filters = isset($request_filters['other_filters']) ? $request_filters['other_filters'] : [];
        $search = $request->search;
        $archived = $request->archived;
        $item_board_id = $request->status['id'];
        $item = KanbanBoardItem::find($item_board_id);
        $board_company_id = KanbanBoard::find($item->board_id)->company_id;

        if ($item->item_type == 1) {
            $kanbanData = $item->kanbanData($item->item_name, $item->board_id, $filters, $search, $page, $request->order,$board_company_id);
        } else {
            $kanbanData = $item->ticketBoardData($item->item_name, $item_board_id, $item->board_id, $filters, $search, $page, $request->order,$archived,$board_company_id);
        }
        return $kanbanData;
    }

}

