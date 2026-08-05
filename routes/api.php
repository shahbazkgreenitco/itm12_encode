<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NLQueryController;
use App\Models\ChatMessage;
use App\Events\PrivateMessage;
use App\Models\User;
use Illuminate\Support\Facades\Http;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/nl-query', [NLQueryController::class, 'query']);
Route::get('/nl-query/schema', [NLQueryController::class, 'schema']);
Route::get('/nl-query/debug', [NLQueryController::class, 'debug']);

Route::group(['middleware' => ['auth.app_token', 'api.language']], function() {
    Route::any('/', [App\Http\Controllers\SettingsController::class, 'basic']);

    Route::post('login', [App\Http\Controllers\Auth\Api\LoginController::class, 'login']);
    Route::post('ms-login', [App\Http\Controllers\Auth\Api\UserController::class, 'msLogin']);
    Route::post('auto-login', [App\Http\Controllers\Auth\Api\LoginController::class, 'autologin']);

    Route::group(['middleware' => ['auth.access_token']], function() { 
        // Get Company Route
        Route::post('getCompanyByUserAccess', [App\Http\Controllers\Auth\Api\UserController::class, 'getCompanyByUserAccess']);
        Route::post('store-default-company', [App\Http\Controllers\Auth\Api\UserController::class, 'storeDefaultCompany'])->name('user.store.default.company.api');
        Route::post('getUserByQuery', [App\Http\Controllers\UserController::class, 'getActiveUsersByQuery']);


         // get Users 
        Route::post('v2/getUserByQuery', [App\Http\Controllers\UserController::class, 'getUsersByQueryApp']);
        Route::post('update-user-detail', [App\Http\Controllers\Auth\Api\UserDetailController::class, 'updateUserDetails']);
        Route::post('user/updateFcmToken', [App\Http\Controllers\Auth\Api\UserController::class, 'updateFcmToken']);
        Route::get('user/get-profile/{id}', [App\Http\Controllers\Auth\Api\ProfileController::class, 'ajaxGetUserProfile']);

        // Tickets Routes
        Route::post('tickets/create', [App\Http\Controllers\Auth\Api\Ticket\IndexController::class, 'create']);

        Route::post('ticket/ticket_history', [App\Http\Controllers\Auth\Api\Ticket\IndexController::class, 'ticketHistory']);
        /*v2 Tickets */
        Route::post('v2/tickets/ajax-lists', [App\Http\Controllers\Ticket\IndexController::class, 'ajaxTicketList']);
        Route::get('v2/tickets/init', [App\Http\Controllers\Ticket\IndexController::class, 'initiate']);
        Route::any('v2/tickets/departments/{ticket_id?}', [App\Http\Controllers\Ticket\IndexController::class, 'departments']);
        Route::post('v2/ticket/reopen', [App\Http\Controllers\Ticket\IndexController::class, 'reopen']);
        Route::post('v2/ticket/update_status', [App\Http\Controllers\Ticket\IndexController::class, 'updateStatus']);
        Route::get('v2/tickets/problem-types/st/{id}', [App\Http\Controllers\Ticket\ProblemCategoryController::class, 'ajaxSubCategoryForApp']);
        Route::post('v2/tickets/update-master-cc/{ticket_id}', [App\Http\Controllers\Ticket\IndexController::class, 'updateMasterCc']);
        Route::get('v2/service-tickets/filter-options', [App\Http\Controllers\Auth\Api\Ticket\IndexController::class, 'serviceTicketFilterOptions']);
        Route::post('v2/ticket/transfer', [App\Http\Controllers\Ticket\IndexController::class, 'transfer']);
        Route::post('v2/ticket/change_creator', [App\Http\Controllers\Ticket\IndexController::class, 'changeCreator']);
        
        Route::post('v2/ticket/attachment/add', [App\Http\Controllers\Ticket\AttachmentController::class, 'add'])->name('attachment.add');
        Route::post('v2/ticket/attachment/remove', [App\Http\Controllers\Ticket\AttachmentController::class, 'remove']);

        Route::post('v2/ticket/get_users_to_assign/{id}', [App\Http\Controllers\Ticket\IndexController::class, 'getUsersToAssignForApp']);
        Route::get('v2/tickets/problem-categories/by-dept/{id}/{parent_only?}', [App\Http\Controllers\Ticket\ProblemCategoryController::class, 'ajaxOptionsForApp']);
        Route::get('v2/ticket/getDepartmentCustomFields/{id}', [App\Http\Controllers\Auth\Api\DepartmentController::class, 'getDepartmentCustomFields']);

        //status Form
        Route::post('ticket-status-form', [App\Http\Controllers\Auth\Api\Ticket\TicketStatusFormController::class, 'getStatusForm'])->name('ticket.status.form');
        Route::post('store-status-form', [App\Http\Controllers\Auth\Api\Ticket\TicketStatusFormController::class, 'storeRequestForm'])->name('store.status.form');
        Route::post('ticket-status-form/edit/{id}', [App\Http\Controllers\Auth\Api\Ticket\TicketStatusFormController::class, 'editStatusForm'])->name('edit.status.form');
        Route::post('status_dynamic_form/update_form', [App\Http\Controllers\Auth\Api\Ticket\TicketStatusFormController::class, 'updateStatusRequestedForm'])->name('update.status.form');
        Route::get('status_dynamic_form/view/{id}', [App\Http\Controllers\Auth\Api\Ticket\TicketStatusFormController::class, 'viewStatusForm'])->name('view.status.form');

        //form 
        Route::post('dynamic_form/form/update', [App\Http\Controllers\FormController::class, 'updateRequestedForm']);
        
        // service request 
        Route::post('tickets/requestList/{main_filter?}', [App\Http\Controllers\Auth\Api\Ticket\RequestController::class, 'requestAjaxList']);
        Route::get('tickets/getServiceRequestInfo/{id}', [App\Http\Controllers\Auth\Api\Ticket\RequestController::class, 'getServiceRequestInfo']);
        Route::get('tickets/requestsHistory/{id}', [App\Http\Controllers\Auth\Api\Ticket\RequestController::class, 'requestsHistory']);
        Route::post('tickets/requestApprove/{pr_id}', [App\Http\Controllers\Auth\Api\Ticket\RequestController::class, 'requestApprove']);
        Route::post('tickets/revokeDecision/{pr_id}', [App\Http\Controllers\Auth\Api\Ticket\RequestController::class, 'revokeDecision']);
        Route::post('tickets/updateRequestInfo/{id}', [App\Http\Controllers\Auth\Api\Ticket\RequestController::class, 'updateRequestInfo']);
        Route::post('update-ticket-tag', [App\Http\Controllers\Auth\Api\Ticket\IndexController::class, 'updateTicketTag']);
        Route::post('tickets/create-by-user', [App\Http\Controllers\Auth\Api\Ticket\IndexController::class, 'createByUser']);
        Route::post('tickets/create', [App\Http\Controllers\Auth\Api\Ticket\IndexController::class, 'create']);
        Route::any('ticket/getTagDetails', [App\Http\Controllers\Auth\Api\Ticket\IndexController::class, 'ajaxTagDetail']);
        Route::post('ticket/edit', [App\Http\Controllers\Auth\Api\Ticket\IndexController::class, 'editTicketApi']);

        Route::post('v2/getUserByQuery', [App\Http\Controllers\UserController::class, 'getUsersByQueryApp']);
        Route::get('user/get-profile/{id}', [App\Http\Controllers\Auth\Api\ProfileController::class, 'ajaxGetUserProfile']);

        Route::post('update-user-detail', [App\Http\Controllers\Auth\Api\UserDetailController::class, 'updateUserDetails']);
        Route::post('v2/ticket/assign_to', [App\Http\Controllers\Ticket\IndexController::class, 'AssignedTo']);
        Route::get('tickets/serviceRequestForm/{id}', [App\Http\Controllers\Ticket\TicketController::class, 'serviceRequestFormDetails']);
        Route::post('dynamic_form/update_form', [App\Http\Controllers\FormController::class, 'requestedFormUpdateApi']);
        Route::post('dynamic_form/form/update', [App\Http\Controllers\FormController::class, 'updateRequestedForm']);


        Route::get('getModulesForRole', [App\Http\Controllers\Auth\Api\CommonController::class, 'getModulesForRole']);
        Route::get('module_check',[App\Http\Controllers\Auth\Api\CommonController::class, 'moduleEnableCheck']);
        Route::get('getModulesPermissions/{moduleId}', [App\Http\Controllers\Auth\Api\CommonController::class, 'getModulesPermissions']);
        Route::get('getLangData', [App\Http\Controllers\Auth\Api\CommonController::class, 'getLangData']);
        Route::post('getTicketProcureRequest', [App\Http\Controllers\Auth\Api\CommonController::class, 'getTicketProcureRequest']);
        Route::get('v2/get-dashboard', [App\Http\Controllers\Auth\Api\CommonController::class, 'getDashboard']);

        Route::get('getByCustomDropDown/{main_filter?}', [App\Http\Controllers\CustomFieldController::class, 'getPredefinedDropdownByQuery']);
        
        //dashboard
        Route::get('dashboard/userActions', [App\Http\Controllers\Auth\Api\CommonController::class, 'userActions']);
        

        // kanban Board
        Route::get('get-access-board-name', [App\Http\Controllers\Auth\Api\Ticket\KanbanController::class, 'apiGetAccessBoardName']);
        Route::post('tickets/kanban-board/board-list', [App\Http\Controllers\Auth\Api\Ticket\KanbanController::class, 'ajaxBoardList']);
        Route::post('get-board-data', [App\Http\Controllers\Auth\Api\Ticket\KanbanController::class, 'apiGetBoardData']);

    });
});
