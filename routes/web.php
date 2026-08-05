<?php

use Illuminate\Support\Facades\Crypt;

use App\Http\Controllers\NLQueryController;
use Illuminate\Support\Facades\Broadcast;

Broadcast::routes(['middleware' => ['web', 'auth']]);

Route::get('/nl-query', [NLQueryController::class, 'index'])->name('nl-query.index');
Route::post('/nl-query', [NLQueryController::class, 'query'])->name('nl-query.query');
Route::post('/nl-query/export-pdf',    [NLQueryController::class, 'exportPdf'])->name('nl-query.export-pdf');
Route::post('/nl-query/export-excel',  [NLQueryController::class, 'exportExcel'])->name('nl-query.export-excel');
Route::get('/nl-query/schema',         [NLQueryController::class, 'schema'])->name('nl-query.schema');

Route::get('device_detail', function () {
    return view('dashboard.device_detail');
})->name('device_details');
Route::get('change_details', function () {
    return view('change_management.details');
})->name('change-details');
Route::get('ni_details', function () {
    return view('devices.ni-detail');
})->name('ni-details');


Route::get('device_list', function () {
    return view('dashboard.device_list');
})->name('device_list');


Route::get('support', function () {
    $token = config('app.app_token');
    $encryptedToken = Crypt::encryptString($token);
    return view('tickets.Support.support', compact('encryptedToken'));
});

Route::get('/gd-check', function () {
    dd([
        'gd_loaded' => extension_loaded('gd'),
        'extension_dir' => ini_get('extension_dir'),
        'php_ini' => php_ini_loaded_file(),
    ]);
});

Route::get('/zip-test', function () {

    return [
        'zip_loaded' => extension_loaded('zip'),
        'zip_class'  => class_exists('ZipArchive'),
        'php_version' => phpversion(),
        'loaded_ini' => php_ini_loaded_file(),
    ];

});

Route::get('/safe-migrate-fix', [App\Http\Controllers\MigrationController::class, 'safeMigrateFix']);

Route::get('iframe', function () {
    return view('tickets.Support.iframe');
});
Route::post('support/submit', [App\Http\Controllers\Auth\Api\Ticket\TktPluginController::class, 'support']);
Route::get("ticket_dashboard", function() {
    return view('dashboard.ticketDash');
});
Route::get("tickets/technician-leader-board", function() {
    return view('dashboard.technician_rank');
});
Route::get('public-articles', [App\Http\Controllers\KnowledgeManagement\PublicDocumentController::class, 'nonloginKnowledgeDocument'])->name('public_articles');
Route::group(['middleware' => ['language']], function () {
    // Public Artical Route
    Route::post('public-articles/jx-public-knowledge-list', [App\Http\Controllers\KnowledgeManagement\PublicDocumentController::class, 'ajaxPublicDocumentList'])->name('publicKnowledgeDocuments');
    Route::get('public-article/view/{id}', [App\Http\Controllers\KnowledgeManagement\PublicDocumentController::class, 'viewPublicArticle']);
    Route::get('getCompanyForPublic', [App\Http\Controllers\KnowledgeManagement\PublicDocumentController::class, 'getCompanyForPublic']);
    Route::get('getPublicDepartmentsWithCompanyByQuery', [App\Http\Controllers\KnowledgeManagement\PublicDocumentController::class, 'getPublicDepartmentsWithCompanyByQuery']);
    Route::get('publicFetch_category_by_ajax', [App\Http\Controllers\KnowledgeManagement\KdCategoryController::class, 'fetchCategoryByAjax']);
    Route::get('publicFetch_subcategory_by_ajax', [App\Http\Controllers\KnowledgeManagement\KdCategoryController::class, 'fetchSubCategoryByAjax']);
    Route::get('public-articles/attachment/download/{id}', [App\Http\Controllers\KnowledgeManagement\DocumentController::class, 'download']);
    Route::get('public-articles/attachment/view/{id}', [App\Http\Controllers\KnowledgeManagement\DocumentController::class, 'viewItem']);
    Route::get('public-document/search', [App\Http\Controllers\KnowledgeManagement\PublicDocumentController::class, 'search']);
    // End Public Article Route

    Route::get('typographyv1', [App\Http\Controllers\HomeController::class, 'typographyv1'])->name('typographyv1');
    Route::get('typographyv2', [App\Http\Controllers\HomeController::class, 'typographyv2'])->name('typographyv2');
    Route::get('tickets/request-approve/{enc}/{unique_code}/{status}', [App\Http\Controllers\Ticket\ServiceRequestApproveController::class, 'approve']);
    Route::post('tickets/request-Approve/{pr_id}', [App\Http\Controllers\Ticket\RequestController::class, '_requestApprove']);
    Route::get('tickets/request-reject/{enc}/{code}/{status}', [App\Http\Controllers\Ticket\ServiceRequestApproveController::class, 'approve']);
    Route::get('tickets/request-bulk-approve/{enc}/{unique_code}/{status}', [App\Http\Controllers\Ticket\ServiceRequestApproveController::class, 'bulkApprove']);

    // Route::middleware(['verify.user_trusted_device'])->group(function () {
    Route::get('tickets/request-bulk-reject/{enc}/{unique_code}/{status}', [App\Http\Controllers\Ticket\ServiceRequestApproveController::class, 'bulkApprove']);
    Route::post('tickets/bulk-approve-request', [App\Http\Controllers\Ticket\ServiceRequestApproveController::class, 'bulkApproveRequest']);
    // });

    Route::get('change_request/request-approve/{enc}/{unique_code}/{status}', [App\Http\Controllers\ChangeManagement\ListController::class, 'approve']);
    Route::get('change_request/request-reject/{enc}/{code}/{status}', [App\Http\Controllers\ChangeManagement\ListController::class, 'approve']);

    Route::get("/twilio/make-call/{to}", [App\Http\Controllers\Ticket\ITMConnect\IndexController::class, 'makeCall'])->name('makeCall');
    Route::get('/twilio/hang-up/{sid}', [App\Http\Controllers\Ticket\ITMConnect\IndexController::class, 'hangUpCall']);
    Route::get('/twilio/saveNo/{to}', [App\Http\Controllers\Ticket\ITMConnect\IndexController::class, 'savePhoneNo']);
    Route::get('/twilio/callLogs', [App\Http\Controllers\Ticket\ITMConnect\IndexController::class, 'callLogs']);
    Route::get('/twilio/playRecording/{url}', [App\Http\Controllers\Ticket\ITMConnect\IndexController::class, 'playRecording']);
    Route::get('/twilio/downloadAndSaveRecording', [App\Http\Controllers\Ticket\ITMConnect\IndexController::class, 'downloadAndSaveRecording']);
    Route::get('/twilio/convetToItmConnect', [App\Http\Controllers\Ticket\ITMConnect\IndexController::class, 'convetToItmConnect']);

    Route::get('sentiment', [App\Http\Controllers\HomeController::class, 'sentiment'])->name('sentiment');

    Route::get('2fa', [App\Http\Controllers\Auth\TwoFAController::class, 'index'])->name('2fa.index');
    Route::post('2fa', [App\Http\Controllers\Auth\TwoFAController::class, 'store'])->name('2fa.post');
    Route::get('2fa/reset/{userId}/{imposonateId?}', [App\Http\Controllers\Auth\TwoFAController::class, 'resend'])->name('2fa.resend');
    Route::get('downloadMacAgent', [App\Http\Controllers\UserController::class, 'downloadMacAgent']);
    Route::get('downloadMacAgentRDP', [App\Http\Controllers\UserController::class, 'downloadMacAgentRDP']);

    Route::any('login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login')->middleware('guest');
    Route::get('company/logo/{id}', [App\Http\Controllers\Auth\LoginController::class, 'localStorageLogo']);
    Route::any('admin/login', [App\Http\Controllers\Auth\LoginController::class, 'adminLogin'])->name('adminLogin');
    Route::any('admin/validate-username/{userName}', [App\Http\Controllers\Auth\LoginController::class, 'validateUserName']);
    Route::get('login-with-google', [App\Http\Controllers\UserController::class, 'loginWithGoogle'])->name('loginWithGoogle');
    Route::any('resume-from-google', [App\Http\Controllers\UserController::class, 'resumeFromGoogle']);
    Route::get('google-login', [App\Http\Controllers\UserController::class, 'googleLogin']);
    Route::any('login-with-office', [App\Http\Controllers\UserController::class, 'loginWithOffice'])->name('loginWithOffice');
    Route::any('resume-from-office', [App\Http\Controllers\UserController::class, 'resumeFromOffice']);
    Route::any('resume-from-office-multiple', [App\Http\Controllers\UserController::class, 'resumeFromOfficeMultiple']);
    Route::any('forgot-password', [App\Http\Controllers\Auth\CustomPasswordController::class, 'reset'])->middleware('guest');
    Route::get('password/reset/{token?}/{id?}', [App\Http\Controllers\Auth\CustomPasswordController::class, 'showResetForm']);
    Route::post('/jx-password-update', [App\Http\Controllers\Auth\CustomPasswordController::class, 'update']);
    // Route::get('itam-knowledge-document', [App\Http\Controllers\KnowledgeManagement\DocumentController::class, 'publicKnowledgeDocument']);
    Route::get('itam-knowledge-document', [App\Http\Controllers\KnowledgeManagement\DocumentController::class, 'publicArticleIndex']);
    Route::post('itam-knowledge-document/jx-knowledge-list', [App\Http\Controllers\KnowledgeManagement\DocumentController::class, 'ajaxPublicDocumentList'])->name('publicKnowledgeDocuments');
    Route::get('itam-knowledge-document/public_article/view/{id}', [App\Http\Controllers\KnowledgeManagement\DocumentController::class, 'viewKnowledge']);

    Route::any('confirm/{id}/{confirm_state}/{access_code}', [App\Http\Controllers\DirectConfirmController::class, 'update']);
    Route::post('confirm/add_reason/{id}', [App\Http\Controllers\DirectConfirmController::class, 'ajaxAddReason']);
    Route::get('task/download_attachment/d/{id}', [App\Http\Controllers\Auth\Api\Task\TaskController::class, 'downloadAttachment']);
    Route::get('task/view_attachment/d/{id}', [App\Http\Controllers\Auth\Api\Task\TaskController::class, 'viewAttachment']);
    Route::get('ticket/attachment/view/d/{id}/{temp?}', [App\Http\Controllers\Ticket\AttachmentController::class, 'viewItem']);
    Route::get('procurement/attachment/view/d/{id}/{temp?}', [App\Http\Controllers\Procurement\AttachmentController::class, 'viewItem']);

    Route::post('ticket/feedback', [App\Http\Controllers\Ticket\IndexController::class, 'ticketFeedback'])->name('ticket.addFeedback');
    Route::get('ticket/feedback/{code}', [App\Http\Controllers\Ticket\IndexController::class, 'addTicketFeedback']);
    Route::post('/twilio/recording-status', [App\Http\Controllers\Ticket\ITMConnect\IndexController::class, 'handleRecordingStatus']);
    Route::get('/twilio/token/{fcmKey}', [App\Http\Controllers\Ticket\ITMConnect\IndexController::class, 'generateToken']);
    Route::post('/twilio/responseAfterCallConnect', [App\Http\Controllers\Ticket\ITMConnect\IndexController::class, 'responseAfterCallConnect']);
    Route::get('/twilio/call-logs', [App\Http\Controllers\Ticket\ITMConnect\IndexController::class, 'getTwilioCallLogs']);

    Route::group(['middleware' => ['auth.app_token', 'auth.access_token']], function () {
        // Route::get('ticket/attachment/view/{id}', [App\Http\Controllers\Ticket\AttachmentController::class, 'viewItem']);
        Route::get('ticket/attachment/download/{id}', [App\Http\Controllers\Ticket\AttachmentController::class, 'download']);
    });

    Route::any('/device_public/{id}', [App\Http\Controllers\Auth\LoginController::class, 'device_public'])->name('devicePublic')
        ->withoutMiddleware([\App\Http\Middleware\RedirectIfAuthenticated::class]);

    Route::group(['middleware' => ['auth']], function () {
        Route::get('global-search', [App\Http\Controllers\GlobalSearch\IndexController::class, 'globalSearch']);
        Route::get('/view-secure-pdf/{name}', function ($name) {
            $name = decrypt($name);
            return view('pdf_viewer', compact('name'));
        })->name('secure.pdf');
        Route::post('opt_in_for_wa_notification', [App\Http\Controllers\Ticket\TechnicianActivityLogController::class, 'optInForWhatsappNotiifacation']);
        Route::get('getByCustomDropDown/{main_filter?}', [App\Http\Controllers\CustomFieldController::class, 'getPredefinedDropdownByQuery']);
        Route::any('confirm/{id}/{confirm_state}/{access_code}/{via_portal}', [App\Http\Controllers\DirectConfirmController::class, 'update']);
        Route::any('set-locale/{locale}', [App\Http\Controllers\UserController::class, 'setLocale'])->name('setLocale');
        Route::get('versions', [App\Http\Controllers\VersionController::class, 'versions'])->name('versions');
        Route::get('ticket/attachment/view/{id}', [App\Http\Controllers\Ticket\AttachmentController::class, 'viewItem']);
        Route::get('ticket/attachment/view/{id}/{thumb?}', [App\Http\Controllers\Ticket\AttachmentController::class, 'viewItem']);
        Route::get('test-mail/{to}', [App\Http\Controllers\UserController::class, 'testMail']);
        Route::get('test-notification/{to}', [App\Http\Controllers\UserController::class, 'sendTestNotification']);
        Route::get('awsSendMail', [App\Http\Controllers\UserController::class, 'awsSendMail']);
        Route::any('rfid-logs', [App\Http\Controllers\DeviceController::class, 'rfidLogs']);
        Route::any('jx-rfid-logs', [App\Http\Controllers\DeviceController::class, 'jxRfidLogs']);
        Route::any('assignDevice', [App\Http\Controllers\DeviceController::class, 'assignDevice']);
        Route::any('exportScanLogs', [App\Http\Controllers\DeviceController::class, 'exportScanLogs']);
        Route::any('blockTag/{tag}', [App\Http\Controllers\DeviceController::class, 'blockTags']);
        Route::any('downloadBOTData', [App\Http\Controllers\Ticket\IndexController::class, 'downloadBOTData']);

        // Route::get('short-list-notifications', [App\Http\Controllers\NotificationController::class, 'shortListNotifications'])->name('shortListNotifications');
        // Route::get('mark-as-viewed-notification/{nid}', [App\Http\Controllers\NotificationController::class, 'markAsViewedNotification'])->name('markAsViewedNotification');
    });

    Route::group(['middleware' => ['auth', 'revalidate', 'user_session_follower']], function () {
        Route::get('power-bi-report', [App\Http\Controllers\PowerBi\PowerBiController::class, 'getPowerBiReport'])->name('power.bi.report');
        Route::post('getReportByAjaxCall', [App\Http\Controllers\PowerBi\PowerBiController::class, 'getPowerBiReport'])->name('get.ReportByAjaxCall');
        Route::get('getReport', [App\Http\Controllers\PowerBi\PowerBiController::class, 'getPowerbiReportsForDropDown'])->name('get.report');

        Route::get('make-logout/{id}/{session_id}', [App\Http\Controllers\UserController::class, 'makeSessionOutForUser'])->name('makeLogout');

        Route::get('create-tickets-from-mail', [App\Http\Controllers\ServiceTicketController::class, 'createbymail']);
        Route::get('my-items', [App\Http\Controllers\UserController::class, 'myItems']);
        Route::get('requestable-device', [App\Http\Controllers\DeviceController::class, 'requestables'])->middleware('client.route_restrict');

        Route::post('jx-requestable-device', [App\Http\Controllers\DeviceController::class, 'ajaxRequestableDevice']);
        Route::any('request-device/{id}', [App\Http\Controllers\DeviceController::class, 'requestDevice']);
        Route::any('requestNotify-device/{id}', [App\Http\Controllers\DeviceController::class, 'requestNotifyDevice']);

        Route::get('download-currencies', [App\Http\Controllers\CurrencyController::class, 'download']);
        Route::get('currency-conversion/{valueToConvert}/{fromCurrency}/{toCurrency}', [App\Http\Controllers\CurrencyController::class, 'getExchangeRate']);
        Route::get('update-currency-conversion', [App\Http\Controllers\CurrencyController::class, 'updateTodayConversionRates']);

        Route::any('user-history/{id}', [App\Http\Controllers\UserController::class, 'userHistory']);
        Route::get('jx-licenseseat-list', [App\Http\Controllers\LicenseController::class, 'jxLicenseList']);
        Route::any('requested_form/{id}', [App\Http\Controllers\FormController::class, 'requestedForm']);
        Route::get('requested_form/view/{id}', [App\Http\Controllers\FormController::class, 'viewForm']);
        Route::get('requested_form/custom_form/view/{id}', [App\Http\Controllers\FormController::class, 'viewCustomForm']);
        Route::get('requested_form/custom_form/edit/{id}', [App\Http\Controllers\FormController::class, 'editCustomForm']);
        Route::post('requested_form/custom_form/edit_qty', [App\Http\Controllers\FormController::class, 'editRequestedCustomFormQty']);
        Route::post('requested_form/custom_form/remove-item', [App\Http\Controllers\FormController::class, 'removeItem']);
        Route::get('getUserByQueryForCustomForm', [App\Http\Controllers\UserController::class, 'getActiveUsersByQueryForCustom']);
        Route::get('requested_form/email/form/view/{id}/{pro_tech?}', [App\Http\Controllers\FormController::class, 'viewCustomFormEmail']);
        Route::get('requested_form/edit/{id}', [App\Http\Controllers\FormController::class, 'editRequestedForm']);
        Route::post('dynamic_form/form/update', [App\Http\Controllers\FormController::class, 'updateRequestedForm']);
        Route::post('dynamic_form/update_form', [App\Http\Controllers\FormController::class, 'requestedFormUpdate']);
        Route::get('important-notice', [App\Http\Controllers\HomeController::class, 'importantNotice']);
        Route::get('showRequestFormForBOt', [App\Http\Controllers\FormController::class, 'showRequestFormForBOt']);
        Route::post('dynamic_form/update_form_for_bot', [App\Http\Controllers\FormController::class, 'updateFormForBot']);

        // Status DynamicForm  routes
        Route::post('dynamic_form/store_form', [App\Http\Controllers\FormController::class, 'storeRequestForm']);
        Route::get('status_requested_form/view/{id}', [App\Http\Controllers\FormController::class, 'viewStatusForm']);
        Route::get('status_requested_form/edit/{id}', [App\Http\Controllers\FormController::class, 'editStatusForm']);
        Route::post('status_dynamic_form/update_form/{id}', [App\Http\Controllers\FormController::class, 'updateStatusRequestedForm']);

        // Dynamic form builder CRUD Operation for service request
        Route::get('dynamic_form', [App\Http\Controllers\FormController::class, 'formList']);
        Route::get('dynamic_form/add', [App\Http\Controllers\FormController::class, 'add']);
        Route::post('jx-form-list', [App\Http\Controllers\FormController::class, 'ajaxformList']);
        Route::post('ajaxAddForm', [App\Http\Controllers\FormController::class, 'ajaxAddForm']);
        Route::any('dynamic_form/edit/{id}', [App\Http\Controllers\FormController::class, 'editForm']);
        Route::get('dynamic_form/delete/{id}', [App\Http\Controllers\FormController::class, 'deleteForm']);
        Route::post('dynamic_form/update', [App\Http\Controllers\FormController::class, 'updateForm']);
        Route::get('dynamic_form_history/{id}', [App\Http\Controllers\FormController::class, 'formListHistoryView']);
        Route::post('jx-form-list-history', [App\Http\Controllers\FormController::class, 'ajaxformListHistory']);
        Route::any('dynamic_form/view/history/{id}', [App\Http\Controllers\FormController::class, 'historyForm']);

        /**Technician clock log */
        Route::any('technician/log-report/{id}', [App\Http\Controllers\Ticket\TechnicianActivityLogController::class, 'technicianLogReport'])->name('logReport');
        Route::any('technician/jx-get-user-log-report/{id}', [App\Http\Controllers\Ticket\TechnicianActivityLogController::class, 'getTechnicianLogData'])->name('getUserlogs');
        Route::get('/technician/logs/export/{id}', [App\Http\Controllers\Ticket\TechnicianActivityLogController::class, 'downloadTechLogs'])->name('downloadTechLogs');

        // Group Auto allocation start
        Route::any('auto-allocation-groups', [App\Http\Controllers\Ticket\AutoAllocationController::class, 'index'])->name('auto-allocation-groups');
        Route::any('jx-add-user-group', [App\Http\Controllers\Ticket\AutoAllocationController::class, 'addUserGroup']);
        Route::get('auto-allocation-export', [App\Http\Controllers\Ticket\AutoAllocationController::class, 'exportAutoAllocation']);
        Route::any('jx-get-user-allocation-groups', [App\Http\Controllers\Ticket\AutoAllocationController::class, 'getUserGroup'])->name('getUserALlocationGroups');
        Route::any('jx-get-user-allocation-groups-select2', [App\Http\Controllers\Ticket\AutoAllocationController::class, 'getUserGroupSelect2'])->name('getUserGroupSelect2');
        Route::any('user-allocation-groups/manage-users/{groupId}', [App\Http\Controllers\Ticket\AutoAllocationController::class, 'manageGroupUsers']);
        Route::any('get-company-wise-location', [App\Http\Controllers\Ticket\AutoAllocationController::class, 'getCompanyWiseLocation']);
        Route::any('jx-add-group-user', [App\Http\Controllers\Ticket\AutoAllocationController::class, 'addGroupUser']);
        Route::any('jx-get-user-allocation-members', [App\Http\Controllers\Ticket\AutoAllocationController::class, 'getGroupUser']);
        Route::any('jx-group-user-delete/{recordId}', [App\Http\Controllers\Ticket\AutoAllocationController::class, 'deleteGroupUser']);
        Route::any('jx-group-delete/{recordId}', [App\Http\Controllers\Ticket\AutoAllocationController::class, 'deleteGroup']);
        Route::any('jx-get-group-details/{recordId}', [App\Http\Controllers\Ticket\AutoAllocationController::class, 'getGroupDetails']);
        Route::any('jx-edit-group-details/{recordId}', [App\Http\Controllers\Ticket\AutoAllocationController::class, 'editGroupDetails']);
        Route::any('jx-get-get-group-member-details/{recordId}', [App\Http\Controllers\Ticket\AutoAllocationController::class, 'getGroupMemberDetails']);
        Route::any('jx-edit-group-member-details/{recordId}', [App\Http\Controllers\Ticket\AutoAllocationController::class, 'editGroupMemberDetails']);
        Route::post('jx-clone-allocation-group', [App\Http\Controllers\Ticket\AutoAllocationController::class, 'cloneAllocationGroup'])->name('cloneAllocationGroup');
        Route::any('auto-allocation-groups_history/{id}', [App\Http\Controllers\Ticket\AutoAllocationController::class, 'historyView']);
        Route::post('jx-get-user-allocation-groups-history', [App\Http\Controllers\Ticket\AutoAllocationController::class, 'getUserGroupHistory']);
        Route::any('user-allocation-groups/manage-users/history/{groupId}', [App\Http\Controllers\Ticket\AutoAllocationController::class, 'manageGroupUsersHistoryView']);
        Route::post('jx-get-user-allocation-members-history', [App\Http\Controllers\Ticket\AutoAllocationController::class, 'getGroupUserHistory']);
        Route::any('auto-allocation-import', [App\Http\Controllers\Ticket\AutoAllocationController::class, 'allocationGroupImport'])->name('auto-allocation-import');
        Route::any('auto-allocation-member-import', [App\Http\Controllers\Ticket\AutoAllocationController::class, 'usersImport'])->name('auto-allocation-member-import');
        // Group Auto allocation end

        /* settings */
        Route::group(['prefix' => 'new-settings'], function () {
            Route::get('new_index', [App\Http\Controllers\SettingsController::class, 'new_index']);
            Route::post('save_changes', [App\Http\Controllers\SettingsController::class, 'saveChanges']);
            Route::post('save_notification', [App\Http\Controllers\SettingsController::class, 'saveNotification']);
            Route::post('ni-not-detected-notification', [App\Http\Controllers\SettingsController::class, 'niNotDetectedNotification']);
            Route::post('smtp-config', [App\Http\Controllers\SettingsController::class, 'smtpConfig']);
        });
        Route::get('settings/edit', [App\Http\Controllers\SettingsController::class, 'edit']);
        Route::any('settings-summery-tbody', [App\Http\Controllers\SettingsController::class, 'summerybody']);
        Route::any('settings/editgeneral', [App\Http\Controllers\SettingsController::class, 'generalSettings']);
        Route::any('settings/editassetsettings', [App\Http\Controllers\SettingsController::class, 'assetSettings']);
        Route::any('settings/editldapsettings', [App\Http\Controllers\SettingsController::class, 'ldapSettings']);
        Route::any('settings/editsettings', [App\Http\Controllers\SettingsController::class, 'settingsEdit']);

        // Route::get('setting', [App\Http\Controllers\SettingController::class, 'edit']);
        Route::post('save_changes', [App\Http\Controllers\SettingController::class, 'saveChanges']);
        Route::any('ldap-get/{id}', [App\Http\Controllers\SettingController::class, 'ldapget']);
        Route::any('ldap-edit/{id}', [App\Http\Controllers\SettingController::class, 'ldapedit']);
        Route::any('general-edit/{id}', [App\Http\Controllers\SettingController::class, 'generaledit']);
        Route::any('general-get/{id}', [App\Http\Controllers\SettingController::class, 'generalget']);

        // Procurement Account Type
        Route::get('procure-account-type', [App\Http\Controllers\ProcurementController::class, 'getIndex']);
        Route::post('jx-account-types', [App\Http\Controllers\ProcurementController::class, 'ajaxAccountTypes']);
        Route::post('jx-account-types-add', [App\Http\Controllers\ProcurementController::class, 'ajaxAddAccountType']);
        Route::get('jx-account-types-get/{id}', [App\Http\Controllers\ProcurementController::class, 'ajaxGetAccountType']);
        Route::post('jx-account-types-edit/{id}', [App\Http\Controllers\ProcurementController::class, 'ajaxEditAccountType']);
        Route::get('jx-account-types-delete/{id}', [App\Http\Controllers\ProcurementController::class, 'deleteAccountType']);

        // Holidays
        Route::get('holidays', [App\Http\Controllers\HolidaysController::class, 'getIndex']);
        Route::post('jx-holidays', [App\Http\Controllers\HolidaysController::class, 'ajaxHolidayList']);
        Route::post('jx-holidays-add', [App\Http\Controllers\HolidaysController::class, 'ajaxAddHoliday']);
        Route::get('jx-holidays-get/{id}', [App\Http\Controllers\HolidaysController::class, 'ajaxGetHoliday']);
        Route::post('jx-holidays-edit/{id}', [App\Http\Controllers\HolidaysController::class, 'ajaxEditHoliday']);
        Route::get('jx-holidays-delete/{id}', [App\Http\Controllers\HolidaysController::class, 'deleteHoliday']);
        Route::get('jx-holidays-countries', [App\Http\Controllers\HolidaysController::class, 'ajaxGetCountries']);
        Route::get('jx-holidays-locations', [App\Http\Controllers\HolidaysController::class, 'ajaxLocationsByCountries']);
        Route::get('export-holidays', [App\Http\Controllers\HolidaysController::class, 'holidayExportExcel']);

        // Custom Tax
        Route::get('custom_tax', [App\Http\Controllers\CustomTaxController::class, 'getIndex']);
        Route::post('custom_tax/getCustomTax', [App\Http\Controllers\CustomTaxController::class, 'ajaxCustomTax']);
        Route::post('custom_tax/save', [App\Http\Controllers\CustomTaxController::class, 'saveCustomTax']);
        Route::post('custom_tax/remove', [App\Http\Controllers\CustomTaxController::class, 'removeCustomTax']);
        Route::post('custom_tax/update', [App\Http\Controllers\CustomTaxController::class, 'updateCustomTax']);
        Route::get('custom_tax/edit', [App\Http\Controllers\CustomTaxController::class, 'editCustomTax']);
        Route::post('custom_tax/remove-element', [App\Http\Controllers\CustomTaxController::class, 'removeCustomElement']);
        Route::any('get-customtax/{custom_id?}', [App\Http\Controllers\CustomTaxController::class, 'getCustomtax']);
        Route::get('get-element/{id}', [App\Http\Controllers\CustomTaxController::class, 'getElement']);

        // announcements
        Route::get('announcements', [App\Http\Controllers\Announcement\AnnouncementsController::class, 'index']);
        Route::post('jx-announcements', [App\Http\Controllers\Announcement\AnnouncementsController::class, 'ajaxAnnouncements']);
        Route::post('jx-announcement-add', [App\Http\Controllers\Announcement\AnnouncementsController::class, 'addAnnouncement']);
        Route::get('jx-announcement-delete/{id}', [App\Http\Controllers\Announcement\AnnouncementsController::class, 'deleteAnnouncement']);
        Route::get('jx-announcement-get/{id}', [App\Http\Controllers\Announcement\AnnouncementsController::class, 'getAnnouncement']);
        Route::post('announcements/changeStatus', [App\Http\Controllers\Announcement\AnnouncementsController::class, 'changeStatus']);
        Route::get('announcements/export', [App\Http\Controllers\Announcement\AnnouncementsController::class, 'export']);
        Route::post('jx-announcement-update/{id}', [App\Http\Controllers\Announcement\AnnouncementsController::class, 'updateAnnouncement']);
        Route::get('announcements/getAllUsers', [App\Http\Controllers\Announcement\AnnouncementsController::class, 'getAllUsers']);
        Route::get('announcements/member-list/{id}', [App\Http\Controllers\Announcement\AnnouncementsController::class, 'memberList']);
        Route::post('announcements/ajax-member-list', [App\Http\Controllers\Announcement\AnnouncementsController::class, 'ajaxMemberList']);
        Route::post('announcements/ajax-member-add', [App\Http\Controllers\Announcement\AnnouncementsController::class, 'ajaxMemberAdd']);
        Route::get('announcements/ajax-member-edit/{id}', [App\Http\Controllers\Announcement\AnnouncementsController::class, 'ajaxMemberEdit']);
        Route::post('announcements/ajax-member-update/{id}', [App\Http\Controllers\Announcement\AnnouncementsController::class, 'ajaxMemberUpdate']);
        Route::get('announcements/ajax-member-delete/{id}', [App\Http\Controllers\Announcement\AnnouncementsController::class, 'ajaxMemberDelete']);
        Route::post('save-announcement-speed', [App\Http\Controllers\Announcement\AnnouncementsController::class, 'saveScrollAmount']);
        Route::get('get-announcement-speed', [App\Http\Controllers\Announcement\AnnouncementsController::class, 'getScrollAmount']);

        /* failed jobs */
        Route::get('failed_jobs', [App\Http\Controllers\FailedjobsController::class, 'index']);
        Route::post('jx-failed_jobs', [App\Http\Controllers\FailedjobsController::class, 'ajaxIndex']);
        Route::any('jx-export-failed-jobs', [App\Http\Controllers\FailedjobsController::class, 'exportFailedJobs']);

        Route::get('jobs', [App\Http\Controllers\JobController::class, 'index']);
        Route::post('ajax-all-jobs', [App\Http\Controllers\JobController::class, 'ajaxAllJobs']);
        Route::get('jobs-export/{search?}', [App\Http\Controllers\JobController::class, 'exportReport']);

        Route::group(['prefix' => 'device-movements'], function () {
            Route::get('/', [App\Http\Controllers\Device\MovementController::class, 'index']);
            Route::post('jx-deviceMovements', [App\Http\Controllers\Device\MovementController::class, 'jxDeviceMovements']);
            Route::get('jx-exportDeviceMovements', [App\Http\Controllers\Device\MovementController::class, 'newExportDeviceMovements']);
            Route::get('count/{id?}', [App\Http\Controllers\Device\MovementController::class, 'jxDeviceMovementsCount']);
            Route::post('jx-untrackedDeviceMovements', [App\Http\Controllers\Device\MovementController::class, 'jxUntrackedDeviceMovements']);
            Route::get('gate-list/{id?}', [App\Http\Controllers\Device\MovementController::class, 'jxDeviceMovementsGate']);
        });

        Route::get('agent/agentClientList', [App\Http\Controllers\AgentController::class, 'agentClientList']);
        Route::get('agent/edit/{id}', [App\Http\Controllers\AgentController::class, 'editAgent']);
        Route::post('jx-agent-list-client', [App\Http\Controllers\AgentController::class, 'ajaxAgentClientList']);
        Route::get('jx-agent-list-client-download/{id}', [App\Http\Controllers\AgentController::class, 'ajaxAgentDownload']);
        Route::get('ticket/custom-fields/{ticketId}', [App\Http\Controllers\CustomFieldController::class, 'getCustomFields']);

        // superuser access only
        Route::group(['middleware' => 'auth.role:SuperAdmin'], function () {

            Route::get('companies', [App\Http\Controllers\CompanyController::class, 'getIndex']);
            Route::get('delete-company/{id}', [App\Http\Controllers\CompanyController::class, 'DeleteCompany']);
            Route::get('restore/{id}', [App\Http\Controllers\CompanyController::class, 'restoreCompany'])->name('restoreCompany');
            Route::any('company/add', [App\Http\Controllers\CompanyController::class, 'addCompany']);
            Route::post('company/ajaxAdd', [App\Http\Controllers\CompanyController::class, 'ajaxAddCompany']);
            Route::post('company/ajaxEdit/{id}', [App\Http\Controllers\CompanyController::class, 'ajaxEditCompany']);
            Route::get('company/ajaxGet/{id}', [App\Http\Controllers\CompanyController::class, 'ajaxGetCompany']);
            Route::any('company/edit/{id}', [App\Http\Controllers\CompanyController::class, 'editCompany']);
            Route::any('company/request-windows-agent/{id}', [App\Http\Controllers\CompanyController::class, 'ajaxRequestWindowsAgent']);
            Route::post('jx-companies', [App\Http\Controllers\CompanyController::class, 'ajaxCompanies']);

            /*Route::get('groups', [App\Http\Controllers\GroupController::class, 'getIndex']);
            Route::get('group/edit/{id}', [App\Http\Controllers\GroupController::class, 'editGroup']);
            Route::post('group/edit/{id}', [App\Http\Controllers\GroupController::class, 'editGroup']);
            Route::get('group/add', [App\Http\Controllers\GroupController::class, 'addGroup']);
            Route::post('group/add', [App\Http\Controllers\GroupController::class, 'addGroup']);
            Route::get('group/delete/{id}', [App\Http\Controllers\GroupController::class, 'deleteGroup']);*/

            Route::get('status-labels', [App\Http\Controllers\LabelController::class, 'getIndex']);
            Route::post('status-list', [App\Http\Controllers\LabelController::class, 'ajaxIndex']);
            Route::post('status-label/ajax-add', [App\Http\Controllers\LabelController::class, 'ajaxAdd']);
            Route::get('status-label/ajax-get-label/{id}', [App\Http\Controllers\LabelController::class, 'ajaxGetLabel']);
            Route::post('status-label/ajax-edit/{id}', [App\Http\Controllers\LabelController::class, 'ajaxEdit']);
            Route::any('status-label/add', [App\Http\Controllers\LabelController::class, 'addLabel']);
            Route::any('status-label/edit/{id}', [App\Http\Controllers\LabelController::class, 'editLabel']);
            Route::get('check-deployable/{id}', [App\Http\Controllers\LabelController::class, 'checkDeployable']);
            Route::get('status-label/delete/{id}', [App\Http\Controllers\LabelController::class, 'deleteLabel']);

            Route::get('custom-field-delete/{id}', [App\Http\Controllers\CustomFieldController::class, 'deleteField']);
            Route::get('custom-fieldset-delete/{id}', [App\Http\Controllers\CustomFieldController::class, 'deleteFldSet']);
            Route::post('custom-fieldset/add', [App\Http\Controllers\CustomFieldController::class, 'addFieldset']);
            Route::any('custom-fieldset/{id}', [App\Http\Controllers\CustomFieldController::class, 'fieldsetDtl']);
            Route::get('custom-fields', [App\Http\Controllers\CustomFieldController::class, 'getIndex']);
            Route::get('custom-field-list', [App\Http\Controllers\CustomFieldController::class, 'ajaxlist']);
            Route::get('custom-fieldset-list', [App\Http\Controllers\CustomFieldController::class, 'ajaxFieldsetlist']);
            Route::post('custom-fields/add', [App\Http\Controllers\CustomFieldController::class, 'addField']);
            Route::get('custom-fields/ajaxGet/{id}', [App\Http\Controllers\CustomFieldController::class, 'ajaxGetField']);
            Route::put('custom-fields/ajaxEdit/{id}', [App\Http\Controllers\CustomFieldController::class, 'editField']);
            Route::get('custom-fields/selectFieldSet/{type}', [App\Http\Controllers\CustomFieldController::class, 'selectFieldSet']);
            Route::get('deleteFieldsetFields', [App\Http\Controllers\CustomFieldController::class, 'deleteFieldsetFields']);
            Route::post('custom-fields-record/reorder', [App\Http\Controllers\CustomFieldController::class, 'updateFieldSetFieldOrder']);
            Route::post('get-fields-by-type', [App\Http\Controllers\CustomFieldController::class, 'getFieldsByType']);

            /**Custom action of custom fields defined */
            Route::get('custom-fields/custom-actions/{id}', [App\Http\Controllers\CustomFieldController::class, 'customActionList']);
            Route::post('custom-fields/custom-actions/{id}', [App\Http\Controllers\CustomFieldController::class, 'customActionList']);
            Route::post('custom-actions/add', [App\Http\Controllers\CustomFieldController::class, 'customActionStore']);
            Route::get('custom-actions/getCustomAction/{id}', [App\Http\Controllers\CustomFieldController::class, 'customActionEdit']);
            Route::post('custom-actions/update/{id}', [App\Http\Controllers\CustomFieldController::class, 'customActionUpdate']);
            Route::get('custom-actions/delete/{id}', [App\Http\Controllers\CustomFieldController::class, 'customActionDelete']);
            Route::get('custom-fields/fetchFieldSet', [App\Http\Controllers\CustomFieldController::class, 'fetchFieldSet']);
            Route::post('fetch-custom-field-reference-field', [App\Http\Controllers\CustomFieldController::class, 'fetchCustomReference']);
            /**Custom action of custom fields defined */

            Route::any('mobile-devices', [App\Http\Controllers\Auth\Api\CommonController::class, 'mobileDevices']);
            Route::any('mobile-devices-list', [App\Http\Controllers\Auth\Api\CommonController::class, 'mobileDevicesList']);
            Route::any('mobile-devices/loc-info/{id}', [App\Http\Controllers\Auth\Api\CommonController::class, 'mobileDevicesInfo']);

            // Roles Start
            Route::get('roles-permission', [App\Http\Controllers\RolesPermissionController::class, 'index'])->name('role-permission');
            Route::post('get-roles', [App\Http\Controllers\RolesPermissionController::class, 'getAllRoles'])->name('getRoles');
            Route::get('edit-role/{roleId}', [App\Http\Controllers\RolesPermissionController::class, 'roleView'])->name('roleView');
            Route::any('add-role', [App\Http\Controllers\RolesPermissionController::class, 'addRole']);
            Route::post('update-roles', [App\Http\Controllers\RolesPermissionController::class, 'updateRole'])->name('updateRole');
            Route::post('clone-role', [App\Http\Controllers\RolesPermissionController::class, 'cloneRole']);
            Route::get('delete-role/{roleId}', [App\Http\Controllers\RolesPermissionController::class, 'deleteRole'])->name('deleteRole');
            Route::get('user-permissions', [App\Http\Controllers\RolesPermissionController::class, 'userPermissions'])->name('userPermissions');
            Route::post('update-user-permission', [App\Http\Controllers\RolesPermissionController::class, 'updateUserPermissions'])->name('updateUserPermissions');
            // Roles End 
        });

        Route::group(['middleware' => ['auth.role:SuperAdmin', 'littlegatekeeper']], function () {
            Route::get('agent/list', [App\Http\Controllers\AgentController::class, 'agentList']);
            Route::post('jx-agent-list', [App\Http\Controllers\AgentController::class, 'ajaxagentList']);
            Route::get('agent/delete/{id}', [App\Http\Controllers\AgentController::class, 'deleteAgent']);
            Route::post('agent/create', [App\Http\Controllers\AgentController::class, 'createAgent']);
            Route::post('agent/update', [App\Http\Controllers\AgentController::class, 'updateAgent']);
            Route::post('agent/logout', [App\Http\Controllers\AgentController::class, 'logout']);
        });
        /*software patch */
        Route::get('sw_patch/group', [App\Http\Controllers\Device\SWPatchGroupController::class, 'index']);
        Route::get('sw_patch/get_group_devices', [App\Http\Controllers\Device\SWPatchGroupController::class, 'getGroupDevices']);
        Route::post('sw_patch/create_group', [App\Http\Controllers\Device\SWPatchGroupController::class, 'storeGroup']);
        Route::get('sw_patch/edit_group/{id}', [App\Http\Controllers\Device\SWPatchGroupController::class, 'editGroup']);
        Route::get('sw_patch/get_device_groups', [App\Http\Controllers\Device\SWPatchGroupController::class, 'getDeviceGroups']);
        Route::get('sw_patch/delete_group/{id}', [App\Http\Controllers\Device\SWPatchGroupController::class, 'deleteGroup']);
        Route::post('sw_patch/update_group', [App\Http\Controllers\Device\SWPatchGroupController::class, 'updateGroup']);
        Route::post('sw_patch/patch_management_device_group', [App\Http\Controllers\Device\SWPatchGroupController::class, 'patchManagementDeviceGroup']);
        Route::get('sw_patch/list', [App\Http\Controllers\Device\SWPatchController::class, 'swPatchList']);
        Route::get('sw_patch/patch_request/list/{id}', [App\Http\Controllers\Device\SWPatchController::class, 'swPatchReqList']);
        Route::post('sw_patch/patch_request/jx-list', [App\Http\Controllers\Device\SWPatchController::class, 'ajaxSWPatchReqlist']);
        Route::get('sw_patch/patch_request_device/list/{id}', [App\Http\Controllers\Device\SWPatchController::class, 'swPatchReqDeviceList']);
        Route::get('sw_patch/patch_request_device/getReportData', [App\Http\Controllers\Device\SWPatchController::class, 'getReportData'])->name('getReportData');
        Route::get('sw_patch/patch_request_device/getDeviceLogs/{patchReqId}/{device_id}', [App\Http\Controllers\Device\SWPatchController::class, 'getDeviceLogs']);
        Route::post('jx-swpatch-list', [App\Http\Controllers\Device\SWPatchController::class, 'ajaxSWPatchlist']);
        Route::get('sw_patch/delete/{id}', [App\Http\Controllers\Device\SWPatchController::class, 'deleteSWPatch']);
        Route::post('sw_patch/create', [App\Http\Controllers\Device\SWPatchController::class, 'createSWPatch']);
        Route::get('sw_patch/edit/{id}', [App\Http\Controllers\Device\SWPatchController::class, 'editSWPatch']);
        Route::post('sw_patch/update', [App\Http\Controllers\Device\SWPatchController::class, 'updateSWPatch']);
        Route::get('jx-software-list-client-download/{id}', [App\Http\Controllers\Device\SWPatchController::class, 'ajaxSWPatchDownload']);
        Route::get('sw_patch/get-os-version', [App\Http\Controllers\Device\SWPatchController::class, 'jxOsVersion']);
        Route::get('sw_patch/get-os', [App\Http\Controllers\Device\SWPatchController::class, 'jxOs']);
        Route::get('sw_patch/getDeviceForDropDown', [App\Http\Controllers\Device\SWPatchController::class, 'getDeviceForDropDown']);
        Route::get('sw_patch/getDeviceForGroup', [App\Http\Controllers\Device\SWPatchController::class, 'getDeviceForGroup']);
        Route::post('sw_patch/bulk_patch', [App\Http\Controllers\Device\SWPatchController::class, 'bulkPatch']);
        Route::post('sw_patch/patch_request_device/jx-list-log', [App\Http\Controllers\Device\SWPatchController::class, 'ajaxSWPatchLoglist']);
        Route::get('sw_patch/patch_request/delete/{id}', [App\Http\Controllers\Device\SWPatchController::class, 'deleteSWPatchRequest']);
        Route::get('sw_patch/get-version-filter', [App\Http\Controllers\Device\SWPatchController::class, 'FilterjxAppVersion']);
        Route::get('getSoftwareForPatchSystem', [App\Http\Controllers\Device\SWPatchController::class, 'getSoftwareForPatchSystem']);
        Route::get('getVersionForPatchSystem', [App\Http\Controllers\Device\SWPatchController::class, 'getVersionForPatchSystem']);

        Route::get('agent/list/login', [App\Http\Controllers\AgentController::class, 'loginview']);
        Route::post('secretapage/login/addCredentials', [App\Http\Controllers\AgentController::class, 'addCredentials'])->name('addCredentials');
        Route::get('agent/agent-client-list', [App\Http\Controllers\AgentController::class, 'agentClientList']);
        Route::post('jx-agent-list-client', [App\Http\Controllers\AgentController::class, 'ajaxAgentClientList']);
        Route::get('jx-agent-list-client-download/{id}', [App\Http\Controllers\AgentController::class, 'ajaxAgentDownload']);

        Route::get('user/basic-info/{id}/{level?}', [App\Http\Controllers\UserController::class, 'basicInfo']);

        Route::get('ajaxVendorDashboard', [App\Http\Controllers\HomeController::class, 'ajaxVendorDashboard']);
        Route::get('ajaxTaskDashboard', [App\Http\Controllers\HomeController::class, 'ajaxTaskDashboard']);
        Route::post('ajaxVendorWaitingList', [App\Http\Controllers\HomeController::class, 'ajaxVendorWaitingReqlist']);
        Route::get('suppliers', [App\Http\Controllers\SupplierController::class, 'getIndex']);
        Route::get('getSupplierByQuery/{type?}', [App\Http\Controllers\SupplierController::class, 'getSupplierByQuery']);
        Route::post('ajax-suppliers-list', [App\Http\Controllers\SupplierController::class, 'ajaxSupplierList']);
        Route::get('suppliers/delete/{id}', [App\Http\Controllers\SupplierController::class, 'deleteSupplier']);
        Route::any('supplier/add', [App\Http\Controllers\SupplierController::class, 'addSupplier']);
        Route::any('supplier/edit/{id}', [App\Http\Controllers\SupplierController::class, 'editSupplier']);
        Route::any('supplier/get-info/{id}', [App\Http\Controllers\SupplierController::class, 'getSupplier']);
        Route::get('supplier-image-download/{id}', [App\Http\Controllers\SupplierController::class, 'attachmentDownload']);
        Route::get('supplier-image-delete/{id}', [App\Http\Controllers\Supplier\AccountController::class, 'attachmentDelete']);
        Route::get('supplier/info/{id}', [App\Http\Controllers\SupplierController::class, 'info']);
        Route::any('supplier/address/add/{supplier_id}', [App\Http\Controllers\Supplier\AddressController::class, 'add']);
        Route::any('supplier/address/edit/{supplier_id}/{address_id}', [App\Http\Controllers\Supplier\AddressController::class, 'edit']);
        Route::any('supplier/address/delete/{supplier_id}/{address_id}', [App\Http\Controllers\Supplier\AddressController::class, 'delete']);
        Route::any('supplier/address/makePrimary/{supplier_id}/{address_id}', [App\Http\Controllers\Supplier\AddressController::class, 'makePrimary']);
        Route::any('supplier/number/delete/{supplier_id}/{number_id}', [App\Http\Controllers\Supplier\NumberController::class, 'delete']);
        Route::any('supplier/number/edit/{supplier_id}/{number_id}', [App\Http\Controllers\Supplier\NumberController::class, 'edit']);
        Route::any('supplier/number/makePrimary/{supplier_id}/{number_id}', [App\Http\Controllers\Supplier\NumberController::class, 'makePrimary']);
        Route::any('supplier/account/add/{supplier_id}', [App\Http\Controllers\Supplier\AccountController::class, 'add']);
        Route::any('supplier/account/edit/{supplier_id}/{account_id}', [App\Http\Controllers\Supplier\AccountController::class, 'edit']);
        Route::any('supplier/account/delete/{supplier_id}/{account_id}', [App\Http\Controllers\Supplier\AccountController::class, 'delete']);
        Route::any('supplier/account/makePrimary/{supplier_id}/{account_id}', [App\Http\Controllers\Supplier\AccountController::class, 'makePrimary']);
        Route::any('supplier/{item_type}/add/{supplier_id}', [App\Http\Controllers\Supplier\NumberController::class, 'add']);
        Route::get('export-suppliers', [App\Http\Controllers\SupplierController::class, 'SupplierExport']);
        Route::any('import-suppliers', [App\Http\Controllers\SupplierController::class, 'supplierImport']);
        Route::any('supplier/bulk-import-info', [App\Http\Controllers\Bulk\ConfigBulkActionController::class, 'ajaxBulkImportSupplierInfo']);
        Route::any('getBusinessCategories', [App\Http\Controllers\SupplierController::class, 'ajaxBusinessCategoryDetail']);
        Route::post('supplier/add-courier-details/{supplier?}', [App\Http\Controllers\SupplierController::class, 'addCourierDetails']);
        Route::get('supplier/get-courier-details/{id}', [App\Http\Controllers\SupplierController::class, 'ajaxCourierList']);
        Route::get('supplier/panList/{supplier_id}', [App\Http\Controllers\SupplierController::class, 'ajaxGetPanList']);
        Route::get('supplier/gstList/{supplier_id}', [App\Http\Controllers\SupplierController::class, 'ajaxGetGstList']);
        Route::get('supplier/addressList/{supplier_id}', [App\Http\Controllers\SupplierController::class, 'ajaxGetAddressData']);
        Route::get('supplier/accountList/{supplier_id}', [App\Http\Controllers\SupplierController::class, 'ajaxGetAccountData']);

        // admin access only
        // Route::group(['middleware' => 'auth.role:SuperAdmin'], function () {
        Route::get('manufactures', [App\Http\Controllers\Config\ManufactureController::class, 'getIndex']);
        Route::any('manufactures/ajax-get/{id}', [App\Http\Controllers\Config\ManufactureController::class, 'ajaxGetManufacturer']);
        Route::any('manufactures/ajax-get-list', [App\Http\Controllers\Config\ManufactureController::class, 'ajaxGetList']);
        Route::any('manufacturer/ajax-add', [App\Http\Controllers\Config\ManufactureController::class, 'ajaxAdd']);
        Route::any('manufacturer/ajax-edit/{id}', [App\Http\Controllers\Config\ManufactureController::class, 'ajaxEdit']);
        Route::any('manufacture/add', [App\Http\Controllers\Config\ManufactureController::class, 'addManufacture']);
        Route::any('manufacture/edit/{id}', [App\Http\Controllers\Config\ManufactureController::class, 'editManufacture']);
        Route::get('manufacture/del-attachment/{id}', [App\Http\Controllers\Config\ManufactureController::class, 'deleteManufactureAttachment']);
        Route::get('delete-manufacture/{id}', [App\Http\Controllers\Config\ManufactureController::class, 'deleteManufacture']);
        Route::get('export-manufactures', [App\Http\Controllers\Config\ManufactureController::class, 'ManufactureExport']);

        Route::get('categories', [App\Http\Controllers\Config\CategoryController::class, 'getIndex']);
        Route::any('category/add', [App\Http\Controllers\Config\CategoryController::class, 'addCategory']);
        Route::any('category/ajax-list', [App\Http\Controllers\Config\CategoryController::class, 'ajaxCategories']);
        Route::any('category/ajax-add', [App\Http\Controllers\Config\CategoryController::class, 'ajaxAddCategory']);
        Route::any('category/ajax-edit/{id}', [App\Http\Controllers\Config\CategoryController::class, 'ajaxEditCategory']);
        Route::any('category/ajax-get/{id}', [App\Http\Controllers\Config\CategoryController::class, 'ajaxGetCategory']);
        Route::any('category/edit/{id}', [App\Http\Controllers\Config\CategoryController::class, 'editCategory']);
        Route::get('delete-category/{id}', [App\Http\Controllers\Config\CategoryController::class, 'deleteCategory']);
        Route::get('export-category', [App\Http\Controllers\Config\CategoryController::class, 'CategoryExport']);
        Route::get('category/history/{id}', [App\Http\Controllers\Config\CategoryController::class, 'categoryHistory']);
        Route::post('jx_categories_info', [App\Http\Controllers\Config\CategoryController::class, 'ajaxCategoryHistory']);
        
        Route::get('models', [App\Http\Controllers\Config\ModelController::class, 'getIndex']);
        Route::any('model-list', [App\Http\Controllers\Config\ModelController::class, 'ajaxIndex']);
        Route::get('model-delete/{id}', [App\Http\Controllers\Config\ModelController::class, 'deleteModel']);
        Route::get('model-restore/{id}', [App\Http\Controllers\Config\ModelController::class, 'restoreModel']);
        Route::any('model/add', [App\Http\Controllers\Config\ModelController::class, 'addModel']);
        Route::any('model/ajax-add', [App\Http\Controllers\Config\ModelController::class, 'ajaxAddModel']);
        Route::any('model/ajax-edit/{id}', [App\Http\Controllers\Config\ModelController::class, 'ajaxEditModel']);
        Route::any('model/ajax-get/{id}', [App\Http\Controllers\Config\ModelController::class, 'ajaxGetModel']);
        Route::any('model/edit/{id}', [App\Http\Controllers\Config\ModelController::class, 'ajaxGetModel']);
        Route::get('models/delete-image/{id}', [App\Http\Controllers\Config\ModelController::class, 'deleteModelImage']);
        Route::get('export-model-details', [App\Http\Controllers\Config\ModelController::class, 'exportModelDetails']);
        Route::get('model/history/{id}', [App\Http\Controllers\Config\ModelController::class, 'modelHistory']);
        Route::post('jx_model_info', [App\Http\Controllers\Config\ModelController::class, 'ajaxModelHistory']);
        Route::get('form-data', [App\Http\Controllers\Config\ModelController::class, 'getFormData']);

        Route::get('depreciations', [App\Http\Controllers\DepreciationController::class, 'getIndex']);
        Route::get('delete-depreciation/{id}', [App\Http\Controllers\DepreciationController::class, 'deleteDepreciation']);
        Route::get('depreciation/add', [App\Http\Controllers\DepreciationController::class, 'addDepreciation']);
        Route::post('depreciation/add', [App\Http\Controllers\DepreciationController::class, 'addDepreciation']);
        Route::get('depreciation/edit/{id}', [App\Http\Controllers\DepreciationController::class, 'editDepreciation']);
        Route::post('depreciation/edit/{id}', [App\Http\Controllers\DepreciationController::class, 'editDepreciation']);

        Route::any('depreciation/ajax-add', [App\Http\Controllers\DepreciationController::class, 'ajaxAddDepreciation']);
        Route::any('depreciation/ajax-get/{id}', [App\Http\Controllers\DepreciationController::class, 'ajaxGetDepreciation']);
        Route::any('depreciation/ajax-edit/{id}', [App\Http\Controllers\DepreciationController::class, 'ajaxEditDepreciation']);
        Route::any('depreciation/ajax-list', [App\Http\Controllers\DepreciationController::class, 'ajaxDepreciations']);

        Route::post('license/add', [App\Http\Controllers\LicenseController::class, 'addLicense']);
        Route::get('license/edit/{id}/{action?}', [App\Http\Controllers\LicenseController::class, 'showedit']);
        Route::post('license/update/{id}', [App\Http\Controllers\LicenseController::class, 'update']);
        Route::post('license/checkout/{id}', [App\Http\Controllers\LicenseController::class, 'checkout']);
        Route::post('licenseseat/checkout/{id}', [App\Http\Controllers\LicenseController::class, 'checkoutSeat']);
        Route::get('license/detail/{id}', [App\Http\Controllers\LicenseController::class, 'licenceDetail']);
        Route::post('licenseseat/checkin/{id}', [App\Http\Controllers\LicenseController::class, 'checkinLicenseSeat']);
        Route::get('licenseseat/checkin/{id}', [App\Http\Controllers\LicenseController::class, 'redirectForPopUP']);
        Route::any('jx-licenseSeat/{id}', [App\Http\Controllers\LicenseController::class, 'ajaxlicenseSeat']);
        Route::any('export-licenseSeat/{id}', [App\Http\Controllers\LicenseController::class, 'downloadlicenseSeats']);
        Route::any('export-pdf-licenseSeat/{id}', [App\Http\Controllers\LicenseController::class, 'downloadpdflicenseSeats']);
        Route::any('jx-licenseCheckoutHistory/{id}', [App\Http\Controllers\LicenseController::class, 'ajaxlicensehistory']);
        Route::post('jx-add-serial-no/{id}', [App\Http\Controllers\LicenseController::class, 'ajaxUpdateSerialNo']);
        Route::any('import-licenses', [App\Http\Controllers\LicenseController::class, 'licensesImport'])->name('licensesImport');
        Route::any('licenses/import_info', [App\Http\Controllers\Bulk\BulkActionController::class, 'licenseImportInfo']);
        Route::get('network-license/{id}', [App\Http\Controllers\LicenseController::class, 'getNetworkLicenseIndex']);
        Route::post('jx-network-license', [App\Http\Controllers\LicenseController::class, 'ajaxNetworkLicenselist']);
        Route::get('download-network-license/{id}', [App\Http\Controllers\LicenseController::class, 'downloadNetworkCount']);
        Route::resources([
            'licenses' => 'LicenseController',
            'consumables' => \App\Http\Controllers\ConsumableController::class,
            'settings' => \App\Http\Controllers\SettingsController::class,
        ]); 
        Route::get('getCategoryOptions', [App\Http\Controllers\LicenseController::class, 'getCategoryOptions']);
        Route::get('getDepreciationOptions', [App\Http\Controllers\LicenseController::class, 'getDepreciationOptions']);
        Route::get('getSupplierByAjax', [App\Http\Controllers\LicenseController::class, 'getSupplierByAjax']); 
        Route::get('currencies/options', [App\Http\Controllers\LicenseController::class, 'getCurrencyOptions'])->name('currencies.options');
        Route::get('getcompanyoptions', [App\Http\Controllers\LicenseController::class, 'getCompanyByAjax']);
        Route::get('licenses', [App\Http\Controllers\LicenseController::class, 'getIndex']);
        Route::get('licenses-restore/{id}', [App\Http\Controllers\LicenseController::class, 'restoreLicense']);
        Route::get('export-licenses', [App\Http\Controllers\LicenseController::class, 'licensesExport']);
        Route::get('export-licenses-pdf', [App\Http\Controllers\LicenseController::class, 'licensesExportPDF']);
        Route::get('license/delete/{id}', [App\Http\Controllers\LicenseController::class, 'deleteLicense']);
        Route::any('licenseseat/delete/{id}', [App\Http\Controllers\LicenseController::class, 'deleteLicenseSeat']);
        Route::post('jx-licenses', [App\Http\Controllers\LicenseController::class, 'ajaxIndex']);
        Route::any('license/info-summery/{id}', [App\Http\Controllers\LicenseController::class, 'infosummery']);
        Route::any('licenses/bulk/checkout', [App\Http\Controllers\Bulk\LicenseController::class, 'bulkCheckout']);
        Route::any('licenses/bulk/checkin', [App\Http\Controllers\Bulk\LicenseController::class, 'bulkCheckin']);
        Route::any('licenses/bulk/checkout-info', [App\Http\Controllers\Bulk\BulkActionController::class, 'licenseImportInfo']);
        Route::any('licenses/bulk/checkin-info', [App\Http\Controllers\Bulk\BulkActionController::class, 'licenseImportInfo']);
        Route::get('getCustomFieldsByCategory/{id}/{type}/{type_id?}', [App\Http\Controllers\CustomFieldController::class, 'getCustomFieldsByCategory']);
        Route::any('download-custom-fields-code-licenses', [App\Http\Controllers\LicenseController::class, 'downloadCustomFieldsCodeLicenses']);
        Route::post('jx-requestable-license', [App\Http\Controllers\LicenseController::class, 'ajaxRequestableLicense']);
        Route::any('request-license/{id}', [App\Http\Controllers\LicenseController::class, 'requestLicense']);

        Route::post('add-purchase-license', [App\Http\Controllers\LicenseController::class, 'addPurchaseLicense']);
        Route::post('purchase-license-list', [App\Http\Controllers\LicenseController::class, 'ajaxPurchaseLicenseIndex']);
        Route::post('delete-purchase-license', [App\Http\Controllers\LicenseController::class, 'deletePurchaseLicense']);
        Route::get('purchase-attachment-license-view/{id}', [App\Http\Controllers\LicenseController::class, 'viewLicensePurchaseAttachments']);
        Route::post('update-purchase-license/{id}', [App\Http\Controllers\LicenseController::class, 'updatePurchaseLicense']);
        Route::get('get-purchase-license/{id}', [App\Http\Controllers\LicenseController::class, 'getPurchaseLicenseDetail']);
        Route::get('verify-purchase-license/{id}', [App\Http\Controllers\LicenseController::class, 'verifyPurchaseLicenseDetail']);

        Route::get('locations', [App\Http\Controllers\Config\LocationController::class, 'getIndex']);
        Route::get('location-delete/{id}', [App\Http\Controllers\Config\LocationController::class, 'deleteLocation']);
        Route::post('jx-locations', [App\Http\Controllers\Config\LocationController::class, 'ajaxIndex']);
        Route::any('location/add', [App\Http\Controllers\Config\LocationController::class, 'addLocation']);
        Route::any('location/ajax-add', [App\Http\Controllers\Config\LocationController::class, 'ajaxAddLocation']);
        Route::any('location/ajax-edit/{id}', [App\Http\Controllers\Config\LocationController::class, 'ajaxEditLocation']);
        Route::any('location/ajax-get/{id}', [App\Http\Controllers\Config\LocationController::class, 'ajaxGetLocation']);
        Route::any('location/edit/{id}', [App\Http\Controllers\Config\LocationController::class, 'editLocation']);
        Route::get('export-locations', [App\Http\Controllers\Config\LocationController::class, 'LocationExport']);
        Route::any('location-import', [App\Http\Controllers\Config\LocationController::class, 'locationImport']);
        Route::any('locations/bulk-import-info', [App\Http\Controllers\Bulk\ConfigBulkActionController::class, 'ajaxBulkImportLocationInfo']);

        /* internal places */
        Route::get('internal-places', [App\Http\Controllers\PlaceController::class, 'getIndex']);
        Route::any('jx-internal-places', [App\Http\Controllers\PlaceController::class, 'ajaxIndex']);
        Route::any('internal-places/ajax-add', [App\Http\Controllers\PlaceController::class, 'ajaxAdd']);
        Route::any('internal-places/ajax-edit/{id}', [App\Http\Controllers\PlaceController::class, 'ajaxEdit']);
        Route::any('internal-places/ajax-get/{id}', [App\Http\Controllers\PlaceController::class, 'ajaxGet']);
        Route::get('internal-places/ajax-delete/{id}', [App\Http\Controllers\PlaceController::class, 'ajaxDelete']);
        Route::any('internal-places/import', [App\Http\Controllers\PlaceController::class, 'placeImport'])->name('place-import');
        Route::get('export-internal-places', [App\Http\Controllers\PlaceController::class, 'placeExportExcel']);
        Route::any('internal-places/bulk-import-info', [App\Http\Controllers\Bulk\ConfigBulkActionController::class, 'ajaxBulkImportInternalPlacesInfo']);

        Route::get('purchases', [App\Http\Controllers\Config\PurchaseController::class, 'getIndex']);
        Route::any('purchase/add', [App\Http\Controllers\Config\PurchaseController::class, 'addPurchase']);
        Route::any('purchase/delete/{id}', [App\Http\Controllers\Config\PurchaseController::class, 'deletePurchase']);
        Route::post('purchase/edit/{id}', [App\Http\Controllers\Config\PurchaseController::class, 'editPurchase']);
        Route::get('purchase/edit/{id}', [App\Http\Controllers\Config\PurchaseController::class, 'getPurchaseDetail']);
        Route::post('jx-purchases', [App\Http\Controllers\Config\PurchaseController::class, 'ajaxPurchases']);
        Route::post('jx-purchase-items-list', [App\Http\Controllers\Config\PurchaseController::class, 'ajaxPurchaseItemsList']);
        Route::get('purchase-file-download/{id}', [App\Http\Controllers\Config\PurchaseController::class, 'attachmentDownload']);
        Route::get('purchase-file-delete/{id}', [App\Http\Controllers\Config\PurchaseController::class, 'attachmentDelete']);
        Route::get('purchase-history/{Id}', [App\Http\Controllers\Config\PurchaseController::class, 'getPurchaseHistory']);
        Route::get('purchase-list-options', [App\Http\Controllers\Config\PurchaseController::class, 'getListOptions']);
        Route::any('purchase/info_tab/{id}', [App\Http\Controllers\Config\PurchaseController::class, 'info']);
        Route::any('purchase/info/{id}', [App\Http\Controllers\Config\PurchaseController::class, 'userDetail']);
        Route::any('purchase-import', [App\Http\Controllers\Config\PurchaseController::class, 'purchaseImport']);
        Route::get('purchase-download', [App\Http\Controllers\Config\PurchaseController::class, 'download']);
        Route::any('purchases/bulk-import-info', [App\Http\Controllers\Bulk\ConfigBulkActionController::class, 'ajaxBulkImportPurchaseInfo']);        

        /* File attachment in purchase */
        Route::post('purchase-attachment/add', [App\Http\Controllers\Purchase\AttachmentController::class, 'add']);
        Route::post('purchase-attachment/remove', [App\Http\Controllers\Purchase\AttachmentController::class, 'remove']);
        Route::get('purchase-attachment/download/{id}', [App\Http\Controllers\Purchase\AttachmentController::class, 'download']);
        Route::post('purchase-attachment/list', [App\Http\Controllers\Purchase\AttachmentController::class, 'list']);
        Route::get('purchase-attachment/view/{id}', [App\Http\Controllers\Purchase\AttachmentController::class, 'view']);

        // for dropdown options
        Route::get('getCompanyByQuery', [App\Http\Controllers\Procurement\PoCompanyController::class, 'getCompanyByQuery']);
        Route::get('getUnitsByQuery', [App\Http\Controllers\Config\PurchaseController::class, 'getUnitsByQuery']);

        // Blocked IP
        Route::get('blockIp', [App\Http\Controllers\Device\BlockedIpController::class, 'getIndex']);
        Route::get('getIpByAjax', [App\Http\Controllers\Device\BlockedIpController::class, 'getIpByAjax']);
        Route::post('blockIp/add', [App\Http\Controllers\Device\BlockedIpController::class, 'ajaxAddBlockedIp']);
        Route::get('blockIp/get/{ip}', [App\Http\Controllers\Device\BlockedIpController::class, 'ajaxGetBlockedIp']);
        Route::post('jx-blockIp', [App\Http\Controllers\Device\BlockedIpController::class, 'ajaxIndex']);
        Route::any('blockIp/delete/{id}', [App\Http\Controllers\Device\BlockedIpController::class, 'ajaxDelete']);
        Route::post('blockIp/edit/{id}', [App\Http\Controllers\Device\BlockedIpController::class, 'editBlockIp']);
        Route::get('export-blockIp', [App\Http\Controllers\Device\BlockedIpController::class, 'downloadReport']);
 
        /* purchase items */
        Route::any('purchase-item/save', [App\Http\Controllers\Config\PurchaseController::class, 'savePurchaseItem']);
        Route::any('purchase-item/list', [App\Http\Controllers\Config\PurchaseController::class, 'ajaxPurchaseItem']);
        Route::get('purchase-item/get/{id}', [App\Http\Controllers\Config\PurchaseController::class, 'ajaxGetPurchaseItem']);
        Route::post('purchase-item/edit/{id}', [App\Http\Controllers\Config\PurchaseController::class, 'ajaxEditPurchaseItem']);
        Route::get('purchase-item/delete/{id}', [App\Http\Controllers\Config\PurchaseController::class, 'ajaxPurchaseItemDelete']);

        /* File attachment in purchase */
        Route::post('purchase-attachment/add', [App\Http\Controllers\Purchase\AttachmentController::class, 'add']);
        Route::post('purchase-attachment/remove', [App\Http\Controllers\Purchase\AttachmentController::class, 'remove']);
        Route::get('purchase-attachment/download/{id}', [App\Http\Controllers\Purchase\AttachmentController::class, 'download']);
        Route::post('purchase-attachment/list', [App\Http\Controllers\Purchase\AttachmentController::class, 'list']);
        Route::get('purchase-attachment/view/{id}', [App\Http\Controllers\Purchase\AttachmentController::class, 'view']);


        /* Contract Agreements */
        Route::get('lease-agreements', [App\Http\Controllers\Config\LeaseController::class, 'index']);
        Route::any('lease-agreements/jx-index', [App\Http\Controllers\Config\LeaseController::class, 'ajaxIndex']);
        Route::any('lease-agreements/jx-add', [App\Http\Controllers\Config\LeaseController::class, 'ajaxAdd']);
        Route::any('lease-agreements/jx-get/{id}', [App\Http\Controllers\Config\LeaseController::class, 'ajaxGet']);
        Route::any('lease-agreements/jx-edit/{id}', [App\Http\Controllers\Config\LeaseController::class, 'ajaxEdit']);
        Route::any('lease-agreements/jx-delete/{id}', [App\Http\Controllers\Config\LeaseController::class, 'ajaxDelete']);
        Route::get('lease-agreements/download/{id}', [App\Http\Controllers\Config\LeaseController::class, 'download']);
        Route::get('lease-info/{id}', [App\Http\Controllers\Config\LeaseController::class, 'getLeaseInfo']);
        Route::get('lease/download/{id}', [App\Http\Controllers\Config\LeaseController::class, 'downloadhistory']);
        Route::post('jx_leases_info', [App\Http\Controllers\Config\LeaseController::class, 'ajaxGetLeaseInfo']);
        Route::any('getLeaseByQuery', [App\Http\Controllers\Config\LeaseController::class, 'getLeaseByQuery']);


         // for dropdown options
        Route::get('getCategoryByQuery', [App\Http\Controllers\Config\CategoryController::class, 'getCategoryByQuery']);
        Route::get('getModelByCategory', [App\Http\Controllers\DeviceController::class, 'getModelByCategory']);
        Route::get('getDeviceByModel', [App\Http\Controllers\DeviceController::class, 'getDeviceByModel']);
        Route::get('getModelByManufacturer/{id}', [App\Http\Controllers\DeviceController::class, 'getModelByManufacturer']);
        Route::get('getModelByQuery', [App\Http\Controllers\DeviceController::class, 'getModelByQuery']);
        Route::get('getAllocationByQuery', [App\Http\Controllers\Config\AllocationTypeController::class, 'getAllocationByQuery']);
        Route::get('getDeviceTypeByQuery', [App\Http\Controllers\DeviceTypeController::class, 'getDeviceTypeByQuery']);



        /* Threshold */
        Route::get('threshold', [App\Http\Controllers\Config\ThresholdController::class, 'getIndex']);
        Route::any('threshold/edit', [App\Http\Controllers\Config\ThresholdController::class, 'ajaxEdit']);

        Route::get('device-type', [App\Http\Controllers\DeviceTypeController::class, 'index']);
        Route::post('jx-deviceTypes', [App\Http\Controllers\DeviceTypeController::class, 'getDeviceTypes']);
        Route::post('jx-devicetype-add', [App\Http\Controllers\DeviceTypeController::class, 'ajaxAddDeviceType']);
        Route::get('jx-get-devicetype/{id}', [App\Http\Controllers\DeviceTypeController::class, 'ajaxGetDeviceType']);
        Route::post('jx-devicetype-edit/{id}', [App\Http\Controllers\DeviceTypeController::class, 'ajaxEditDeviceType']);
        Route::get('jx-devicetype-delete/{id}', [App\Http\Controllers\DeviceTypeController::class, 'deleteDeviceType']);
        Route::get('export-devicetype', [App\Http\Controllers\DeviceTypeController::class, 'deviceTypeExportExcel']);

        /* Departments */
        Route::get('departments', [App\Http\Controllers\Config\DepartmentController::class, 'getIndex']);
        Route::get('department-attenders/{dept_id}', [App\Http\Controllers\Config\DepartmentController::class, 'getAttenders']);
        Route::get('jx-departments', [App\Http\Controllers\Config\DepartmentController::class, 'ajaxDepartment']);
        Route::any('department/add', [App\Http\Controllers\Config\DepartmentController::class, 'addDepartment']);
        Route::any('department/ajaxadd', [App\Http\Controllers\Config\DepartmentController::class, 'ajaxAddDepartment']);
        Route::any('department/ajaxedit/{id}', [App\Http\Controllers\Config\DepartmentController::class, 'ajaxEditDepartment']);
        Route::any('department/ajaxget/{id}', [App\Http\Controllers\Config\DepartmentController::class, 'ajaxGetDepartment']);
        Route::any('department/edit/{id}', [App\Http\Controllers\Config\DepartmentController::class, 'editDepartment']);
        Route::any('department/detail/{id}', [App\Http\Controllers\Config\DepartmentController::class, 'detailDepartment']);
        Route::any('department/delete/{id}', [App\Http\Controllers\Config\DepartmentController::class, 'deleteDepartment']);
        Route::get('export-departments', [App\Http\Controllers\Config\DepartmentController::class, 'departmentExport']);
        Route::get('getAssetDepartments', [App\Http\Controllers\Config\DepartmentController::class, 'getAssetDepartments'])->name('getAssetDepartments');
        Route::any('department-import', [App\Http\Controllers\Config\DepartmentController::class, 'departmentImport']);
        Route::get('department/getSubCategories', [App\Http\Controllers\Config\DepartmentController::class, 'getSubCategories']);
        Route::any('department/bulk-import-info', [App\Http\Controllers\Bulk\ConfigBulkActionController::class, 'ajaxBulkImportDepartmentInfo']);

        Route::get('users', [App\Http\Controllers\UserController::class, 'getIndex']);
        Route::post('sync-azure-users', [App\Http\Controllers\UserController::class, 'userSyncAzure']);
        Route::get('export-users', [App\Http\Controllers\UserController::class, 'usersExport']);
        Route::get('export-users-pdf', [App\Http\Controllers\UserController::class, 'usersExportPDF']);
        Route::post('users-list', [App\Http\Controllers\UserController::class, 'ajaxIndex'])->name('users-list');
        Route::any('user/add', [App\Http\Controllers\UserController::class, 'addUser']);
        Route::any('user/edit/{id}', [App\Http\Controllers\UserController::class, 'editUser']);
        Route::get('user/getUserForEdit', [App\Http\Controllers\UserController::class, 'getUserForEdit']);
        Route::any('user/delete/{id}', [App\Http\Controllers\UserController::class, 'deleteUser']);
        Route::get('user/print/{id}', [App\Http\Controllers\UserController::class, 'printUser']);
        Route::get('user/printInOut/{id}', [App\Http\Controllers\UserController::class, 'printInOut']);
        Route::any('user/restore/{id}', [App\Http\Controllers\UserController::class, 'restoreUser']);
        Route::any('users/bulk/update', [App\Http\Controllers\Bulk\UserController::class, 'update']);
        Route::any('users/bulk/delete', [App\Http\Controllers\Bulk\UserController::class, 'delete']);
        Route::post('user/bulkUserActivate', [App\Http\Controllers\UserController::class, 'bulkUserActivate']);
        Route::post('user/bulkUserDeactivate', [App\Http\Controllers\UserController::class, 'bulkUserDeactivate']);
        Route::any('user/send-credential/{id}', [App\Http\Controllers\UserController::class, 'sendResetCredential']);
        Route::any('user/merge_users', [App\Http\Controllers\UserController::class, 'mergeUsers']);
        Route::get('user/exportTermConditionPDF', [App\Http\Controllers\UserController::class, 'exportTermConditionPDF']);
        Route::Post('user/addUserNotificationForAnnouncement', [App\Http\Controllers\UserController::class, 'addUserNotificationForAnnouncement']);
        Route::any('users-log', [App\Http\Controllers\UserController::class, 'usersLog'])->name('users-log');
        Route::post('users-log/ajax', [App\Http\Controllers\UserController::class, 'ajaxUsersLog'])->name('users-log-ajax');
        Route::get('user/getUserForEmpdetail', [App\Http\Controllers\UserController::class, 'getUserForEmpdetail']);
        Route::any('user/info/{id}', [App\Http\Controllers\UserController::class, 'userDetail']);
        Route::get('user-history', [App\Http\Controllers\UserController::class, 'userHistory']);
        Route::get('user-history-details', [App\Http\Controllers\UserController::class, 'userHistoryDetails']);
        // Route::get('user/basic-info/{id}/{level?}', [App\Http\Controllers\UserController::class, 'basicInfo']);
        Route::post('user/info/{main_filter?}', [App\Http\Controllers\UserController::class, 'ajaxTickets']);
        Route::get('user-permissions', [App\Http\Controllers\RolesPermissionController::class, 'userPermissions'])->name('userPermissions');
        Route::post('update-user-permission', [App\Http\Controllers\RolesPermissionController::class, 'updateUserPermissions'])->name('updateUserPermissions');
        Route::get('user/checkUser/{id}', [App\Http\Controllers\UserController::class, 'checkUser'])->name('checkUser');

        Route::get('qrcode-user/{id}', [App\Http\Controllers\UserController::class, 'qrcodeUser']);
        Route::get('print-user-barcode/{ids}', [App\Http\Controllers\UserController::class, 'printBarcodeUser']);
        Route::get('print-user-one-col/{ids}', [App\Http\Controllers\UserController::class, 'printBarcodeOneColUser']);
        Route::get('print-user-two-col/{ids}', [App\Http\Controllers\UserController::class, 'printBarcodeTwoColUser']);
        Route::get('print-user-vertical-col/{ids}', [App\Http\Controllers\UserController::class, 'printVerticalColUser']);

        Route::get('devices/{status?}', [App\Http\Controllers\DeviceController::class, 'getIndex']);
        Route::post('jx-devices', [App\Http\Controllers\DeviceController::class, 'ajaxIndex']);
        Route::get('export-devices', [App\Http\Controllers\DeviceController::class, 'deviceExportExcel']);
        Route::get('export-devices/{type?}', [App\Http\Controllers\DeviceController::class, 'deviceExportExcelForType']);
        Route::get('export-devices-pdf', [App\Http\Controllers\DeviceController::class, 'deviceExportPDF']);
        Route::post('device/add/{type?}', [App\Http\Controllers\DeviceController::class, 'addDevice']);
        Route::any('device/get/{id}/{forAction}', [App\Http\Controllers\DeviceController::class, 'getDevice']);
        Route::post('device/add-device', [App\Http\Controllers\DeviceController::class, 'ajaxAddDevice']);
        Route::any('device/edit/{id}', [App\Http\Controllers\DeviceController::class, 'editDevice']);
        Route::post('device/get-assign-to', [App\Http\Controllers\DeviceController::class, 'getAssignTo']);
        Route::any('getincrementedAssetID', [App\Http\Controllers\DeviceController::class, 'autoincrement_asset']);
        Route::post('device/tagedit', [App\Http\Controllers\DeviceController::class, 'updateAssetTag']);
        Route::post('jx-asset-summary', [App\Http\Controllers\DeviceController::class, 'ajaxAssetSummary']);
        Route::post('asset-balance-summary', [App\Http\Controllers\DeviceController::class, 'ajaxBalanceSummary']);
        Route::get('asset-export-summary', [App\Http\Controllers\DeviceController::class, 'exportSummary']);
        Route::get('device/getSummaryReportData', [App\Http\Controllers\DeviceController::class, 'showReport'])->name('device.assetSummaryReport');
        Route::get('asset-summary-report', [App\Http\Controllers\DeviceController::class, 'showReport'])->name('asset.summary.report');

        Route::any('device/bulk-delete', [App\Http\Controllers\DeviceController::class, 'deleteBulkDevices']);
        Route::any('device/delete/{id}', [App\Http\Controllers\DeviceController::class, 'deleteDevice']);
        Route::any('device/restore/{id}', [App\Http\Controllers\DeviceController::class, 'restoreDevice']);
        Route::any('device/restore_sold_device/{id}', [App\Http\Controllers\DeviceController::class, 'restoresoldDevice']);
        Route::post('device/checkin', [App\Http\Controllers\DeviceController::class, 'checkin']);
        Route::post('device/checkout', [App\Http\Controllers\DeviceController::class, 'checkout']);
        Route::any('device/accept_checkout/{id}', [App\Http\Controllers\DeviceController::class, 'acceptCheckout']);
        Route::any('device/send_chkout_accept_reminder/{id}', [App\Http\Controllers\DeviceController::class, 'sendChkoutAcceptReminder']);
        Route::any('device/bulk_send_chkout_accept_reminder', [App\Http\Controllers\DeviceController::class, 'sendBulkChkoutAcceptReminder']);
        Route::get('device/get_bulk_chkout_options', [App\Http\Controllers\DeviceController::class, 'getBulkChkoutOptions']);

        Route::post('device/resale', [App\Http\Controllers\DeviceController::class, 'resaleDevice']);
        Route::post('getDeviceItems/{id}', [App\Http\Controllers\DeviceController::class, 'getDeviceItems']);
        Route::get('device/info/{id}', [App\Http\Controllers\DeviceController::class, 'viewInfo'])->name('deviceInfo');
        Route::get('device/info-tab/{id}', [App\Http\Controllers\DeviceController::class, 'viewInfoTab']);
        Route::post('device/patch/list', [App\Http\Controllers\DeviceController::class, 'ajaxPatchManagementListByDevice']);
        Route::get('device/patch/list/export', [App\Http\Controllers\DeviceController::class, 'ajaxPatchManagementListByDeviceExport']);
        Route::post('device/jx-history', [App\Http\Controllers\DeviceController::class, 'ajaxHistory']);
        Route::any('device/jx-requests', [App\Http\Controllers\DeviceController::class, 'ajaxDevReqs']);
        Route::post('device/jx-rdp-history', [App\Http\Controllers\DeviceController::class, 'ajaxRdpHistory']);
        Route::any('device/reject-requests', [App\Http\Controllers\DeviceController::class, 'rejectReq']);
        Route::post('device/jx-assigned-licenses', [App\Http\Controllers\DeviceController::class, 'ajaxAssignedLicenses']);
        Route::post('device/jx-assigned-accessories', [App\Http\Controllers\DeviceController::class, 'ajaxAssignedAccessories']);
        Route::post('device/jx-assigned-consumable', [App\Http\Controllers\DeviceController::class, 'ajaxAssignedConsumable']);
        Route::post('device/jx-assigned-components', [App\Http\Controllers\DeviceController::class, 'ajaxAssignedComponents']);
        Route::post('device/jx-assigned-tickets', [App\Http\Controllers\DeviceController::class, 'ajaxAssignedTickets']);
        Route::post('device/add-rdp-history', [App\Http\Controllers\DeviceController::class, 'ajaxAddRdpConnectHistory']);
        Route::any('devices/bulk/update', [App\Http\Controllers\Bulk\DeviceController::class, 'update']);
        Route::any('devices/bulk/checkout', [App\Http\Controllers\Bulk\DeviceController::class, 'bulkCheckout']);
        Route::any('devices/bulk/checkin', [App\Http\Controllers\Bulk\DeviceController::class, 'bulkCheckin']);
        Route::any('devices/bulk/delete', [App\Http\Controllers\Bulk\DeviceController::class, 'bulkDeviceDelete']);
        Route::any('devices/bulk/checkout-info', [App\Http\Controllers\Bulk\BulkActionController::class, 'ajaxBulkCheckout']);
        Route::any('devices/bulk/checkin-info', [App\Http\Controllers\Bulk\BulkActionController::class, 'ajaxBulkCheckout']);
        Route::any('devices/bulk/delete-info', [App\Http\Controllers\Bulk\BulkActionController::class, 'ajaxBulkCheckout']);
        Route::any('devices/bulk/import-info', [App\Http\Controllers\Bulk\BulkActionController::class, 'ajaxBulkCheckout']);
        Route::any('devices/bulk/dispose', [App\Http\Controllers\Bulk\DeviceController::class, 'bulkDeviceDispose']);
        Route::any('devices/bulk/dispose-info', [App\Http\Controllers\Bulk\BulkActionController::class, 'deviceBulkDispose']);
        Route::post('device/jx-update-rdp-status', [App\Http\Controllers\DeviceController::class, 'ajaxUpdateRdpStatus']);
        Route::get('device/get-warranty-date/{id}', [App\Http\Controllers\DeviceController::class, 'getWarrantyDate']);
        Route::get('devices/change-info/{id}', [App\Http\Controllers\DeviceController::class, 'getChangeInfo']);
        Route::get('getCostByAjax/{type_val}/{id}', [App\Http\Controllers\DeviceController::class, 'getCostByAjax']);
        Route::get('device/accounting/{device_id}', [App\Http\Controllers\DeviceController::class, 'getAccountData']);
        Route::get('device/jx-cost-earning-history/{device_id}', [App\Http\Controllers\DeviceController::class, 'ajaxCostHistory']);
        Route::get('getCustomFieldValue/{id}', [App\Http\Controllers\DeviceController::class, 'getCustomFieldValue']);
        Route::get('getRdpPower/{id}', [App\Http\Controllers\DeviceController::class, 'getRdpPower']);
        Route::get('device/gatepass/{device_id}', [App\Http\Controllers\DeviceController::class, 'ajaxGatepass']);
        Route::any('send-acceptance-mail-to-user', [App\Http\Controllers\DeviceController::class, 'sendAcceptanceMail']);

        // Route::get('patch-dashboard',[App\Http\Controllers\PatchManagement\PatchController::class,'indexPatchDashboard']);
        Route::get('patch-device-count/{id?}', [App\Http\Controllers\PatchManagement\PatchController::class, 'jxPatchDeviceCount']);
        Route::get('get-severity-wise-device-count/{id}', [App\Http\Controllers\PatchManagement\PatchController::class, 'getSeverityWiseDeviceCount']);
        // Route::get('get-type-wise-patch-count/{id}', [App\Http\Controllers\PatchManagement\PatchController::class, 'getTypeOfUpdatedWiseDeviceCount']);
        Route::get('patch-request-count/{id?}', [App\Http\Controllers\PatchManagement\PatchController::class, 'jxPatchRequestCount']);
        Route::get('get-patch-model-wise-count/{id?}', [App\Http\Controllers\PatchManagement\PatchController::class, 'jxPatchModelWiseCount']);
        Route::get('patch-request-list-type/{type}', [App\Http\Controllers\PatchManagement\PatchController::class, 'patchReqListType']);
        Route::post('jx-patch-request-list-type', [App\Http\Controllers\PatchManagement\PatchController::class, 'ajaxPatchReqlistType']);
        Route::get('device-list-type/{type}', [App\Http\Controllers\PatchManagement\PatchController::class, 'deviceListType']);
        Route::post('jx-device-list-type', [App\Http\Controllers\PatchManagement\PatchController::class, 'ajaxDevicelistType']);
        Route::get('patch-list-model-type/{type}', [App\Http\Controllers\PatchManagement\PatchController::class, 'patchModelWiseListType']);
        Route::post('jx-patch-list-model-type', [App\Http\Controllers\PatchManagement\PatchController::class, 'jxPatchModelWiseListTpye']);
        Route::get('patch-list-os-type/{type}', [App\Http\Controllers\PatchManagement\PatchController::class, 'patchOsWiseListType']);
        Route::post('jx-patch-list-os-type', [App\Http\Controllers\PatchManagement\PatchController::class, 'jxPatchOsWiseListTpye']);
        Route::get('get-not-updated-most-device-patch-wise-count/{id?}', [App\Http\Controllers\PatchManagement\PatchController::class, 'jxDeviceNotMostUpdatedPatchWiseCount']);
        Route::get('get-not-updated-critical-device-patch-wise-count/{id?}', [App\Http\Controllers\PatchManagement\PatchController::class, 'jxDeviceNotTopCriticalUpdatedPatchWiseCount']);
        Route::get('patch-get-report-data/{id?}', [App\Http\Controllers\PatchManagement\PatchController::class, 'getPatchReportData']);
        Route::get('patch-get-os-report-data/{id?}', [App\Http\Controllers\PatchManagement\PatchController::class, 'getPatchOsReportData']);
        Route::get('patch-get-severity-data/{id}', [App\Http\Controllers\PatchManagement\PatchController::class, 'getSeverityReportData']);
        // Route::get('severity-wise-patch-device',[App\Http\Controllers\PatchManagement\PatchController::class, 'severityWisePatchDevice']);
        // Route::post('jx-severity',[App\Http\Controllers\PatchManagement\PatchController::class, 'jxSeverityPatchDevice']);
        Route::get('severity-wise-device', [App\Http\Controllers\PatchManagement\PatchController::class, 'severityWiseDevice']);
        Route::post('jx-severity-wise-device', [App\Http\Controllers\PatchManagement\PatchController::class, 'jxSeverityDevice']);
        Route::get('patch-not-updated-device-list/{id}', [App\Http\Controllers\PatchManagement\PatchController::class, 'getPatchNotUpdateDevice']);
        Route::post('jx-patch-not-updated-device-list', [App\Http\Controllers\PatchManagement\PatchController::class, 'jxPatchNotUpdateDevice']);
        Route::post('patch-request-failed-pending', [App\Http\Controllers\PatchManagement\PatchController::class, 'jxPatchRequestFailedPending']);
        Route::get('patch-list/{type?}', [App\Http\Controllers\PatchManagement\PatchController::class, 'indexPatchList']);
        Route::post('jx-patch-list', [App\Http\Controllers\PatchManagement\PatchController::class, 'ajaxPatchList']);
        Route::get('jx-patch-delete/{id}', [App\Http\Controllers\PatchManagement\PatchController::class, 'deletePatchList']);
        Route::post('patch/create', [App\Http\Controllers\PatchManagement\PatchController::class, 'createPatch']);
        Route::get('patch/edit/{id}', [App\Http\Controllers\PatchManagement\PatchController::class, 'editPatch']);
        Route::post('patch/update', [App\Http\Controllers\PatchManagement\PatchController::class, 'updatePatch']);
        Route::get('download-patch-manager-file/{id}', [App\Http\Controllers\PatchManagement\PatchController::class, 'downloadPatchMasterFile']);
        Route::get('patch-device-list', [App\Http\Controllers\PatchManagement\PatchController::class, 'indexPatchDeviceList']);
        Route::post('jx-patch-device-list', [App\Http\Controllers\PatchManagement\PatchController::class, 'ajaxPatchDeviceList']);
        Route::get('patch-request-list/{id}', [App\Http\Controllers\PatchManagement\PatchController::class, 'patchReqList']);
        Route::post('jx-patch-request-list', [App\Http\Controllers\PatchManagement\PatchController::class, 'ajaxPatchReqlist']);
        Route::get('patch-request-delete/{id}', [App\Http\Controllers\PatchManagement\PatchController::class, 'deletePatchRequest']);
        Route::get('patch-request-device-list/{id}', [App\Http\Controllers\PatchManagement\PatchController::class, 'patchReqDeviceList']);
        Route::post('jx-patch-request-device-list-log', [App\Http\Controllers\PatchManagement\PatchController::class, 'ajaxPatchLoglist']);
        Route::get('patch-request-device/getDeviceLogs/{patchReqId}/{device_id}', [App\Http\Controllers\PatchManagement\PatchController::class, 'getDeviceLogs']);
        Route::get('patch-request-device/getReportData', [App\Http\Controllers\PatchManagement\PatchController::class, 'getReportData'])->name('getReportData');
        Route::get('patch-manager', [App\Http\Controllers\PatchManagement\PatchController::class, 'indexPatch']);
        Route::post('patch/type/category/{type}', [App\Http\Controllers\PatchManagement\PatchController::class, 'indexPatchTypeCategory']);
        Route::post('patch/type/category/device/{type}', [App\Http\Controllers\PatchManagement\PatchController::class, 'indexPatchTypeCategoryByDevice']);
        Route::post('request-patch-by-device', [App\Http\Controllers\PatchManagement\PatchController::class, 'bulkPatch']);
        Route::post('request-patch-type-by-device', [App\Http\Controllers\PatchManagement\PatchController::class, 'bulkPatchType']);
        Route::get('patch-manager-request/{id}', [App\Http\Controllers\PatchManagement\PatchController::class, 'indexPatchRequest']);
        Route::post('patch-manager-request-list', [App\Http\Controllers\PatchManagement\PatchController::class, 'ajaxPatchRequestList']);

        // new follow patch management
        // patch manager menu
        Route::get('patch-manager-list', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'indexPatch']);
        Route::post('patch-type-category/{type}', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'indexPatchTypeCategory']);
        Route::post('patch-type-category-device/{type}', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'indexPatchTypeCategoryByDevice']);
        Route::post('device-patch-list', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'ajaxPatchListByDevice']);
        Route::get('device-patch-list-export', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'ajaxPatchListByDeviceExport']);
        Route::post('patch-request-by-device', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'bulkPatchRequest']);
        Route::get('patch-requested-list/{id?}', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'indexPatchRequestDevice']);
        Route::post('ajax-patch-requested-list', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'ajaxPatchRequestDeviceList']);

        // patch list menu
        Route::get('patches/{type?}', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'indexPatchList']);
        Route::post('ajax-patches', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'ajaxPatchList']);
        Route::get('patch-wise-device-list', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'indexPatchWiseDeviceList']);
        Route::post('ajax-wise-patch-device-list', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'ajaxPatchWiseDeviceList']);
        Route::post('device-request-by-patch', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'bulkDeviceRequest']);
        Route::get('patch-request-list', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'patchRequestList']);
        Route::post('ajax-patch-request-list', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'ajaxPatchRequestList']);
        Route::get('inprogress-patch-request-list', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'inprogressPatchRequestList']);
        Route::post('ajax-inprogress-patch-request-list', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'ajaxInprogressPatchRequestList']);
        Route::get('delete-patch-request/{id}', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'deletePatchRequest']);
        Route::get('patch-device-request-list/{id}', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'patchReqDeviceList']);
        Route::post('ajax-patch-device-request-list', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'ajaxPatchLogList']);
        Route::get('patch-device-request-getDeviceLogs/{patchReqId}/{device_id}', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'getDeviceLogs']);
        Route::get('patch-device-request-getReportData', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'getReportData'])->name('getReportData');

        // patch-configuration
        Route::get('patch-configuration', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'patchConfiguration']);
        Route::post('save-patch-configuration', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'ajaxPatchConfiguration']);
        Route::post('approval-status', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'ajaxPatchApprovalStatus']);
        Route::post('ajax-patch-setting-rules', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'ajaxPatchSettingRules']);
        Route::post('create-patch-setting-rule', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'createPatchSettingRules']);
        Route::get('change-status-patch-setting-rule/{id}', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'chanseStatusPatchSettingRules']);
        Route::get('delete-patch-setting-rule/{id}', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'deletePatchSettingRules']);
        Route::get('get-patch-groups', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'getGroupPatchManagementDevice']);
        // patch dashboard
        Route::get('patch-dashboard', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'indexPatchDashboard']);
        Route::get('get-count/{id?}', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'ajaxCount']);
        Route::post('ajax-patch-failed-pending', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'ajaxPatchRequestFailedPending']);
        Route::get('get-severity-wise-patch/{id}', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'getSeverityWisePatch']);
        Route::get('letest-log-error-wise-patch-device/{id?}', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'ajaxLetestLogErrorWisePatchDevice']);
        Route::get('not-update-device-critical-patch-wise-count/{id?}', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'ajaxNotUpdateDeviceCriticalPatchWiseCount']);
        Route::get('activity-wise-list', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'activityWisePatchDevice']);
        Route::post('ajax-activity-wise-list', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'ajaxActivityPatchDevice']);
        Route::get('get-severity-wise-not-update-device/{id}', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'getSeverityWiseDevice']);
        Route::get('not-updated-most-device-wise-patch-count/{id?}', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'ajaxDeviceNotMostUpdatedPatchWiseCount']);
        Route::get('get-type-wise-patch-count/{id}', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'getTypeOfUpdatedWisePatchCount']);
        Route::get('get-patch-model-report-data/{id?}', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'getPatchModelReportData']);
        Route::get('get-patch-category-report-data/{id?}', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'getPatchCategoryReportData']);
        Route::get('get-patch-os-report-data/{id?}', [App\Http\Controllers\PatchManagement\DevicePatchController::class, 'getPatchOsReportData']);

        // Device Expense
        Route::post('jx-expense', [App\Http\Controllers\Device\DeviceMaintenanceController::class, 'ajaxExpenseIndex']);
        Route::post('expense/save', [App\Http\Controllers\Device\DeviceMaintenanceController::class, 'ajaxSaveExpense']);
        // Route::get('export-expense', [App\Http\Controllers\Device\DeviceMaintenanceController::class, 'expenseExport']);
        Route::get('expense/get/{id}/{forAction}', [App\Http\Controllers\Device\DeviceMaintenanceController::class, 'getExpenseDetails']);
        Route::get('expense/delete/{id}', [App\Http\Controllers\Device\DeviceMaintenanceController::class, 'ajaxExpenseDelete']);
        Route::get('expense-export', [App\Http\Controllers\Device\DeviceMaintenanceController::class, 'expensesExport']);

        Route::post('document/jx-upload', [App\Http\Controllers\DocumentController::class, 'ajaxUpload']);
        Route::post('document/delete', [App\Http\Controllers\DocumentController::class, 'ajaxDelete']);
        // Route::get('device/sez/{id}', [App\Http\Controllers\DeviceController::class, 'editSez']);
        Route::get('device/sez-info-tab/{id}', [App\Http\Controllers\DeviceController::class, 'viewSezInfoTab']);
        Route::get('device/device-get-sez/{id}', [App\Http\Controllers\DeviceController::class, 'ajaxGetSez']);
        Route::post('device/device-edit-sez/{id}', [App\Http\Controllers\DeviceController::class, 'ajaxEditSez']);
        Route::post('devices/transfer_device', [App\Http\Controllers\DeviceController::class, 'ajaxAddTransfer']);

        // transfer
        Route::any('transfer', [App\Http\Controllers\TransferController::class, 'getTransferList']);
        Route::any('transfer/list', [App\Http\Controllers\TransferController::class, 'ajaxTransferList']);
        Route::any('transfer/info/{id}', [App\Http\Controllers\TransferController::class, 'getInfo']);
        Route::any('transfer/item/list', [App\Http\Controllers\TransferController::class, 'ajaxTransferInfoList']);
        Route::any('download-excel', [App\Http\Controllers\TransferController::class, 'downloadTransferList']);
        Route::any('download-pdf', [App\Http\Controllers\TransferController::class, 'downloadPDFTransferList']);
        Route::any('transfer-items-download-excel/{id}', [App\Http\Controllers\TransferController::class, 'downloadTransferListItem']);
        Route::any('transfer-items-download-pdf/{id}', [App\Http\Controllers\TransferController::class, 'downloadPDFTransferListItem']);
        Route::get('transfer-get/{id}', [App\Http\Controllers\TransferController::class, 'getTransfer']);
        Route::post('transfer-edit/{id}', [App\Http\Controllers\TransferController::class, 'ajaxEditTransfer']);
        Route::get('transfer-item-get/{id}', [App\Http\Controllers\TransferController::class, 'getTransferItem']);
        Route::post('transfer-item-edit/{id}', [App\Http\Controllers\TransferController::class, 'ajaxEditTransferItem']);
        Route::post('transfer/history/{id}', [App\Http\Controllers\TransferController::class, 'ajaxTransferHistory']);
        Route::get('getTransferStatusAjax', [App\Http\Controllers\TransferController::class, 'getTransferStatusAjax']);

        // Blocked IP
        Route::get('blockIp', [App\Http\Controllers\Device\BlockedIpController::class, 'getIndex']);
        Route::get('getIpByAjax', [App\Http\Controllers\Device\BlockedIpController::class, 'getIpByAjax']);
        Route::post('blockIp/add', [App\Http\Controllers\Device\BlockedIpController::class, 'ajaxAddBlockedIp']);
        Route::get('blockIp/get/{ip}', [App\Http\Controllers\Device\BlockedIpController::class, 'ajaxGetBlockedIp']);
        Route::post('jx-blockIp', [App\Http\Controllers\Device\BlockedIpController::class, 'ajaxIndex']);
        Route::any('blockIp/delete/{id}', [App\Http\Controllers\Device\BlockedIpController::class, 'ajaxDelete']);
        Route::post('blockIp/edit/{id}', [App\Http\Controllers\Device\BlockedIpController::class, 'editBlockIp']);
        Route::get('export-blockIp', [App\Http\Controllers\Device\BlockedIpController::class, 'downloadReport']);

        // Printer Info
        Route::get('printer-info', [App\Http\Controllers\Device\PrinterInfoController::class, 'getIndex']);
        Route::post('jx-printer-info', [App\Http\Controllers\Device\PrinterInfoController::class, 'ajaxIndex']);
        Route::get('printer-info/ajax-delete/{id}', [App\Http\Controllers\Device\PrinterInfoController::class, 'ajaxDeletePrinterInfo']);
        Route::post('printer-info/ajax-add', [App\Http\Controllers\Device\PrinterInfoController::class, 'ajaxAdd']);
        Route::post('printer-info/ajax-edit/{id}', [App\Http\Controllers\Device\PrinterInfoController::class, 'ajaxEdit']);
        Route::any('printer-info/ajax-get/{id}', [App\Http\Controllers\Device\PrinterInfoController::class, 'ajaxGet']);
        Route::get('export-printer-info', [App\Http\Controllers\Device\PrinterInfoController::class, 'printerInfoExport']);

        // commenting for now since functions are not present in controller
        /* Route::post('device-maintenance/cost', [App\Http\Controllers\Device\DeviceMaintenanceController::class, 'maintenanceCost']);
        Route::get('export-device-maintenances', [App\Http\Controllers\Device\DeviceMaintenanceController::class, 'deviceMaintainanceExport']);
        Route::get('device-maintenance', [App\Http\Controllers\Device\DeviceMaintenanceController::class, 'getIndex']);
        Route::post('jx-device-maintenance', [App\Http\Controllers\Device\DeviceMaintenanceController::class, 'ajaxIndex']);
        Route::post('device-maintenance/save', [App\Http\Controllers\Device\DeviceMaintenanceController::class, 'ajaxSave']);
        Route::get('device-maintenance/get/{id}/{forAction}', [App\Http\Controllers\Device\DeviceMaintenanceController::class, 'getDetails']);
        Route::get('device-maintenance/delete/{id}', [App\Http\Controllers\Device\DeviceMaintenanceController::class, 'ajaxDelete']); */

        Route::get('scheduled-maintenance', [App\Http\Controllers\ScheduledMaintenanceController::class, 'getIndex']);
        Route::get('schedules', [App\Http\Controllers\ScheduledMaintenanceController::class, 'getschedules']);
        Route::get('schedules/info', [App\Http\Controllers\ScheduledMaintenanceController::class, 'getscheduleindex']);
        Route::get('schedules/task-info', [App\Http\Controllers\ScheduledMaintenanceController::class, 'getscheduleinfo']);
        Route::get('scheduled-maintenance/info/{id}', [App\Http\Controllers\ScheduledMaintenanceController::class, 'getinfo']);
        Route::get('scheduled-maintenance/info', [App\Http\Controllers\ScheduledMaintenanceController::class, 'getinfo']);
        Route::get('scheduled-maintenance/plan-info/{id}', [App\Http\Controllers\ScheduledMaintenanceController::class, 'planInfo']);
        Route::get('schedules/calendarview', [App\Http\Controllers\ScheduledMaintenanceController::class, 'getcalendarview']);
        Route::post('scheduled-maintenance/create', [App\Http\Controllers\ScheduledMaintenanceController::class, 'ajaxaddScheduleMaintenance']);
        Route::post('scheduled-maintenance/list', [App\Http\Controllers\ScheduledMaintenanceController::class, 'ajaxList']);
        Route::post('scheduled-maintenance/edit/{id}', [App\Http\Controllers\ScheduledMaintenanceController::class, 'ajaxEditScheduledMaintenance']);
        Route::get('scheduled-maintenance/get/{id}', [App\Http\Controllers\ScheduledMaintenanceController::class, 'ajaxGetScheduleMaintenance']);
        Route::get('scheduled-maintenance/delete/{id}', [App\Http\Controllers\ScheduledMaintenanceController::class, 'deleteScheduledMaintenance']);
        Route::get('export-scheduled-maintenance-excel', [App\Http\Controllers\ScheduledMaintenanceController::class, 'downloadScheduledMaintenance']);
        Route::get('export-scheduled-maintenance-pdf', [App\Http\Controllers\ScheduledMaintenanceController::class, 'downloadScheduledMaintenancePDF']);
        //Route::get('getPlanForDropDown', [App\Http\Controllers\ScheduledMaintenanceController::class, 'getPlanForDropDown']);

        /* Schedule Maintenance  */

        /* Work Plan */
        Route::get('schedule-maintenance', [App\Http\Controllers\ScheduledMaintenanceController::class, 'getScheduleMaintenanceIndex']);
        Route::get('work-plan', [App\Http\Controllers\ScheduledMaintenanceController::class, 'getWorkPlanIndex']);
        Route::get('work-plan/list', [App\Http\Controllers\ScheduledMaintenanceController::class, 'ajaxPlanList']);
        Route::any('work-plan/add', [App\Http\Controllers\ScheduledMaintenanceController::class, 'ajaxPlanAdd']);
        Route::get('work-plan/get/{id}', [App\Http\Controllers\ScheduledMaintenanceController::class, 'ajaxPlanget']);
        Route::post('work-plan/update/{id}', [App\Http\Controllers\ScheduledMaintenanceController::class, 'ajaxPlanUpdate']);
        Route::get('work-plan/delete/{id}', [App\Http\Controllers\ScheduledMaintenanceController::class, 'ajaxPlanDelete']);
        Route::get('work-plan/info/{id}', [App\Http\Controllers\ScheduledMaintenanceController::class, 'getWorkPlanInfo']);
        Route::get('task/list', [App\Http\Controllers\ScheduledMaintenanceController::class, 'ajaxTaskList']);
        Route::any('task/add', [App\Http\Controllers\ScheduledMaintenanceController::class, 'ajaxTaskAdd']);
        Route::get('task/get/{id}', [App\Http\Controllers\ScheduledMaintenanceController::class, 'ajaxTaskGet']);
        Route::post('task/update/{id}', [App\Http\Controllers\ScheduledMaintenanceController::class, 'ajaxTaskUpdate']);
        Route::get('task/delete/{id}', [App\Http\Controllers\ScheduledMaintenanceController::class, 'ajaxTaskDelete']);
        Route::get('schedule-maintenance/list', [App\Http\Controllers\ScheduledMaintenanceController::class, 'ajaxScheduleMaintenanceList']);
        Route::any('schedule-maintenance/add', [App\Http\Controllers\ScheduledMaintenanceController::class, 'ajaxScheduleMaintenanceAdd']);
        Route::get('schedule-maintenance/get/{id}', [App\Http\Controllers\ScheduledMaintenanceController::class, 'ajaxScheduleMaintenanceGet']);
        Route::post('schedule-maintenance/update/{id}', [App\Http\Controllers\ScheduledMaintenanceController::class, 'ajaxScheduleMaintenanceUpdate']);
        Route::get('schedule-maintenance/delete/{id}', [App\Http\Controllers\ScheduledMaintenanceController::class, 'ajaxScheduleMaintenanceDelete']);
        Route::get('getWorkPlanForDropDown', [App\Http\Controllers\ScheduledMaintenanceController::class, 'getWorkPlanForDropDown']);
        Route::get('task/{id}', [App\Http\Controllers\ScheduledMaintenanceController::class, 'ajaxTask']);
        Route::get('schedule-maintenance/info/{id}', [App\Http\Controllers\ScheduledMaintenanceController::class, 'getScheduleInfo']);
        Route::get('task/update/list', [App\Http\Controllers\ScheduledMaintenanceController::class, 'ajaxSchDeviceTaskList']);
        Route::get('task_update/get/{id}', [App\Http\Controllers\ScheduledMaintenanceController::class, 'ajaxSchDeviceTaskGet']);
        Route::post('task_update/update/{id}', [App\Http\Controllers\ScheduledMaintenanceController::class, 'ajaxSchDeviceTaskUpdate']);

        /* Task List */
        Route::post('scheduled-task/view-list', [App\Http\Controllers\ScheduledMaintenanceController::class, 'ajaxScheduledTaskList']);
        Route::post('scheduled-task/add', [App\Http\Controllers\ScheduledMaintenanceController::class, 'ajaxaddScheduledTask']);
        Route::post('scheduled-task/edit/{id}', [App\Http\Controllers\ScheduledMaintenanceController::class, 'ajaxEditScheduledTask']);
        Route::get('scheduled-task/get/{id}', [App\Http\Controllers\ScheduledMaintenanceController::class, 'ajaxGetScheduledTask']);
        Route::get('scheduled-task/delete/{id}', [App\Http\Controllers\ScheduledMaintenanceController::class, 'deleteScheduledTask']);
        Route::get('export-scheduled-task-excel/{device_id}', [App\Http\Controllers\ScheduledMaintenanceController::class, 'downloadTaskList']);

        /* Schedules */
        Route::post('schedules/list', [App\Http\Controllers\ScheduledMaintenanceController::class, 'ajaxSchedulesList']);
        Route::get('schedules/info/{id}', [App\Http\Controllers\ScheduledMaintenanceController::class, 'getscheduleindex']);
        Route::get('schedules/plan-info/{id}', [App\Http\Controllers\ScheduledMaintenanceController::class, 'getscheduleinfo']);


        /* settings for admin credentials */
        Route::get('outgoing-email-settings', [App\Http\Controllers\SettingsController::class, 'outGoingIndex']);
        Route::post('outgoing-email-settings/ajax-edit', [App\Http\Controllers\SettingsController::class, 'updateOutGoingEmail']);

        /* Scheduled Info list*/
        Route::post('schedules/info/list', [App\Http\Controllers\ScheduledMaintenanceController::class, 'ajaxTaskListForPlan']);

        /* Scheduled Device */
        Route::post('add-shedule-for-device', [App\Http\Controllers\ScheduledMaintenanceController::class, 'ajaxaddScheduledPlan']);

        /* Device Audit */
        Route::get('device-audits', [App\Http\Controllers\AuditController::class, 'getIndex']);
        Route::get('device-audits/ajaxList', [App\Http\Controllers\AuditController::class, 'ajaxIndex']);
        Route::get('device/device-audits/ajaxList', [App\Http\Controllers\AuditController::class, 'ajaxListForDeviceInfo']);
        Route::post('device-audits/ajaxAdd', [App\Http\Controllers\AuditController::class, 'ajaxAdd']);
        Route::post('device-audits/ajaxEdit/{id}', [App\Http\Controllers\AuditController::class, 'ajaxEdit']);
        Route::get('device-audits/ajaxGet/{id}', [App\Http\Controllers\AuditController::class, 'ajaxGet']);
        Route::get('device-audits/ajaxDelete/{id}', [App\Http\Controllers\AuditController::class, 'ajaxDelete']);
        Route::get('download-audits', [App\Http\Controllers\AuditController::class, 'download']);
        Route::get('audit/get-images/{id}', [App\Http\Controllers\AuditController::class, 'getImages']);
        Route::get('download-zip/{id}', [App\Http\Controllers\AuditController::class, 'downloadZip']);

        Route::get('accessories-restore/{id}', [App\Http\Controllers\AccessoryController::class, 'restoreAccessory']);
        Route::get('export-accessories', [App\Http\Controllers\AccessoryController::class, 'accessoriesExport']);
        Route::get('export-accessories-pdf', [App\Http\Controllers\AccessoryController::class, 'accessoriesExportPDF']);
        Route::get('accessories', [App\Http\Controllers\AccessoryController::class, 'getIndex']);
        Route::post('jx-accessories', [App\Http\Controllers\AccessoryController::class, 'ajaxIndex']);
        Route::post('accessory/add', [App\Http\Controllers\AccessoryController::class, 'ajaxAddAccessory']);
        Route::any('accessory/edit/{id}', [App\Http\Controllers\AccessoryController::class, 'editAccessory']);
        Route::any('accessory/delete/{id}', [App\Http\Controllers\AccessoryController::class, 'deleteAccessory']);
        Route::any('accessory/get/{id}', [App\Http\Controllers\AccessoryController::class, 'getAccessory']);
        Route::post('accessory/checkin', [App\Http\Controllers\AccessoryController::class, 'checkin']);
        Route::post('accessory/checkout', [App\Http\Controllers\AccessoryController::class, 'checkout']);
        Route::any('accessory/accept_checkout/{id}', [App\Http\Controllers\AccessoryController::class, 'acceptCheckout']);
        Route::any('accessory/import', [App\Http\Controllers\AccessoryController::class, 'accessoryImport'])->name('accessory-import');
        Route::get('accessory/info/{id}', [App\Http\Controllers\AccessoryController::class, 'viewInfo']);
        Route::get('accessory/info-tab/{id}', [App\Http\Controllers\AccessoryController::class, 'viewInfoTab']);
        Route::post('accessory/jx-users', [App\Http\Controllers\AccessoryController::class, 'ajaxUsers']);
        Route::post('accessory/jx-history', [App\Http\Controllers\AccessoryController::class, 'ajaxHistory']);
        Route::any('jx-exportaccessory/{id}', [App\Http\Controllers\AccessoryController::class, 'exportAccessory']);
        Route::any('jx-pdfexportaccessory/{id}', [App\Http\Controllers\AccessoryController::class, 'pdfexportAccessory']);
        Route::any('accessories/bulk/checkout', [App\Http\Controllers\Bulk\AccessoryController::class, 'bulkCheckout']);
        Route::any('accessories/bulk/checkout-info', [App\Http\Controllers\Bulk\BulkActionController::class, 'accessoriesImportInfo']);
        Route::any('accessories/bulk/checkin', [App\Http\Controllers\Bulk\AccessoryController::class, 'bulkCheckin']);
        Route::any('accessories/bulk/checkin-info', [App\Http\Controllers\Bulk\BulkActionController::class, 'accessoriesImportInfo']);
        Route::any('accessories/import', [App\Http\Controllers\AccessoryController::class, 'accessoryImport'])->name('accessory-import');
        Route::any('accessories/import_info', [App\Http\Controllers\Bulk\BulkActionController::class, 'accessoriesImportInfo']);
        Route::any('download-custom-fields-code-accessories', [App\Http\Controllers\AccessoryController::class, 'downloadCustomFieldsCodeAccessories']);
        Route::post('jx-requestable-accessory', [App\Http\Controllers\AccessoryController::class, 'ajaxRequestableAccessory']);
        Route::any('request-accessory/{id}', [App\Http\Controllers\AccessoryController::class, 'requestAccessory']);
        Route::post('accessory/scrap', [App\Http\Controllers\AccessoryController::class, 'scrapAccessory']);
        Route::post('accessory/revertScrap', [App\Http\Controllers\AccessoryController::class, 'revertScrap']);
        Route::post('accessory/expected-checkin-date/{id}', [App\Http\Controllers\AccessoryController::class, 'expectedCheckin']);
        Route::get('getAccessoryForDropDown', [App\Http\Controllers\AccessoryController::class, 'getAccessoryForDropDown']);
        Route::get('accessories/change-info/{id}',[App\Http\Controllers\AccessoryController::class,'getAccessoryChangeInfo']);

        //for dropdown 
        Route::get('getManufacturerByQuery', [App\Http\Controllers\Config\ManufactureController::class, 'getManufacturerByQuery']);
        Route::get('getInvoiceByQuery', [App\Http\Controllers\Config\PurchaseController::class, 'getInvoiceByQuery']);
        Route::get('getUserForDropDown', [App\Http\Controllers\UserController::class, 'getUserForDropDown']);
        Route::get('getDeviceForCheckoutDropDown', [App\Http\Controllers\DeviceController::class, 'getDeviceForCheckoutDropDown']);
        // Route::post('purchase-accessory-list',[App\Http\Controllers\AccessoryController::class, 'ajaxPurchaseAccessoryIndex']);
        Route::get('getCustomFieldsByCategory/{id}/{type}/{type_id?}', [App\Http\Controllers\CustomFieldController::class, 'getCustomFieldsByCategory']);
        Route::post('document/jx-upload', [App\Http\Controllers\DocumentController::class, 'ajaxUpload']);
        Route::post('document/delete', [App\Http\Controllers\DocumentController::class, 'ajaxDelete']);
        Route::get('getActivatedUsers', [App\Http\Controllers\UserController::class, 'getActivatedUsers']);



        /* ---------- */
        Route::post('add-purchase-accessory', [App\Http\Controllers\AccessoryController::class, 'addPurchaseAccessory']);
        Route::post('purchase-accessory-list', [App\Http\Controllers\AccessoryController::class, 'ajaxPurchaseAccessoryIndex']);
        Route::post('delete-purchase-accessory', [App\Http\Controllers\AccessoryController::class, 'deletePurchaseAccessory']);
        Route::get('purchase-attachment-accessory-view/{id}', [App\Http\Controllers\AccessoryController::class, 'viewAccessoryPurchaseAttachments']);
        Route::post('update-purchase-accessory/{id}', [App\Http\Controllers\AccessoryController::class, 'updatePurchaseAccessory']);
        Route::get('get-purchase-accessory/{id}', [App\Http\Controllers\AccessoryController::class, 'getPurchaseAccessoryDetail']);

        Route::get('blacklisted', [App\Http\Controllers\BlacklistedSoftwareController::class, 'getIndex']);
        Route::get('getSoftwareByAjax', [App\Http\Controllers\BlacklistedSoftwareController::class, 'getSoftwareByAjax']);
        Route::get('getSoftwareForBlacklisted', [App\Http\Controllers\BlacklistedSoftwareController::class, 'getSoftwareForBlacklisted']);
        Route::post('blacklisted/add', [App\Http\Controllers\BlacklistedSoftwareController::class, 'ajaxAddBlacklisted']);
        Route::post('jx-blacklisted', [App\Http\Controllers\BlacklistedSoftwareController::class, 'ajaxIndex']);
        Route::get('blacklisted/{id}', [App\Http\Controllers\BlacklistedSoftwareController::class, 'getBlacklistedSoftware']);
        Route::post('blacklisted/edit/{id}', [App\Http\Controllers\BlacklistedSoftwareController::class, 'editBlacklistedSoftware']);
        Route::any('blacklisted/jx-delete/{id}', [App\Http\Controllers\BlacklistedSoftwareController::class, 'ajaxDelete']);
        Route::get('getVersionByAjax', [App\Http\Controllers\BlacklistedSoftwareController::class, 'getVersionByAjax']);
        Route::get('getVersionBySoftwares', [App\Http\Controllers\BlacklistedSoftwareController::class, 'getVersionBySoftwares']);
        Route::get('getVersionForBlacklisted', [App\Http\Controllers\BlacklistedSoftwareController::class, 'getVersionForBlacklisted']);
        Route::get('getPublisherByAjax', [App\Http\Controllers\BlacklistedSoftwareController::class, 'getPublisherByAjax']);

        /* Components */
        Route::get('export-components', [App\Http\Controllers\ComponentController::class, 'componentExport']);
        Route::get('export-components-pdf', [App\Http\Controllers\ComponentController::class, 'componentExportPDF']);
        Route::get('components', [App\Http\Controllers\ComponentController::class, 'getIndex']);
        Route::post('jx-components', [App\Http\Controllers\ComponentController::class, 'ajaxIndex']);
        Route::get('component/get/{id}/{forAction}', [App\Http\Controllers\ComponentController::class, 'getComponent']);
        Route::post('component/add', [App\Http\Controllers\ComponentController::class, 'addComponent']);
        Route::post('component/edit/{id}', [App\Http\Controllers\ComponentController::class, 'editComponent']);
        Route::any('component/delete', [App\Http\Controllers\ComponentController::class, 'deleteComponent']);
        Route::post('component/checkout', [App\Http\Controllers\ComponentController::class, 'checkout']);
        Route::post('component/checkin', [App\Http\Controllers\ComponentController::class, 'checkin']);
        Route::any('component/restore/{id}', [App\Http\Controllers\ComponentController::class, 'restoreComponent']);
        Route::get('component/info/{id}', [App\Http\Controllers\ComponentController::class, 'viewInfo']);
        Route::get('component/info-tab/{id}', [App\Http\Controllers\ComponentController::class, 'viewInfoTab']);
        Route::post('component/jx-users', [App\Http\Controllers\ComponentController::class, 'ajaxUsers']);
        Route::post('component/jx-history', [App\Http\Controllers\ComponentController::class, 'ajaxHistory']);
        Route::any('components/bulk/checkout', [App\Http\Controllers\Bulk\ComponentController::class, 'bulkCheckout']);
        Route::any('components/bulk/checkout-info', [App\Http\Controllers\Bulk\BulkActionController::class, 'componentImportInfo']);
        Route::any('components/bulk/checkin', [App\Http\Controllers\Bulk\ComponentController::class, 'bulkCheckin']);
        Route::any('components/bulk/checkin-info', [App\Http\Controllers\Bulk\BulkActionController::class, 'componentImportInfo']);
        Route::any('components/import_info', [App\Http\Controllers\Bulk\BulkActionController::class, 'componentImportInfo']);
        Route::any('components/import', [App\Http\Controllers\ComponentController::class, 'componentImport']);
        Route::any('components/bulk/update', [App\Http\Controllers\Bulk\ComponentController::class, 'bulkUpdate']);
        Route::any('components/bulk_update_info', [App\Http\Controllers\Bulk\ComponentController::class, 'componentUpdateInfo']);
        Route::any('download-custom-fields-code-components', [App\Http\Controllers\ComponentController::class, 'downloadCustomFieldsCodeComponents']);
        Route::post('jx-requestable-component', [App\Http\Controllers\ComponentController::class, 'ajaxRequestableComponent']);
        Route::any('request-component/{id}', [App\Http\Controllers\ComponentController::class, 'requestComponent']);
        Route::get('getComponentForDropDown', [App\Http\Controllers\ComponentController::class, 'getComponentForDropDown']);
        Route::get('component/checkin/{id}', [App\Http\Controllers\ComponentController::class, 'getComponentCheckinData']);

        /* Component BarCode */
        Route::get('qrcode-component/{id}', [App\Http\Controllers\ComponentController::class, 'qrcode']);
        Route::get('print-component-barcode/{option}/{ids}/{user_info?}', [App\Http\Controllers\ComponentController::class, 'printBarcode']);
        Route::get('print-component-one-col/{option}/{ids}/{user_info?}', [App\Http\Controllers\ComponentController::class, 'printBarcodeOneCol']);
        Route::get('print-component-two-col/{option}/{ids}/{user_info?}', [App\Http\Controllers\ComponentController::class, 'printBarcodeTwoCol']);
        Route::get('print-component-vertical-col/{option}/{ids}/{user_info?}', [App\Http\Controllers\ComponentController::class, 'printVerticalCol']);

        /* ---------- */
        Route::post('add-purchase-consumable', [App\Http\Controllers\ConsumableController::class, 'addPurchaseConsumable']);
        Route::post('purchase-consumable-list', [App\Http\Controllers\ConsumableController::class, 'ajaxPurchaseConsumableIndex']);
        Route::post('delete-purchase-consumable', [App\Http\Controllers\ConsumableController::class, 'deletePurchaseConsumable']);
        Route::get('purchase-attachment-consumable-view/{id}', [App\Http\Controllers\ConsumableController::class, 'viewConsumablePurchaseAttachments']);
        Route::post('update-purchase-consumable/{id}', [App\Http\Controllers\ConsumableController::class, 'updatePurchaseConsumable']);
        Route::get('get-purchase-consumable/{id}', [App\Http\Controllers\ConsumableController::class, 'getPurchaseConsumableDetail']);
        Route::post('activity-history-list', [App\Http\Controllers\ConsumableController::class, 'getConsumableActivity']);

        Route::get('consumable-restore/{id}', [App\Http\Controllers\ConsumableController::class, 'restoreConsumable']);
        Route::post('consumable/scrap', [App\Http\Controllers\ConsumableController::class, 'scrapConsumable']);
        Route::post('consumable/revertScrap', [App\Http\Controllers\ConsumableController::class, 'revertScrapConsumable']);
        Route::get('export-consumables', [App\Http\Controllers\ConsumableController::class, 'consumablesExport']);
        Route::get('export-consumables-pdf', [App\Http\Controllers\ConsumableController::class, 'consumablesExportPDF']);
        Route::any('accept-consumable/{id}', [App\Http\Controllers\ConsumableController::class, 'acceptCheckout']);
        Route::any('checkout-consumable/{id}', [App\Http\Controllers\ConsumableController::class, 'checkout']);
        Route::any('consumable/info-summery/{id}', [App\Http\Controllers\ConsumableController::class, 'infosummery']);
        Route::any('consumable-checkout-users/{id}', [App\Http\Controllers\ConsumableController::class, 'consumableCheckoutUsers']);
        Route::any('consumable-history/{id}', [App\Http\Controllers\ConsumableController::class, 'history']);
        Route::any('consumable-info/{id}', [App\Http\Controllers\ConsumableController::class, 'consumableInfo']);
        Route::post('consumable-revoke-assignment', [App\Http\Controllers\ConsumableController::class, 'revokeConsumable']);
        Route::post('update-consumables/{id}', [App\Http\Controllers\ConsumableController::class, 'update']);
        Route::any('delete-consumable/{id}', [App\Http\Controllers\ConsumableController::class, 'deleteConsumable']);
        Route::post('jx-consumables', [App\Http\Controllers\ConsumableController::class, 'ajaxIndex']);
        Route::post('jx-consumable-users/{id}', [App\Http\Controllers\ConsumableController::class, 'consumableUsers']);
        Route::any('jx-exportconsumables/{id}', [App\Http\Controllers\ConsumableController::class, 'exportConsumables']);
        Route::any('jx-exportpdfconsumables/{id}', [App\Http\Controllers\ConsumableController::class, 'exportPDFConsumables']);
        Route::any('consumables/bulk/checkout', [App\Http\Controllers\Bulk\ConsumableController::class, 'bulkCheckout']);
        Route::any('consumables/bulk/checkout-info', [App\Http\Controllers\Bulk\BulkActionController::class, 'consumableImportInfo']);
        Route::any('consumables/bulk/checkin', [App\Http\Controllers\Bulk\ConsumableController::class, 'bulkCheckin']);
        Route::any('consumables/bulk/checkin-info', [App\Http\Controllers\Bulk\BulkActionController::class, 'consumableImportInfo']);
        Route::any('import-consumables', [App\Http\Controllers\ConsumableController::class, 'consumablesImport'])->name('consumablesImport');
        Route::any('consumables/import_info', [App\Http\Controllers\Bulk\BulkActionController::class, 'consumableImportInfo']);
        Route::any('download-custom-fields-code-consumable', [App\Http\Controllers\ConsumableController::class, 'downloadCustomFieldsCodeConsumable']);
        Route::post('jx-requestable-consumable', [App\Http\Controllers\ConsumableController::class, 'ajaxRequestableConsumable']);
        Route::any('request-consumable/{id}', [App\Http\Controllers\ConsumableController::class, 'requestConsumable']);
        Route::get('getConsumableForDropDown', [App\Http\Controllers\ConsumableController::class, 'getConsumableForDropDown']);
        Route::post('consumable-image-delete/{id}', [App\Http\Controllers\ConsumableController::class, 'attachmentDelete']);

        Route::get('getUnitsByAjax', [App\Http\Controllers\ConsumableController::class, 'getUnitsByAjax']);
        Route::any('users-import', [App\Http\Controllers\UserController::class, 'usersImport']);
        Route::any('devices-import', [App\Http\Controllers\DeviceController::class, 'deviceImport']);
        Route::any('download-custom-fields-code', [App\Http\Controllers\CustomFieldController::class, 'downloadCustomFieldsCode']);
        Route::any('download-based-os', [App\Http\Controllers\DeviceController::class, 'downloadbasedOS']);
        Route::any('download-based-ms-office', [App\Http\Controllers\DeviceController::class, 'downloadbasedMSOffice']);

        // Compliance Dashboard
        Route::get('configuration', [App\Http\Controllers\DeviceController::class, 'getConfigurationIndex']);
        Route::post('save-configuration-changes', [App\Http\Controllers\DeviceController::class, 'saveConfigurationChanges']);
        Route::post('update-column-export', [App\Http\Controllers\DeviceController::class, 'updateColumnExports'])->name('updateColumnExports');
        Route::get('compliance_dashboard/{comp_id?}', [App\Http\Controllers\HomeController::class, 'ajaxComplianceDashboard']);
        Route::get('get-os-version', [App\Http\Controllers\HomeController::class, 'jxOsVersion']);
        Route::get('get-os', [App\Http\Controllers\HomeController::class, 'jxOs']);
        Route::get('ni-install-devices/{caption}', [App\Http\Controllers\Device\NetworkInventoryController::class, 'getNetworkInstallDevices']);
        Route::get('ni-not-install-devices/{caption}', [App\Http\Controllers\Device\NetworkInventoryController::class, 'getNetworkUnistallDevices']);
        Route::get('ni-not-installed-static-application/{caption}', [App\Http\Controllers\Device\NetworkInventoryController::class, 'getNotInstalledStaticapplication']);
        Route::post('jx-ni-install-software-devices', [App\Http\Controllers\Device\NetworkInventoryController::class, 'ajaxInstallDeviceBySoftware']);
        Route::post('jx-ni-not-install-software-devices', [App\Http\Controllers\Device\NetworkInventoryController::class, 'ajaxUninstallDeviceBySoftware']);
        Route::post('jx-ni-not-install-static-application', [App\Http\Controllers\Device\NetworkInventoryController::class, 'ajaxUninstallStaticApplication']);
        Route::get('ni-os-devices/{os_name}', [App\Http\Controllers\Device\NetworkInventoryController::class, 'getNiOsDevices']);
        Route::post('jx-ni-os-devices', [App\Http\Controllers\Device\NetworkInventoryController::class, 'ajaxDeviceByOs']);
        Route::get('ni-os-version-devices/{os_name}/{version_name}', [App\Http\Controllers\Device\NetworkInventoryController::class, 'getNiOsVersionDevices']);
        Route::post('jx-ni-version-devices', [App\Http\Controllers\Device\NetworkInventoryController::class, 'ajaxDeviceByVersion']);
        Route::get('ni-problem-catwise-devices/{category_name}', [App\Http\Controllers\Device\NetworkInventoryController::class, 'getNiProbcatWiseDevices']);
        Route::post('jx-ni-software-devices-by-probcategory', [App\Http\Controllers\Device\NetworkInventoryController::class, 'ajaxNicatWiseDevices']);
        Route::get('ni-without-problem-catwise-devices/{category_name}', [App\Http\Controllers\Device\NetworkInventoryController::class, 'getNiWithoutProbcatWiseDevices']);
        Route::post('jx-ni-software-devices-without-probcategory', [App\Http\Controllers\Device\NetworkInventoryController::class, 'ajaxNiWithoutcatWiseDevices']);
        Route::get('ni-architecture-devices/{bit}', [App\Http\Controllers\Device\NetworkInventoryController::class, 'getNiOsArchitectureDevices']);
        Route::post('jx-ni-osArchitecture-devices', [App\Http\Controllers\Device\NetworkInventoryController::class, 'ajaxNiOsArchitecture']);
        Route::get('ni-bitlocker-devices', [App\Http\Controllers\Device\NetworkInventoryController::class, 'getNiBitlockerDevices']);
        Route::post('jx-ni-bitLocker-devices', [App\Http\Controllers\Device\NetworkInventoryController::class, 'ajaxNiBitlockerDevices']);
        Route::get('ni-without-bitlocker-devices', [App\Http\Controllers\Device\NetworkInventoryController::class, 'getNiWithoutBitlockerDevices']);
        Route::post('jx-ni-without-bitLocker-devices', [App\Http\Controllers\Device\NetworkInventoryController::class, 'ajaxNiWithoutBitlockerDevices']);
        Route::get('install-list-download/{caption}', [App\Http\Controllers\Device\NetworkInventoryController::class, 'exportInstallDevices']);
        Route::get('uninstall-list-download/{caption}', [App\Http\Controllers\Device\NetworkInventoryController::class, 'exportUnInstallDevices']);
        Route::get('not-install-static-application-download/{caption}', [App\Http\Controllers\Device\NetworkInventoryController::class, 'exportNotInstalledStaticApplications']);
        Route::get('ni-problem-catwise-devices-download/{cat}', [App\Http\Controllers\Device\NetworkInventoryController::class, 'exportProblemCatwiseDevices']);
        Route::get('ni-without-problem-catwise-devices-download/{cat}', [App\Http\Controllers\Device\NetworkInventoryController::class, 'exportWithoutProblemCatwiseDevices']);
        Route::get('bitlocker-devices-download', [App\Http\Controllers\Device\NetworkInventoryController::class, 'exportBitlockerDevices']);
        Route::get('without-bitlocker-devices-download', [App\Http\Controllers\Device\NetworkInventoryController::class, 'exportWithoutBitlockerDevices']);
        Route::get('osArchitecture-devices-download/{bit}', [App\Http\Controllers\Device\NetworkInventoryController::class, 'exportOsArchitectureDevices']);
        Route::get('os-devices-download/{os_name}', [App\Http\Controllers\Device\NetworkInventoryController::class, 'exportOsDevices']);
        Route::get('os-version-wise-devices-download/{os_name}/{version}', [App\Http\Controllers\Device\NetworkInventoryController::class, 'exportOsVersionDevices']);
        Route::get('dynamicGraph', [App\Http\Controllers\HomeController::class, 'getDynamicGraph']);
        Route::get('operating-system-devices/{os_name}', [App\Http\Controllers\Device\NetworkInventoryController::class, 'getOsSystemDevices']);
        Route::post('jx-ni-os-devices', [App\Http\Controllers\Device\NetworkInventoryController::class, 'ajaxDeviceByOpSystem']);
        Route::get('os-system-wise-devices-download/{os_name}', [App\Http\Controllers\Device\NetworkInventoryController::class, 'exportOsSystemDevices']);
        Route::get('ms-devices-devices/{ms_name}', [App\Http\Controllers\Device\NetworkInventoryController::class, 'getMsDevices']);
        Route::post('jx-ni-ms-devices', [App\Http\Controllers\Device\NetworkInventoryController::class, 'ajaxDeviceByMsOffices']);
        Route::get('ms-office-wise-devices-download/{ms_name}', [App\Http\Controllers\Device\NetworkInventoryController::class, 'exportMsDevices']);

        /* Tracked Devices (Network Devices) */
        Route::group(['prefix' => 'devices'], function () {
            Route::group(['prefix' => 'live-monitor'], function () {
                Route::post('reminder/notification', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'reminderLiveMonitorDevice']);
                Route::post('add', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'addLiveMonitorDevice']);
                Route::get('list', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'getLiveMonitorList']);
                Route::post('jx-list', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'ajaxLiveMonitorList']);
                Route::get('delete/{id}/{type?}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'LiveMonitorDelete']);
                Route::post('reminder/notification', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'reminderLiveMonitorDevice']);
                Route::post('user-info-list', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'ajaxUserList'])->name('ajaxUserList');
                Route::post('user-remove-list', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'ajaxUserRemoveList'])->name('ajaxUserRemoveList');
                Route::any('info/{id}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'getLiveMonitorDeviceInfo']);
                Route::any('ajax-info/{id}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'getLiveMonitorDeviceAjaxInfo']);
                Route::post('add-alert', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'addLiveMonitorDeviceAlert']);
                Route::post('alert-jx-list/{website_id}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'ajaxLiveMonitorDeviceAlertList']);
                Route::get('get-alert/{id}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'ajaxLiveMonitorDeviceAlertGet']);
                Route::post('edit-alert/{id}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'ajaxLiveMonitorDeviceAlertEdit']);
                Route::get('delete-alert/{id}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'ajaxLiveMonitorDeviceAlertDelete']);
            });
            Route::group(['prefix' => 'network_inventory'], function () {
                Route::get('list', [App\Http\Controllers\Device\NI\NetworkInventoryController::class, 'getList']);
                Route::post('jx-list', [App\Http\Controllers\Device\NI\NetworkInventoryController::class, 'ajaxList']);
                Route::get('export-inventory-details', [App\Http\Controllers\Device\NI\NetworkInventoryController::class, 'exportInventoryDetails']);

                Route::post('jx-programs-list/{id}', [App\Http\Controllers\Device\NetworkInventoryController::class, 'ajaxPrograms']);
                Route::post('jx-changes-list/{id}', [App\Http\Controllers\Device\NetworkInventoryController::class, 'ajaxChangeList']);
                Route::get('change-list-download/{id}', [App\Http\Controllers\Device\NetworkInventoryController::class, 'downloadNIChangeDetails']);

                Route::post('env-list/{id}', [App\Http\Controllers\Device\NetworkInventoryController::class, 'getEnv']);
                Route::get('env-list-download/{id}', [App\Http\Controllers\Device\NetworkInventoryController::class, 'downloadEnv']);

                Route::post('outlook-account/{id}', [App\Http\Controllers\Device\NetworkInventoryController::class, 'getOutlookAccount']);
                Route::get('outlook-accounts-download/{id}', [App\Http\Controllers\Device\NetworkInventoryController::class, 'downloadOutlookAccounts']);
                Route::post('usb-ports/{id}', [App\Http\Controllers\Device\NetworkInventoryController::class, 'getUsbPorts']);

                Route::get('info/{id}', [App\Http\Controllers\Device\NetworkInventoryController::class, 'getInfo']);
                Route::get('change-info/{id}', [App\Http\Controllers\Device\NetworkInventoryController::class, 'getChangesInfo']);
                Route::get('change-more-info/{id}', [App\Http\Controllers\Device\NetworkInventoryController::class, 'getChangeMoreInfo']);
                Route::get('export/{id}', [App\Http\Controllers\Device\NetworkInventoryController::class, 'downloadDeviceReport']);

                Route::get('detectExistingSystem', [App\Http\Controllers\Device\NetworkInventoryController::class, 'detectExistingSystem'])->name('detectExistingSystem');
                Route::post('mapHostBySerial', [App\Http\Controllers\Device\NetworkInventoryController::class, 'mapHostBySerial'])->name('mapHostBySerial');

                Route::get('change_list', [App\Http\Controllers\Device\NetworkInventoryController::class, 'getChanges']);
                Route::post('jx-changes', [App\Http\Controllers\Device\NetworkInventoryController::class, 'ajaxChanges']);
                Route::get('download-change-list', [App\Http\Controllers\Device\NetworkInventoryController::class, 'downloadChangeList']);

                Route::get('other_list', [App\Http\Controllers\Device\NetworkInventoryController::class, 'getothers']);
                // Route::post('jx-changes', [App\Http\Controllers\Device\NetworkInventoryController::class, 'ajaxChanges']);

                Route::get('license_list', [App\Http\Controllers\Device\NetworkInventoryController::class, 'getLicense']);
                Route::post('jx-license', [App\Http\Controllers\Device\NetworkInventoryController::class, 'ajaxLicense']);
                Route::get('jx-license-export-excel', [App\Http\Controllers\Device\NetworkInventoryController::class, 'exportajaxLicense']);
                Route::get('jx-license-export-pdf', [App\Http\Controllers\Device\NetworkInventoryController::class, 'ajaxLicensePDF']);
                Route::get('jx-export-pdf-format', [App\Http\Controllers\Device\NI\NetworkInventoryController::class, 'downloadPdfFormat']);
                Route::get('getVersionByAjax', [App\Http\Controllers\Device\NetworkInventoryController::class, 'getLicenseVersionByAjax']);
                Route::get('software_usage_list/{software_id}/{serial}', [App\Http\Controllers\Device\NetworkInventoryController::class, 'getSoftwareUsage']);
                Route::post('jxSoftwareUsage', [App\Http\Controllers\Device\NetworkInventoryController::class, 'ajaxSoftwareUsageList']);

                Route::get('os_list', [App\Http\Controllers\Device\NetworkInventoryController::class, 'getOs']);
                Route::post('jx-os-details', [App\Http\Controllers\Device\NetworkInventoryController::class, 'ajaxOsDetails']);
                Route::get('jx-os-export-excel', [App\Http\Controllers\Device\NetworkInventoryController::class, 'exportOs']);

                Route::get('download-os-detail', [App\Http\Controllers\Device\NetworkInventoryController::class, 'downloadosdetail']);
                Route::get('download-microsoft-office-detail', [App\Http\Controllers\Device\NetworkInventoryController::class, 'downloadMicrosoftOfficedetail']);
            });
        });

        /* Tracked Website */
        Route::group(['prefix' => 'websites'], function () {
            Route::group(['prefix' => 'live-monitor'], function () {
                Route::post('add', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'addLiveMonitorWebsites']);
                Route::get('list/{type?}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'getLiveMonitorWebsiteList']);
                Route::post('jx-list/{type?}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'ajaxLiveMonitorWebsiteList']);
                Route::get('get/{id}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'ajaxLiveMonitorWebsiteGet']);
                Route::post('edit/{id}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'ajaxLiveMonitorWebsiteEdit']);
                Route::get('delete/{id}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'ajaxLiveMonitorWebsiteDelete']);
                Route::get('enable/{id}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'ajaxLiveMonitorWebsiteEnable']);
                Route::get('alert-enable/{id}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'ajaxLiveMonitorWebsiteAlertEnable']);
                Route::any('export', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'ajaxLiveMonitorWebsiteExport']);
                Route::any('info/{id}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'getLiveMonitorWebsiteInfo']);
                Route::any('ajax-info/{id}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'getLiveMonitorWebsiteAjaxInfo']);
                Route::post('add-alert', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'addLiveMonitorWebsiteAlert']);
                Route::post('alert-jx-list/{website_id}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'ajaxLiveMonitorWebsiteAlertList']);
                Route::get('alert-website-export/{website_id}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'exportLiveMonitorWebsiteAlertList']);
                Route::get('get-alert/{id}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'ajaxLiveMonitorWebsiteAlertGet']);
                Route::post('edit-alert/{id}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'ajaxLiveMonitorWebsiteAlertEdit']);
                Route::get('delete-alert/{id}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'ajaxLiveMonitorWebsiteAlertDelete']);
                Route::post('incident-jx-list/{website_id}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'ajaxLiveMonitorWebsiteIncidentList']);
                Route::get('incident-website-export/{website_id}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'exportLiveMonitorWebsiteIncidentList']);
                Route::get('get-incident/{id}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'ajaxLiveMonitorWebsiteIncidentGet']);
                Route::post('edit-incident/{id}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'ajaxLiveMonitorWebsiteIncidentEdit']);
                Route::post('edit-incident-mark/{id}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'ajaxLiveMonitorWebsiteIncidentMark']);
                Route::post('get-overview-opened-incidents/{id}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'ajaxLiveMonitorWebsiteOverviewIncidentOpen']);
                Route::post('get-overview-histories/{id}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'ajaxLiveMonitorWebsiteOverviewHistory']);
                Route::get('overview-histories-export/{id}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'ajaxLiveMonitorWebsiteOverviewHistoryExport']);
                Route::get('get-graph-data/{id}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'ajaxLiveMonitorWebsiteOverviewGraph']);
                Route::get('get-up-down-graph-data/{id}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'ajaxLiveMonitorWebsiteOverviewGraphUpDown']);
                Route::get('member-list/{id}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'memberList']);
                Route::post('ajax-member-list', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'ajaxMemberList']);
                Route::get('ajax-member-delete/{id}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'ajaxMemberDelete']);
                Route::post('ajax-member-add', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'ajaxMemberAdd']);
                Route::get('ajax-member-edit/{id}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'ajaxMemberEdit']);
                Route::post('ajax-member-update/{id}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'ajaxMemberUpdate']);
                Route::post('histories-jx-list/{website_id}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'ajaxLiveMonitorWebsiteHistoriesList']);
                Route::get('incident-list/{type?}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'liveMonitorWebsiteIncidentList']);
                Route::post('dashboard-incident-jx-list/{type?}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'ajaxLiveMonitorWebsiteDashboardIncidentList']);
                Route::get('dashboard-incident-jx-list-export/{type?}', [App\Http\Controllers\LiveMonitor\LiveMonitorController::class, 'exportLiveMonitorWebsiteDashboardIncidentList']);
            });
        });

        /* Network Inventory */
        Route::group(['prefix' => 'network_inventory'], function () {

            /* Network Inventory to Device List Automatic Creation */
            Route::any('ajax-update-ni-settings', [App\Http\Controllers\NetworkInventory\ConfigController::class, 'updateNiSettings']);

            /* scan ranges */
            Route::get('scan-ranges', [App\Http\Controllers\NetworkInventory\ConfigController::class, 'scanRangesList']);
            Route::any('ajax-scan-ranges', [App\Http\Controllers\NetworkInventory\ConfigController::class, 'ajaxScanRangesList']);
            Route::any('scan-ranges/ajax-add', [App\Http\Controllers\NetworkInventory\ConfigController::class, 'ajaxAddScanRange']);
            Route::any('scan-ranges/ajax-get/{id}', [App\Http\Controllers\NetworkInventory\ConfigController::class, 'ajaxGetScanRange']);
            Route::any('scan-ranges/ajax-edit/{id}', [App\Http\Controllers\NetworkInventory\ConfigController::class, 'ajaxEditScanRange']);
            Route::any('scan-ranges/ajax-remove/{id}', [App\Http\Controllers\NetworkInventory\ConfigController::class, 'ajaxDeleteScanRange']);
            /* static mapped ips */
            Route::get('mapped-ips', [App\Http\Controllers\Device\NetworkInventoryController::class, 'MapList']);
            Route::any('ajax-scan-ranges', [App\Http\Controllers\NetworkInventory\ConfigController::class, 'ajaxScanRangesList']);
            Route::any('scan-ranges/ajax-add', [App\Http\Controllers\NetworkInventory\ConfigController::class, 'ajaxAddScanRange']);
            Route::any('scan-ranges/ajax-get/{id}', [App\Http\Controllers\NetworkInventory\ConfigController::class, 'ajaxGetScanRange']);
            Route::any('scan-ranges/ajax-edit/{id}', [App\Http\Controllers\NetworkInventory\ConfigController::class, 'ajaxEditScanRange']);
            Route::any('scan-ranges/ajax-remove/{id}', [App\Http\Controllers\NetworkInventory\ConfigController::class, 'ajaxDeleteScanRange']);
            /* host valut */
            Route::get('host-vault', [App\Http\Controllers\NetworkInventory\ConfigController::class, 'hostVaultList']);
            Route::any('ajax-host-vault', [App\Http\Controllers\NetworkInventory\ConfigController::class, 'ajaxHostVaultList']);
            Route::any('host-vault/ajax-add', [App\Http\Controllers\NetworkInventory\ConfigController::class, 'ajaxAddHost']);
            Route::any('host-vault/ajax-get/{id}', [App\Http\Controllers\NetworkInventory\ConfigController::class, 'ajaxGetHost']);
            Route::any('host-vault/ajax-edit/{id}', [App\Http\Controllers\NetworkInventory\ConfigController::class, 'ajaxEditHost']);
            Route::any('host-vault/ajax-remove/{id}', [App\Http\Controllers\NetworkInventory\ConfigController::class, 'ajaxDeleteHost']);

            /* common credentials */
            Route::group(['prefix' => 'common-credentials'], function () {
                Route::get('/', [App\Http\Controllers\NetworkInventory\ConfigController::class, 'commonCredentials']);
                Route::any('ajax-list', [App\Http\Controllers\NetworkInventory\ConfigController::class, 'ajaxCommonCredentials']);
                Route::any('ajax-get/{id}', [App\Http\Controllers\NetworkInventory\ConfigController::class, 'ajaxGetCommonCredentials']);
                Route::any('ajax-edit/{id}', [App\Http\Controllers\NetworkInventory\ConfigController::class, 'ajaxEditCommonCredentials']);
                Route::any('ajax-remove/{id}', [App\Http\Controllers\NetworkInventory\ConfigController::class, 'ajaxDeleteCommonCredentials']);
            });

            /* snmp credentials */
            Route::group(['prefix' => 'snmp-credentials'], function () {
                Route::get('/', [App\Http\Controllers\NetworkInventory\SnmpController::class, 'credentialsList']);
                Route::any('ajax-list', [App\Http\Controllers\NetworkInventory\SnmpController::class, 'ajaxCredentialsList']);
                Route::any('ajax-add', [App\Http\Controllers\NetworkInventory\SnmpController::class, 'ajaxAddCredentials']);
                Route::any('ajax-get/{id}', [App\Http\Controllers\NetworkInventory\SnmpController::class, 'ajaxGetCredentials']);
                Route::any('ajax-edit/{id}', [App\Http\Controllers\NetworkInventory\SnmpController::class, 'ajaxEditCredentials']);
                Route::any('ajax-remove/{id}', [App\Http\Controllers\NetworkInventory\SnmpController::class, 'ajaxDeleteCredentials']);
            });

            /* detected hosts */
            Route::get('detected-hosts', [App\Http\Controllers\NetworkInventory\HostController::class, 'detectedHosts']);
            Route::any('ajax-detected-hosts', [App\Http\Controllers\NetworkInventory\HostController::class, 'ajaxDetectedHosts']);
            Route::any('ajax-update-credentials/{id}', [App\Http\Controllers\NetworkInventory\HostController::class, 'ajaxUpdateCredentials']);

            /* mapped location */
            Route::get('mapped-location', [App\Http\Controllers\NetworkInventory\MappedLocationController::class, 'getindex']);
            Route::get('mapped-location/ajax-list', [App\Http\Controllers\NetworkInventory\MappedLocationController::class, 'ajaxList']);
            Route::any('mapped-location/ajax-add', [App\Http\Controllers\NetworkInventory\MappedLocationController::class, 'ajaxAdd']);
            Route::any('mapped-location/ajax-get/{id}', [App\Http\Controllers\NetworkInventory\MappedLocationController::class, 'ajaxGet']);
            Route::any('mapped-location/ajax-edit/{id}', [App\Http\Controllers\NetworkInventory\MappedLocationController::class, 'ajaxEdit']);
            Route::any('mapped-location/ajax-remove/{id}', [App\Http\Controllers\NetworkInventory\MappedLocationController::class, 'ajaxDelete']);
            Route::any('mapped-location/mapped-location-import', [App\Http\Controllers\NetworkInventory\MappedLocationController::class, 'mappedLocationImport']);

            /* Blocked website Url */
            Route::get('blocked-websites', [App\Http\Controllers\NetworkInventory\BlockedController::class, 'getindex']);
            Route::get('blocked-websites/ajax-list', [App\Http\Controllers\NetworkInventory\BlockedController::class, 'ajaxList']);
            Route::any('blocked-website/ajax-add', [App\Http\Controllers\NetworkInventory\BlockedController::class, 'ajaxAdd']);
            Route::any('blocked-website/ajax-get/{id}', [App\Http\Controllers\NetworkInventory\BlockedController::class, 'ajaxGet']);
            Route::any('blocked-website/ajax-edit/{id}', [App\Http\Controllers\NetworkInventory\BlockedController::class, 'ajaxEdit']);
            Route::any('blocked-website/ajax-delete/{id}', [App\Http\Controllers\NetworkInventory\BlockedController::class, 'ajaxDelete']);

            /* usb port blocked */
            Route::get('getUsbPortByQuery', [App\Http\Controllers\NetworkInventory\BlockedController::class, 'getUsbPortByQuery']);
            Route::get('blocked-usb-ports', [App\Http\Controllers\NetworkInventory\BlockedController::class, 'getUsbPortIndex']);
            Route::get('blocked-usb-device-details/ajax-list', [App\Http\Controllers\NetworkInventory\BlockedController::class, 'ajaxUsbDeviceDetailList']);
            Route::any('blocked-usb-device-details/ajax-edit/{id}', [App\Http\Controllers\NetworkInventory\BlockedController::class, 'ajaxUsbDeviceDetailEdit']);
            Route::any('blocked-usb-device-details/ajax-delete/{id}', [App\Http\Controllers\NetworkInventory\BlockedController::class, 'ajaxUsbDeviceDetailDelete']);
            Route::get('blocked-usb-ports/info/{id}', [App\Http\Controllers\NetworkInventory\BlockedController::class, 'getUsbPortInfoIndex']);
            Route::get('blocked-usb-port/ajax-list/{id}', [App\Http\Controllers\NetworkInventory\BlockedController::class, 'ajaxUsbPortList']);
            Route::any('blocked-usb-port/ajax-add', [App\Http\Controllers\NetworkInventory\BlockedController::class, 'ajaxUsbPortAdd']);
            Route::any('blocked-usb-port/ajax-get/{id}', [App\Http\Controllers\NetworkInventory\BlockedController::class, 'ajaxUsbPortGet']);
            Route::any('blocked-usb-port/ajax-edit/{id}', [App\Http\Controllers\NetworkInventory\BlockedController::class, 'ajaxUsbPortEdit']);
            Route::any('blocked-usb-port/ajax-delete/{id}', [App\Http\Controllers\NetworkInventory\BlockedController::class, 'ajaxUsbPortDelete']);
        });

        /* Azure AD */
        Route::get('azure-ad-devices', [App\Http\Controllers\AzureDevicesController::class, 'index'])->name('azure-devices');
        Route::post('getAzureDevices', [App\Http\Controllers\AzureDevicesController::class, 'getAzureDevices'])->name('getAzureDevices');
        Route::get('azureInfo/{id?}', [App\Http\Controllers\AzureDevicesController::class, 'azureInfo'])->name('azureInfo');
        Route::any('azure-ad-devices/export', [App\Http\Controllers\AzureDevicesController::class, 'exportDevices'])->name('exportAzureDevices');

        // RDP Asset
        Route::get('getRDPDevices', [App\Http\Controllers\Device\RDPController::class, 'getRDPDevice']);
        Route::post('ajaxGetRDPDevices', [App\Http\Controllers\Device\RDPController::class, 'ajaxGetRDPDevices'])->name('ajaxGetRDPDevices');
        // });

        Route::get('my-items', [App\Http\Controllers\UserController::class, 'myItems'])->name('my-items');
        // listing calls for general access
        Route::post('user/jx-assigned-devices', [App\Http\Controllers\UserController::class, 'ajaxAssignedDevices']);
        Route::post('user/jx-assigned-pending-devices', [App\Http\Controllers\UserController::class, 'ajaxAssignedPendingDevices']);
        Route::post('user/jx-assigned-licenses', [App\Http\Controllers\UserController::class, 'ajaxAssignedLicenses']);
        Route::post('user/jx-assigned-accessories', [App\Http\Controllers\UserController::class, 'ajaxAssignedAccessories']);
        Route::post('user/jx-assigned-components', [App\Http\Controllers\UserController::class, 'ajaxAssignedComponents']);
        Route::post('user/jx-assigned-consumables', [App\Http\Controllers\UserController::class, 'ajaxAssignedConsumables']);
        Route::post('document/jx-documents', [App\Http\Controllers\DocumentController::class, 'ajaxDocuments']);
        Route::get('document-attachment/view/{id}', [App\Http\Controllers\DocumentController::class, 'viewDocuments']);
        Route::post('user/history', [App\Http\Controllers\UserController::class, 'ajaxHistory']);
        Route::post('user/tnc-acceptance', [App\Http\Controllers\HomeController::class, 'tncAcceptance'])->name('tncAcceptance');
        Route::get('download/documents/{id}', [App\Http\Controllers\DocumentController::class, 'ajaxDocumentDownload']);
        Route::post('user/jx-srApproval-pending', [App\Http\Controllers\UserController::class, 'ajaxSRApprovalPendding']);
        // ---------------------

        // Route::get('profile', [App\Http\Controllers\ProfileController::class, 'getIndex']);
        // Route::any('profile/edit', [App\Http\Controllers\ProfileController::class, 'editProfile']);
        // Route::any('profile/change-password', [App\Http\Controllers\ProfileController::class, 'changePassword']);
        // Route::post('user-qrcode', [App\Http\Controllers\UserController::class, 'userQrcode'])->name('user.qrcode');
        Route::get('profile', [App\Http\Controllers\ProfileController::class, 'getIndex'])->name('profile.index');
        Route::any('profile/edit', [App\Http\Controllers\ProfileController::class, 'editProfile']);
        Route::any('profile/change-password', [App\Http\Controllers\ProfileController::class, 'changePassword']);
        Route::post('user-qrcode', [App\Http\Controllers\UserController::class, 'userQrcode'])->name('user.qrcode');
        Route::post('profile/avatar-upload', [App\Http\Controllers\ProfileController::class, 'uploadAvatar'])->name('profile.avatar.upload');
        Route::post('/profile/remove-avatar', [App\Http\Controllers\ProfileController::class, 'removeAvatar'])->name('profile.remove.avatar');

        //Download link
        Route::get('download/ITEscalation', [App\Http\Controllers\DownloadController::class, 'downloadITEscalation']);
        Route::get('download/HREscalation', [App\Http\Controllers\DownloadController::class, 'downloadHREscalation']);

        Route::get('qrcode/{id}', [App\Http\Controllers\DeviceController::class, 'qrcode']);
        Route::get('print-device-barcode/{option}/{ids}/{user_info?}', [App\Http\Controllers\DeviceController::class, 'printBarcode']);
        Route::get('print-one-col/{option}/{ids}/{user_info?}', [App\Http\Controllers\DeviceController::class, 'printBarcodeOneCol']);
        Route::get('print-two-col/{option}/{ids}/{user_info?}', [App\Http\Controllers\DeviceController::class, 'printBarcodeTwoCol']);
        Route::get('print-vertical-col/{option}/{ids}/{user_info?}', [App\Http\Controllers\DeviceController::class, 'printVerticalCol']);

        Route::get('dashboard', [App\Http\Controllers\HomeController::class, 'newdashboard'])->name('dashboard');
        Route::get('/dashboard/daily-ticket-overview', [App\Http\Controllers\HomeController::class, 'getDailyTicketOverview']);  
        Route::get('dashboard/ticket-status-trend',[App\Http\Controllers\HomeController::class, 'getTicketStatusTrend']);   
        Route::get('dashboard/trend-analytics', [App\Http\Controllers\HomeController::class, 'getTrendAnalytics']); 
        Route::get('dashboard/ticket-source-trend', [App\Http\Controllers\HomeController::class, 'ticketSourceTrend']);
        Route::get('dashboard/feedback-dashboard', [App\Http\Controllers\HomeController::class, 'feedbackDashboard']);
        Route::get('dashboard/issue-type', [App\Http\Controllers\HomeController::class, 'issueTypeChart']);
        Route::get('dashboard/slaPriority', [App\Http\Controllers\HomeController::class, 'getSlaPriority']);
        Route::get('dashboard/topIssue', [App\Http\Controllers\HomeController::class, 'getTopIssues']);
        Route::get('dashboard/getTechnicianLeaderboard', [App\Http\Controllers\HomeController::class, 'getTechnicianLeaderboard']);
        Route::get('dashboard/getTicketByPlaces', [App\Http\Controllers\HomeController::class, 'getTicketByPlaces']);

        Route::get('ajax_dashboard/{comp_id?}', [App\Http\Controllers\HomeController::class, 'ajaxDashboard']);
        Route::get('ajax_new_dashboard/{comp_id?}', [App\Http\Controllers\HomeController::class, 'ajaxNewDashboard']);
        Route::get('live-monitor-dashboard/{comp_id?}', [App\Http\Controllers\HomeController::class, 'ajaxLiveMonitorDashboard']);
        Route::get('live-monitor/get-graph-data', [App\Http\Controllers\HomeController::class, 'ajaxLiveMonitorWebsiteLoadTimeGraph']);
        Route::get('live-monitor/website/down', [App\Http\Controllers\HomeController::class, 'ajaxLiveMonitorWebsiteDown']);
        Route::get('live-monitor/website/uptime', [App\Http\Controllers\HomeController::class, 'ajaxLiveMonitorWebsiteMaxUptime']);
        Route::get('ajax_location_dashboard/{comp_id?}', [App\Http\Controllers\HomeController::class, 'ajaxLocationDashboard']);
        Route::get('ajaxTicketDashboard', [App\Http\Controllers\HomeController::class, 'ajaxTicketDashboard'])->name('ajaxTicketDashboard');
        Route::get('device/getReportData', [App\Http\Controllers\HomeController::class, 'getReportData']);
        Route::get('logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
        // Route::get('live-monitor-dashboard/{comp_id?}', [App\Http\Controllers\HomeController::class, 'ajaxLiveMonitorDashboard']);
        // Route::get('live-monitor/get-graph-data', [App\Http\Controllers\HomeController::class, 'ajaxLiveMonitorWebsiteLoadTimeGraph']);
        // Route::get('live-monitor/website/down', [App\Http\Controllers\HomeController::class, 'ajaxLiveMonitorWebsiteDown']);
        // Route::get('live-monitor/website/uptime', [App\Http\Controllers\HomeController::class, 'ajaxLiveMonitorWebsiteMaxUptime']);
        Route::get('ajax_nonit_dashboard', [App\Http\Controllers\HomeController::class, 'ajaxNonItDashboard']);

        Route::get('newdashboard', [App\Http\Controllers\HomeController::class, 'newdashboard']);
        Route::get('ajax-new-dashboard-users', [App\Http\Controllers\Dashboard\UserController::class, 'getTabData']);
        Route::get('getUsers', [App\Http\Controllers\UserController::class, 'getUsers']);

        // Route::get('service-ticket', [App\Http\Controllers\ServiceTicketController::class, 'getIndex']);
        Route::any('transfer-service-ticket', [App\Http\Controllers\ServiceTicketController::class, 'transferTicket']);
        Route::get('service-ticket-star/{id}', [App\Http\Controllers\ServiceTicketController::class, 'starServiceTicket']);
        Route::any('service-ticket-bulk-star', [App\Http\Controllers\ServiceTicketController::class, 'bulkStar']);
        Route::any('service-ticket-bulk-delete', [App\Http\Controllers\ServiceTicketController::class, 'bulkDelete']);
        Route::any('service-ticket-bulk-resolve', [App\Http\Controllers\ServiceTicketController::class, 'bulkResolve']);
        // Route::any('service-ticket/add', [App\Http\Controllers\ServiceTicketController::class, 'addServiceTicket']);
        Route::any('service-ticket-addfollowing/{id}', [App\Http\Controllers\ServiceTicketController::class, 'addTicketFollowings']);
        Route::any('service-ticket/detail/{id}', [App\Http\Controllers\ServiceTicketController::class, 'serviceTicketDetail']);
        Route::any('tickets/attachments/download/{id}', [App\Http\Controllers\ServiceTicketController::class, 'attachmentDownload']);
        Route::any('service-ticket-property-update/{id}', [App\Http\Controllers\ServiceTicketController::class, 'updateProperty']);
        Route::any('service-ticket-note-update/{id}', [App\Http\Controllers\ServiceTicketController::class, 'updateNote']);
        Route::any('service-ticket-update-tag/{id}', [App\Http\Controllers\ServiceTicketController::class, 'updateTags']);
        // Route::get('service-ticket/{id}', [App\Http\Controllers\LabelController::class, 'checkDeployable']);
        Route::Resource('service-tickets', 'ServiceTicketController');
        Route::any('my-service-tickets', [App\Http\Controllers\ServiceTicketController::class, 'myServiceTicket']);
        Route::any('resolve-service-tickets', [App\Http\Controllers\ServiceTicketController::class, 'ResolveServiceTicket']);
        Route::any('tkt-response-attachment/{id}', [App\Http\Controllers\ServiceTicketController::class, 'uploadAttachFile']);
        Route::any('tkt-attachment', [App\Http\Controllers\ServiceTicketController::class, 'uploadAttachFile']);
        Route::any('tkt-response/{id}', [App\Http\Controllers\ServiceTicketController::class, 'responsetkt']);

        /*Route::get('service-ticket-statuses', [App\Http\Controllers\ServiceTicketStatusController::class, 'getIndex']);
        Route::get('ajax-ticket-statuses', [App\Http\Controllers\ServiceTicketStatusController::class, 'getServiceStatusList']);
        Route::any('service-ticket-statuses/add', [App\Http\Controllers\ServiceTicketStatusController::class, 'addServiceTicketStatus']);
        Route::any('service-ticket-statuses/edit/{id}', [App\Http\Controllers\ServiceTicketStatusController::class, 'editServiceTicketStatus']);
        Route::get('service-ticket-statuses/{id}', [App\Http\Controllers\ServiceTicketStatusController::class, 'getServiceTicketDetail']);*/

        /*Route::get('service-ticket-priorites', [App\Http\Controllers\ServiceTicketPriorityController::class, 'getIndex']);
        Route::get('ajax-service-ticket-priorites', [App\Http\Controllers\ServiceTicketPriorityController::class, 'ajaxPriorityList']);
        Route::any('service-ticket-priorites/add', [App\Http\Controllers\ServiceTicketPriorityController::class, 'addServiceTicketPriority']);
        Route::any('service-ticket-priorites/edit/{id}', [App\Http\Controllers\ServiceTicketPriorityController::class, 'editServiceTicketPriority']);
        Route::get('service-ticket-priorites/{id}', [App\Http\Controllers\ServiceTicketPriorityController::class, 'getServiceTicketPriorityDetail']);*/
        Route::any('service-ticket-priorites/delete/{id}', [App\Http\Controllers\ServiceTicketPriorityController::class, 'deleteServiceTicketPriority']);

        // Route::get('ticket-service-types', [App\Http\Controllers\TicketServiceTypeController::class, 'getIndex']);
        // Route::any('ticket-service-type/add', [App\Http\Controllers\TicketServiceTypeController::class, 'addTicketServiceType']);
        // Route::any('ticket-service-type/edit/{id}', [App\Http\Controllers\TicketServiceTypeController::class, 'editTicketServiceType']);
        // Route::get('ticket-service-type/{id}', [App\Http\Controllers\TicketServiceTypeController::class, 'getServiceTicketCategoryDetail']);

        // Route::get('ticket-problem-types', [App\Http\Controllers\TicketProblemTypeController::class, 'getIndex']);
        // Route::get('ticket-problem-type/serv-id/{id}', [App\Http\Controllers\TicketProblemTypeController::class, 'getPrbforService']);
        // Route::any('ticket-problem-type/add', [App\Http\Controllers\TicketProblemTypeController::class, 'addTicketProblemType']);
        // Route::any('ticket-problem-type/edit/{id}', [App\Http\Controllers\TicketProblemTypeController::class, 'editTicketProblemType']);
        // Route::get('ticket-problem-type/{id}', [App\Http\Controllers\TicketProblemTypeController::class, 'getServiceTicketCategoryDetail']);
        // Route::post('tkt-problem-types/put/{id}', [App\Http\Controllers\TicketProblemTypeController::class, 'update']);

        Route::get('company-departments', [App\Http\Controllers\Config\DepartmentController::class, 'getCompanyDeptmts']);
        Route::get('company-users', [App\Http\Controllers\TicketProblemTypeController::class, 'getCompanyUserList']);
        // Route::Resource('tkt-service-types', 'TicketServiceTypeController');
        Route::Resource('tkt-problem-types', 'TicketProblemTypeController');
        Route::get('problem-attender-user/{id}', [App\Http\Controllers\TicketProblemTypeController::class, 'getProblemAttenderId']);
        Route::get('departments/by-company/{id}', [App\Http\Controllers\Config\DepartmentController::class, 'getDepartmentByCompany']); // for later use
        Route::get('departments/by-service-request', [App\Http\Controllers\Config\DepartmentController::class, 'getDepartmentOfServiceRequest']);

        Route::get('ticket/getStatusByTicketType/{ticketType?}', [App\Http\Controllers\Ticket\IndexController::class, 'getStatusByTicketType'])->name('getStatusByTicketType');
        Route::get('ticket/getTicketTypeFieldsets/{ticketType?}/{status?}', [App\Http\Controllers\Ticket\IndexController::class, 'getTicketTypeFieldsets'])->name('getTicketTypeFieldsets');
        Route::get('ticket/getDepartmentCustomFields/{departmentId}', [App\Http\Controllers\Config\DepartmentController::class, 'getDepartmentCustomFields']);
        Route::get('ticket/getCustomFieldsForFilter', [App\Http\Controllers\CustomFieldController::class, 'getCustomFieldsForFilter']);
        Route::get('ticket/getCustomFieldsValueForFilter/{id}', [App\Http\Controllers\CustomFieldController::class, 'getCustomFieldsValueForFilter']);
        Route::get('ticket/custom-field/options/{id}', [App\Http\Controllers\Ticket\IndexController::class, 'getCustomOptions']);

        // CC EMAIL User Groups
        Route::get('tickets/user-groups', [App\Http\Controllers\Ticket\CCEmailUserGroupController::class, 'index']);
        Route::post('tickets/jx-user-groups-list', [App\Http\Controllers\Ticket\CCEmailUserGroupController::class, 'ajaxUserList']);
        Route::post('tickets/user-groups-add', [App\Http\Controllers\Ticket\CCEmailUserGroupController::class, 'createGroup']);
        Route::get('tickets/user-groups-edit/{id}', [App\Http\Controllers\Ticket\CCEmailUserGroupController::class, 'editGroup']);
        Route::post('tickets/user-groups-update/{id}', [App\Http\Controllers\Ticket\CCEmailUserGroupController::class, 'updateGroup']);
        Route::get('tickets/user-groups-delete/{id}', [App\Http\Controllers\Ticket\CCEmailUserGroupController::class, 'deleteGroup']);
        Route::get('tickets/user-groups-member/{id}', [App\Http\Controllers\Ticket\CCEmailUserGroupController::class, 'groupMember']);
        Route::post('tickets/user-groups-member-list/{id}', [App\Http\Controllers\Ticket\CCEmailUserGroupController::class, 'getGroupMemberList']);
        Route::post('tickets/user-groups/members/{id}', [App\Http\Controllers\Ticket\CCEmailUserGroupController::class, 'addGroupMember']);
        Route::get('tickets/user-groups-member-edit/{id}', [App\Http\Controllers\Ticket\CCEmailUserGroupController::class, 'fetchGroupMember']);
        Route::post('tickets/user-groups-member-update/{id}', [App\Http\Controllers\Ticket\CCEmailUserGroupController::class, 'updateGroupMember']);
        Route::get('tickets/get-cc-user-groups', [App\Http\Controllers\Ticket\CCEmailUserGroupController::class, 'fetchGroupDepart']);
        Route::get('tickets/user-groups-member-delete/{id}', [App\Http\Controllers\Ticket\CCEmailUserGroupController::class, 'deleteMember']);
        Route::get('tickets/fetch-cc-users-email/{pc}', [App\Http\Controllers\Ticket\CCEmailUserGroupController::class, 'fetchCCUsersEmail']);
        Route::get('tickets/fetch-cc-users-email-id/{department}/{problemCategory}/{subCategory?}', [App\Http\Controllers\Ticket\CCEmailUserGroupController::class, 'fetchCCUsersEmailID']);

        /* Service Ticket Module */
        Route::group(['middleware' => 'auth.service_ticket'], function () {
            Route::get('tickets/dashboard', [App\Http\Controllers\Ticket\IndexController::class, 'dashboard'])->name('ticket.dashboard');
            Route::get('get-ai-generated-tickets', [App\Http\Controllers\Ticket\IndexController::class, 'getAiGeneratedTickets'])->name('getAiGeneratedTicketsData');
            Route::get('tickets/config/ajaxindex', [App\Http\Controllers\Ticket\ConfigController::class, 'ajaxindex']);
            Route::get('tickets/config', [App\Http\Controllers\Ticket\ConfigController::class, 'index']);
            Route::post('tickets/update-config', [App\Http\Controllers\Ticket\ConfigController::class, 'updateConfig']);
            Route::post('tickets/email-based-ticketing', [App\Http\Controllers\Ticket\ConfigController::class, 'updateEmailRes']);
            Route::post('tickets/update-report-fields', [App\Http\Controllers\Ticket\ConfigController::class, 'updateReportFields']);
            Route::post('tickets/config/ajax-departments', [App\Http\Controllers\Ticket\ConfigController::class, 'ajaxDepartments']);
            Route::any('tickets/config/ajax-user-privileges', [App\Http\Controllers\Ticket\ConfigController::class, 'ajaxUserPrivileges']);
            Route::any('tickets/config/update-privilege', [App\Http\Controllers\Ticket\ConfigController::class, 'updatePrivilege']);
            Route::get('tickets/config/update-department/{id}/{val}/{company_id}', [App\Http\Controllers\Ticket\ConfigController::class, 'updateDepartment']);
            Route::get('tickets/config/export-config', [App\Http\Controllers\Ticket\ConfigController::class, 'exportConfig']);
            Route::post('tickets/update-config-notification', [App\Http\Controllers\Ticket\ConfigController::class, 'updateConfigNotification']);
            Route::post('tickets/ticket-auto-update', [App\Http\Controllers\Ticket\ConfigController::class, 'ticketAutoUpdate']);
            Route::post('tickets/duplicate-ticket-update', [App\Http\Controllers\Ticket\ConfigController::class, 'duplicateTicketUpdate']);
            Route::post('tickets/config/update-departments-bulk', [App\Http\Controllers\Ticket\ConfigController::class, 'updateDepartmentsBulk']);
            Route::get("tickets/ticket-details", function(){
                return view('dashboard.dashboard_copy');
            });
            Route::get('tickets/requestList/{main_filter?}', [App\Http\Controllers\Ticket\RequestController::class, 'index']);
            Route::post('tickets/requestAjaxList', [App\Http\Controllers\Ticket\RequestController::class, 'requestAjaxList']);
            Route::get("tickets/kanban", function(){
                return view('dashboard.kanban');
            });

            // Ticket Trigger
            Route::get('tickets/trigger', [App\Http\Controllers\Ticket\TicketTriggerController::class, 'getTicketTrigger']);
            Route::post('tickets/trigger/save', [App\Http\Controllers\Ticket\TicketTriggerController::class, 'saveTicketTrigger']);
            Route::post('tickets/getTicketTriggers', [App\Http\Controllers\Ticket\TicketTriggerController::class, 'ajaxTicketTrigger']);
            Route::post('tickets/exportTicketTriggers', [App\Http\Controllers\Ticket\TicketTriggerController::class, 'exportTicketTrigger']);
            Route::post('tickets/trigger/getOperators', [App\Http\Controllers\Ticket\TicketTriggerController::class, 'getOperators'])->name('getOperators');
            Route::get('tickets/trigger/edit', [App\Http\Controllers\Ticket\TicketTriggerController::class, 'editTicketTrigger'])->name('editTicketTrigger');
            Route::post('tickets/trigger/update', [App\Http\Controllers\Ticket\TicketTriggerController::class, 'updateTicketTrigger'])->name('updateTicketTrigger');
            Route::post('tickets/trigger/remove', [App\Http\Controllers\Ticket\TicketTriggerController::class, 'removeTicketTrigger'])->name('removeTicketTrigger');
            Route::post('tickets/trigger/getHistory/{id}',  [App\Http\Controllers\Ticket\TicketTriggerController::class, 'ajaxHistory']);

            // Ticket Schedule
            Route::get('tickets/initSchedule', [App\Http\Controllers\Ticket\SchedularController::class, 'initSchedule']);
            Route::post('tickets/createSchedular', [App\Http\Controllers\Ticket\SchedularController::class, 'createSchedular']);
            Route::post('tickets/attachment/schedular_attachment', [App\Http\Controllers\Ticket\SchedularController::class, 'schedularAttachment']);
            Route::post('tickets/attachment/schedular_attachment_remove', [App\Http\Controllers\Ticket\SchedularController::class, 'schedularAttachmentRemove']);
            Route::get('tickets/schedularList', [App\Http\Controllers\Ticket\SchedularController::class, 'schedularList']);
            Route::post('jx-schedular-list', [App\Http\Controllers\Ticket\SchedularController::class, 'ajaxschedularList']);
            Route::get('tickets/editschedular/edit/{id}', [App\Http\Controllers\Ticket\SchedularController::class, 'editSchedular']);
            Route::get('tickets/deleteschedular/delete/{id}', [App\Http\Controllers\Ticket\SchedularController::class, 'deleteSchedular']);
            Route::get('export-schedular', [App\Http\Controllers\Ticket\SchedularController::class, 'exportSchedular']);

            //ITM Connect
            Route::get('tickets/itmlist', [App\Http\Controllers\Ticket\ITMConnect\IndexController::class, 'index']);
            Route::get('ticket/exportItmLog', [App\Http\Controllers\Ticket\ITMConnect\IndexController::class, 'exportItmLog']);
            Route::post('tickets/ajax-itmconnect-lists', [App\Http\Controllers\Ticket\ITMConnect\IndexController::class, 'ajaxItmConnectList']);
            Route::post('tickets/convert-ticket/{id}', [App\Http\Controllers\Ticket\ITMConnect\IndexController::class, 'convertTicket']);
            Route::get('tickets/twillio', [App\Http\Controllers\Ticket\ITMConnect\IndexController::class, 'twillioCall']);

            // Ticket Incident
            Route::get('tickets/initIncident', [App\Http\Controllers\Ticket\IncidentController::class, 'initIncident']);
            Route::post('tickets/createIncident', [App\Http\Controllers\Ticket\IncidentController::class, 'createIncident']);
            Route::post('tickets/attachment/incident_attachment', [App\Http\Controllers\Ticket\IncidentController::class, 'incidentAttachment']);
            Route::post('tickets/attachment/incident_attachment_remove', [App\Http\Controllers\Ticket\IncidentController::class, 'incidentAttachmentRemove']);
            Route::get('tickets/incidentList', [App\Http\Controllers\Ticket\IncidentController::class, 'incidentList']);
            Route::post('jx-incident-list', [App\Http\Controllers\Ticket\IncidentController::class, 'ajaxIncidentList']);
            Route::get('tickets/editincident/edit/{id}', [App\Http\Controllers\Ticket\IncidentController::class, 'editIncident']);
            Route::get('tickets/deleteincident/delete/{id}', [App\Http\Controllers\Ticket\IncidentController::class, 'deleteIncident']);
            Route::get('tickets/viewincident/view/{id}', [App\Http\Controllers\Ticket\IncidentController::class, 'viewIncident']);
            Route::get('export-incident', [App\Http\Controllers\Ticket\IncidentController::class, 'exportIncident']);
            Route::get('tickets/exportTicketIncidentPDF', [App\Http\Controllers\Ticket\IncidentController::class, 'exportTicketIncidentPDF']);
            Route::get('tickets/exportTicketIncidentModalPDF/{id?}', [App\Http\Controllers\Ticket\IncidentController::class, 'exportTicketIncidentModalPDF']);
            Route::get('tickets/exportTicketIncidentModalPDF', [App\Http\Controllers\Ticket\IncidentController::class, 'exportTicketIncidentModalPDF']);
            Route::any('tickets/getTicketDetails', [App\Http\Controllers\Ticket\IncidentController::class, 'ajaxTicketDetail']);
            Route::any('tickets/incident/getTicketDetails', [App\Http\Controllers\Ticket\IncidentController::class, 'getTicketDetail']);
            Route::get('jx-get-incident-select2', [App\Http\Controllers\Ticket\IncidentController::class, 'getIncidentListSelect2'])->name('getIncidentListSelect2');
            Route::post('tickets/incident/add_impacted_ticket', [App\Http\Controllers\Ticket\IncidentController::class, 'submitImapactTicket']);
            Route::get('tickets/incident/remove_impacted_ticket', [App\Http\Controllers\Ticket\IncidentController::class, 'removeImapactTicket']);
            Route::get('tickets/attachment/download/{id}', [App\Http\Controllers\Ticket\IncidentController::class, 'download']);

            Route::get('tickets/new_design', [App\Http\Controllers\Ticket\TicketController::class, 'newdesign']);
            Route::get('getEnabledAccountsForOptions', [App\Http\Controllers\Ticket\TicketController::class,'getEnabledAccountsForOptions']);

            Route::get('tickets/requestList', [App\Http\Controllers\Ticket\RequestController::class, 'requestList'])->name('tickets.requestList');
            Route::post('tickets/requestAjaxList', [App\Http\Controllers\Ticket\RequestController::class, 'requestAjaxList'])->name('tickets.requestAjaxList');
            Route::get('tickets/requestInfo/{id}', [App\Http\Controllers\Ticket\RequestController::class, 'requestInfo'])->name('tickets.requestInfo');
            Route::get('tickets/export-request', [App\Http\Controllers\Ticket\RequestController::class, 'exportServiceRequest']);
            Route::post('tickets/updateRequestInfo/{id}', [App\Http\Controllers\Ticket\RequestController::class, 'updateRequestInfo'])->name('tickets.updateRequestInfo');
            Route::get('tickets/requestsHistory/{id}', [App\Http\Controllers\Ticket\RequestController::class, 'requestsHistory'])->name('tickets.requestsHistory');
            Route::post('tickets/requestApprove/{pr_id}', [App\Http\Controllers\Ticket\RequestController::class, '_requestApprove'])->name('tickets.requestApprove');
            Route::post('tickets/revokeDecision/{pr_id}', [App\Http\Controllers\Ticket\RequestController::class, 'revokeDecision'])->name('tickets.revokeDecision');
            Route::get('tickets/getUserOfTicketHandler', [App\Http\Controllers\Ticket\PabController::class, 'getUserOfTicketHandler'])->name('tickets.getUserOfTicketHandler');
            Route::get('tickets/myRequestList', [App\Http\Controllers\Ticket\RequestController::class, 'myRequestList'])->name('tickets.myRequestList');
            Route::get('tickets/myRequestList/{main_filter?}', [App\Http\Controllers\Ticket\RequestController::class, 'myRequestList'])->name('tickets.myRequestList');
            Route::post('tickets/request/timeline', [App\Http\Controllers\Ticket\RequestController::class, 'getRefreshedTimeLine'])->name('getRefreshedTimeLine');
            Route::post('tickets/request/timeline_archived', [App\Http\Controllers\Ticket\RequestController::class, 'getRefreshedTimeLineArchive'])->name('getRefreshedTimeLineArchive');
            Route::post('getDayForEndDate', [App\Http\Controllers\Ticket\RequestController::class, 'getDayForEndDate'])->name('getDayForEndDate');
            Route::post('tickets/bulk-decision', [App\Http\Controllers\Ticket\RequestController::class, 'bulkDecision']);
            Route::get('tickets/archived-requests', [App\Http\Controllers\Ticket\ArchivedRequestController::class, 'index']);
            Route::get('tickets/archived-requests/ajax-list', [App\Http\Controllers\Ticket\ArchivedRequestController::class, 'archivedRequestList']);
            Route::get('tickets/archived-requests/details', [App\Http\Controllers\Ticket\ArchivedRequestController::class, 'archivedRequestDetails']);
            Route::get('tickets/archived-requests/history', [App\Http\Controllers\Ticket\ArchivedRequestController::class, 'requestsHistory']);
            Route::get('tickets/archived-requests/export', [App\Http\Controllers\Ticket\ArchivedRequestController::class, 'exportArchivedRequest']);
            Route::get('requested_archived_form/view/{id}', [App\Http\Controllers\Ticket\ArchivedController::class, 'viewArchivedForm']);
            Route::get('requested_archived_custom_form/view/{id}', [App\Http\Controllers\Ticket\ArchivedController::class, 'viewArchivedCustomForm']);
            //archived
            Route::get('tickets/archive-list', [App\Http\Controllers\Ticket\ArchivedController::class, 'archivedRemote']);
            //Pab
            Route::get('tickets/srat/list', [App\Http\Controllers\Ticket\PabController::class, 'list']);
            Route::post('tickets/srat/ajax-list', [App\Http\Controllers\Ticket\PabController::class, 'ajaxList']);
            Route::any('tickets/srat/add', [App\Http\Controllers\Ticket\PabController::class, 'add']);
            Route::any('tickets/srat/edit/{id}', [App\Http\Controllers\Ticket\PabController::class, 'edit']);
            Route::any('tickets/srat/delete/{id}', [App\Http\Controllers\Ticket\PabController::class, 'delete']);
            Route::get('tickets/srat/srat_history/{id}', [App\Http\Controllers\Ticket\PabController::class, 'historyView']);
            Route::post('tickets/srat/srat-history-ajax', [App\Http\Controllers\Ticket\PabController::class, 'historylistAjax']);
            Route::get('tickets/srat/srat_member_history/{id}', [App\Http\Controllers\Ticket\PabController::class, 'historyMemberView']);
            Route::any('tickets/srat/members/{id}/{company_id}', [App\Http\Controllers\Ticket\PabController::class, 'members']);
            Route::any('tickets/srat/members/add/{id}', [App\Http\Controllers\Ticket\PabController::class, 'addMember']);
            Route::any('tickets/srat/members/remove/{id}/{company_id}/{member_id}', [App\Http\Controllers\Ticket\PabController::class, 'removeMember']);
            Route::any('tickets/srat/members/edit/{id}/{company_id}/{member_id}', [App\Http\Controllers\Ticket\PabController::class, 'editMember']);
            Route::post('tickets/srat/members/updates', [App\Http\Controllers\Ticket\PabController::class, 'updateMember']);
            Route::post('tickets/srat/srat-member-history-ajax', [App\Http\Controllers\Ticket\PabController::class, 'historylistMemberAjax']);
            Route::post('tickets/srat/members/actionUpdate', [App\Http\Controllers\Ticket\PabController::class, 'actionUpdate'])->name('actionUpdate');
            Route::get('tickets/srat/export', [App\Http\Controllers\Ticket\PabController::class, 'export']);
            
            // Technician clock log
            Route::any('technician/log-activity/{logActivityFor?}', [App\Http\Controllers\Ticket\TechnicianActivityLogController::class, 'logTechnicianActivity'])->name('logUserActivity');
            Route::any('technician/get-tech-curren-status', [App\Http\Controllers\Ticket\TechnicianActivityLogController::class, 'getTechCurrentStatus'])->name('getTechCurrentStatus');
            Route::any('technician/get-tech-curren-status-id/{id}', [App\Http\Controllers\Ticket\TechnicianActivityLogController::class, 'getTechCurrentStatusByTechId'])->name('getTechCurrentStatusByTechId');
            Route::any('technician/get-tech-get-log-activities', [App\Http\Controllers\Ticket\TechnicianActivityLogController::class, 'getLogActivities'])->name('getLogActivities');
            Route::any('technician/post-user-activity', [App\Http\Controllers\Ticket\TechnicianActivityLogController::class, 'postUserActivity'])->name('postUserActivity');
            Route::any('technician/live-status', [App\Http\Controllers\Ticket\TechnicianActivityLogController::class, 'liveStatus'])->name('liveStatus');
            Route::post('technician/ajax-live-status', [App\Http\Controllers\Ticket\TechnicianActivityLogController::class, 'ajaxLiveStatus'])->name('ajaxLiveStatus');
            Route::post('technician/bulkUserActivate', [App\Http\Controllers\Ticket\TechnicianActivityLogController::class, 'bulkUserActivate']);
            Route::post('technician/bulkUserDeactivate', [App\Http\Controllers\Ticket\TechnicianActivityLogController::class, 'bulkUserLogOut']);
            // Technician clock log end

            // Ticket Status Dynamic Tab
            Route::post('tabs', [App\Http\Controllers\Ticket\TicketController::class, 'addCustomTabs']);
            Route::post('tabList', [App\Http\Controllers\Ticket\TicketController::class, 'tabList']);
            Route::get('editTab/{id}', [App\Http\Controllers\Ticket\TicketController::class, 'editTab']);
            Route::post('updateTab', [App\Http\Controllers\Ticket\TicketController::class, 'updateTab']);
            Route::post('tabDelete', [App\Http\Controllers\Ticket\TicketController::class, 'tabDelete']);
            Route::post('tabName', [App\Http\Controllers\Ticket\TicketController::class, 'customStatusTab']);
            Route::get('tickets/get-status-tabs', [App\Http\Controllers\Ticket\TicketController::class, 'customStatusTab']);

            Route::get('ticket/get_users_to_assign_by_dep_by_availability', [App\Http\Controllers\Ticket\TechnicianActivityLogController::class, 'getUserToAssignByDepartmentByAvailability']);

            //File Tracker module
            Route::get('files_tracker', [App\Http\Controllers\FileTracker\FilesTrackerController::class, 'FilesTracker']);
            Route::Post('files_tracker/list', [App\Http\Controllers\FileTracker\FilesTrackerController::class, 'FilesTrackerList']);
            Route::Post('files_tracker/add', [App\Http\Controllers\FileTracker\FilesTrackerController::class, 'createFilesTracker']);
            Route::get('files_tracker/delete/{id}', [App\Http\Controllers\FileTracker\FilesTrackerController::class, 'deleteFileTracker']);
            Route::get('files_tracker/edit/{id}', [App\Http\Controllers\FileTracker\FilesTrackerController::class, 'editFileTracker']);
            Route::post('files_tracker/update/{id}', [App\Http\Controllers\FileTracker\FilesTrackerController::class, 'updateFileTracker']);
            Route::get('files_tracker/export', [App\Http\Controllers\FileTracker\FilesTrackerController::class, 'exportFileTracker']);
            Route::get('files_tracker_history/{id}', [App\Http\Controllers\FileTracker\FilesTrackerController::class, 'fileTrackerHistoryView']);
            Route::post('file_tracker_list_history', [App\Http\Controllers\FileTracker\FilesTrackerController::class, 'fileTrackerListHistory']);
            Route::any('files_tracker/view/history/{id}', [App\Http\Controllers\FileTracker\FilesTrackerController::class, 'fileTrackerhistory']);

            // Ticket Status
            Route::group(['middleware' => 'auth.role:SuperAdmin'], function () {
                Route::get('ticket-status', [App\Http\Controllers\Ticket\StatusController::class, 'statusList']);
                Route::post('jx-status-list', [App\Http\Controllers\Ticket\StatusController::class, 'ajaxstatusList']);
                Route::get('tickets/status/deletestatus/delete/{id}', [App\Http\Controllers\Ticket\StatusController::class, 'deleteStatus']);
                Route::post('tickets/status/createStatus', [App\Http\Controllers\Ticket\StatusController::class, 'createStatus']);
                Route::get('tickets/status/edit/{id}', [App\Http\Controllers\Ticket\StatusController::class, 'editStatus']);
                Route::post('tickets/status/update', [App\Http\Controllers\Ticket\StatusController::class, 'updateStatus']);
                Route::get('tickets/subcategories', [App\Http\Controllers\Ticket\StatusController::class, 'getSubcategories']);
            });
            Route::get('get-status-form', [App\Http\Controllers\Ticket\StatusController::class, 'getStatusForm']);
            Route::get('get-status-update', [App\Http\Controllers\Ticket\StatusController::class, 'getStatusByAjaxForUpdateStatus']);

            Route::get('getStatusByAjax',[App\Http\Controllers\Ticket\StatusController::class,'getStatusByAjax']);
            Route::get('get-status-approval-list',[App\Http\Controllers\Ticket\StausApprovalController::class,'getStatusApprovalList']);
            Route::get('status-approval', [App\Http\Controllers\Ticket\StausApprovalController::class,'index']);
            Route::post('status-approval-list', [App\Http\Controllers\Ticket\StausApprovalController::class,'ajaxApprovalList']);
            Route::post('create-status-approval', [App\Http\Controllers\Ticket\StausApprovalController::class,'ajaxCreateStatusApproval']);
            Route::get('delete-status-approval/{id}', [App\Http\Controllers\Ticket\StausApprovalController::class,'ajaxDeleteStatusApproval']);
            Route::get('edit-status-approval/{id}', [App\Http\Controllers\Ticket\StausApprovalController::class,'ajaxEditStatusApproval']);
            Route::post('update-status-approval/{id}', [App\Http\Controllers\Ticket\StausApprovalController::class,'ajaxUpdateStatusApproval']);
            Route::get('status-approval/export', [App\Http\Controllers\Ticket\StausApprovalController::class, 'exportApprovalList'])->name('status.approval.export');
            Route::get('get-status-approval',[App\Http\Controllers\Ticket\StausApprovalController::class, 'getStatusApproval'])->name('get.status.approval');
            Route::get('ticket-status-approval/view/{tkt_id}/{user_id}',[App\Http\Controllers\Ticket\StausApprovalController::class, 'ticketStatusApprovalView'])->name('ticket.status.approval.view');
            Route::get('status-change-approvals/{main_filter}',[App\Http\Controllers\Ticket\StausApprovalController::class, 'StatusChangeApprovals'])->name('status.change.approvals');
            Route::post('ajax-status-change-approvals/{main_filter}',[App\Http\Controllers\Ticket\StausApprovalController::class, 'ajaxStatusChangeApprovals'])->name('ajax.status.change.approvals');
            Route::get('status-change-approval-info/{apr_id}/{main_filter}',[App\Http\Controllers\Ticket\StausApprovalController::class, 'statusChangeApprovalInfo'])->name('status.change.approval.info');
            Route::post('statusChangeApproval',[App\Http\Controllers\Ticket\StausApprovalController::class, 'statusChangeApproval'])->name('status.change.approval');
            Route::get('status-approval-data/{id}',[App\Http\Controllers\Ticket\StausApprovalController::class, 'getStatusApprovalData'])->name('status.approval.data');
            Route::get('ticket-status-approval/approve/{tkt_id}/{user_id}',[App\Http\Controllers\Ticket\StausApprovalController::class, 'ticketStatusApprovalApprove'])->name('ticket.status.approval.approve');
            Route::get('ticket-status-approval/reject/{tkt_id}/{user_id}',[App\Http\Controllers\Ticket\StausApprovalController::class, 'ticketStatusApprovalReject'])->name('ticket.status.approval.reject');


            Route::group(['prefix' => 'tickets/pab'], function () {
                Route::get('list', [App\Http\Controllers\Ticket\PabController::class, 'list']);
                Route::post('ajax-list', [App\Http\Controllers\Ticket\PabController::class, 'ajaxList']);
                Route::any('add', [App\Http\Controllers\Ticket\PabController::class, 'add']);
                Route::any('edit/{id}', [App\Http\Controllers\Ticket\PabController::class, 'edit']);
                Route::any('delete/{id}', [App\Http\Controllers\Ticket\PabController::class, 'delete']);
                Route::any('members/{id}', [App\Http\Controllers\Ticket\PabController::class, 'members']);
                Route::any('members/add/{id}', [App\Http\Controllers\Ticket\PabController::class, 'addMember']);
                Route::any('members/remove/{id}/{member_id}', [App\Http\Controllers\Ticket\PabController::class, 'removeMember']);
                Route::post('actionUpdate', [App\Http\Controllers\Ticket\PabController::class, 'actionUpdate'])->name('actionUpdate');
                Route::post('getDepartment', [App\Http\Controllers\Ticket\PabController::class, 'getDepartment'])->name('getDepartment');
                Route::any('members/edit/{id}', [App\Http\Controllers\Ticket\PabController::class, 'editMember']);
                Route::any('members/update/{id}', [App\Http\Controllers\Ticket\PabController::class, 'updateMember']);
                Route::get('pab_history/{id}', [App\Http\Controllers\Ticket\PabController::class, 'historyView']);
                Route::post('pab-history-ajax', [App\Http\Controllers\Ticket\PabController::class, 'historylistAjax']);
                Route::get('pab_member_history/{id}', [App\Http\Controllers\Ticket\PabController::class, 'historyMemberView']);
                Route::post('pab-member-history-ajax', [App\Http\Controllers\Ticket\PabController::class, 'historylistMemberAjax']);
                Route::get('export', [App\Http\Controllers\Ticket\PabController::class, 'export']);
            });

            // Route::get('tickets/departments', [App\Http\Controllers\Ticket\ConfigController::class, 'getDepartmentsByQuery']);

            // Technician leadership board
            Route::get('technician-leaderboard', [App\Http\Controllers\Ticket\TechnicianLeaderboardController::class, 'index']);
            Route::get('fetchLeaderboard', [App\Http\Controllers\Ticket\TechnicianLeaderboardController::class, 'fetchLeaderboard']);
            Route::get('fetchLeaderboardCache', [App\Http\Controllers\Ticket\TechnicianLeaderboardController::class, 'fetchLeaderboardCache']);
            Route::get('getTechnicianDetailsCache/{id}', [App\Http\Controllers\Ticket\TechnicianLeaderboardController::class, 'getTechnicianDetailsCache']);
            Route::get('leaderboard/{id}', [App\Http\Controllers\Ticket\TechnicianLeaderboardController::class, 'getTechnicianDetails']);
            Route::get('leaderboard/tickets/{id}', [App\Http\Controllers\Ticket\TechnicianLeaderboardController::class, 'getTechnicianTickets']);
            Route::get('leaderboard/escalations/{id}', [App\Http\Controllers\Ticket\TechnicianLeaderboardController::class, 'getTechnicianEscalations']);
            Route::get('leaderboard/feedback/{id}', [App\Http\Controllers\Ticket\TechnicianLeaderboardController::class, 'getTechnicianFeedback']);
            Route::get('leaderboard-calculate', [App\Http\Controllers\Ticket\TechnicianLeaderboardController::class, 'calculateAndStoreLeaderboard']);
            Route::get('leaderboard/sla_table/{id}', [App\Http\Controllers\Ticket\TechnicianLeaderboardController::class, 'getTechnicianSLA']);
            Route::get('print-tech-performance/{id}', [App\Http\Controllers\Ticket\TechnicianLeaderboardController::class, 'printTechPerformance']);

            Route::get('getforms', [App\Http\Controllers\Ticket\StatusController::class, 'getForm']);
            Route::get('get-status-formid', [App\Http\Controllers\Ticket\StatusController::class, 'getStatusFormId']);
            Route::get('tickets/ajaxDashboard', [App\Http\Controllers\Ticket\IndexController::class, 'ajaxDashboard']);
            Route::get('tickets/getReportData', [App\Http\Controllers\Ticket\IndexController::class, 'getReportData'])->name('getReportData');
            Route::get('tickets/getSlaToBreachedIn4HoursData', [App\Http\Controllers\Report\TicketController::class, 'getSlaToBreachedIn4HoursData'])->name('getSlaToBreachedIn4HoursData');
            Route::get('tickets/export', [App\Http\Controllers\Ticket\IndexController::class, 'exportTickets']);
            Route::get('mytickets', [App\Http\Controllers\Ticket\IndexController::class, 'myTickets']);
            Route::get('tickets/list/{main_filter?}', [App\Http\Controllers\Ticket\IndexController::class, 'tickets'])->middleware('client.route_restrict');
            // Route::get('closed-tickets', [App\Http\Controllers\Ticket\IndexController::class, 'closedTickets']);
            Route::get('ticket/mail/body/{id}', [App\Http\Controllers\Ticket\IndexController::class, 'mailBody'])->name('ticket.mail.body');
            Route::get('ticket/{id}', [App\Http\Controllers\Ticket\IndexController::class, 'ticket'])->name('ticket-view');
            Route::get('ticket-report/pdf', [App\Http\Controllers\Ticket\TicketController::class, 'ticketPdf'])->name('ticket-pdf');
            Route::post('tickets/ajax-my-lists', [App\Http\Controllers\Ticket\IndexController::class, 'ajaxMyTickets']);
            Route::post('tickets/ajax-lists', [App\Http\Controllers\Ticket\IndexController::class, 'ajaxTickets']);
            // Route::post('tickets/ajax-closed-lists', [App\Http\Controllers\Ticket\IndexController::class, 'ajaxClosedTickets']);
            Route::get('service-tickets', [App\Http\Controllers\Ticket\IndexController::class, 'serviceTickets']);
            Route::get('tickets/init', [App\Http\Controllers\Ticket\IndexController::class, 'initiate']);
            Route::post('tickets/create', [App\Http\Controllers\Ticket\IndexController::class, 'create']);
            Route::post('tickets/create-by-user', [App\Http\Controllers\Ticket\IndexController::class, 'createByUser']);
            Route::post('ticket/delete', [App\Http\Controllers\Ticket\IndexController::class, 'deleteTicket']);
            Route::post('ticket/edit', [App\Http\Controllers\Ticket\IndexController::class, 'editTicket']);
            Route::post('ticket/staring', [App\Http\Controllers\Ticket\IndexController::class, 'toggleStar']);
            Route::post('ticket/update_status', [App\Http\Controllers\Ticket\IndexController::class, 'updateStatus']);
            Route::post('ticket/add_comment', [App\Http\Controllers\Ticket\IndexController::class, 'addComment']);
            Route::post('ticket/get_timeline', [App\Http\Controllers\Ticket\IndexController::class, 'getTimeline']);
            Route::post('kd/get_timeline', [App\Http\Controllers\KnowledgeManagement\DocumentController::class, 'getKDTimeline']);
            Route::post('kd/getRelatedDocuments', [App\Http\Controllers\KnowledgeManagement\DocumentController::class, 'getRelatedDocuments']);
            Route::post('ticket/get_timeline_archived', [App\Http\Controllers\Ticket\IndexController::class, 'getTimelineforArchived']);
            Route::get('ticket/get_users_to_assign/{id}', [App\Http\Controllers\Ticket\IndexController::class, 'getUsersToAssign']);
            Route::get('ticket/get_users_to_assign_by_dep/{id}', [App\Http\Controllers\Ticket\IndexController::class, 'getUserToAssignByDepartment']);
            Route::get('ticket/get_users_to_assign_by_availability/{id}', [App\Http\Controllers\Ticket\TechnicianActivityLogController::class, 'getUsersToAssignWithAvailability']);
            Route::get('ticket/get-data-for-transfer/{id}', [App\Http\Controllers\Ticket\IndexController::class, 'getDataForTransfer']);
            Route::get('ticket/get-self-assign-mode/{id}', [App\Http\Controllers\Ticket\IndexController::class, 'getSelfAssignMode']);
            Route::post('ticket/transfer', [App\Http\Controllers\Ticket\IndexController::class, 'transfer']);
            Route::post('ticket/assign_to', [App\Http\Controllers\Ticket\IndexController::class, 'AssignedTo']);
            Route::post('ticket/self_assign', [App\Http\Controllers\Ticket\IndexController::class, 'selfAssign']);
            Route::post('ticket/change_creator', [App\Http\Controllers\Ticket\IndexController::class, 'changeCreator']);
            Route::post('ticket/spam', [App\Http\Controllers\Ticket\IndexController::class, 'markAsSpam']);
            Route::post('ticket/feedback/add', [App\Http\Controllers\Ticket\IndexController::class, 'addFeedback']);
            Route::get('ticket/feedback/edit/{id}', [App\Http\Controllers\Ticket\IndexController::class, 'editFeedback']);
            Route::post('ticket/feedback/update', [App\Http\Controllers\Ticket\IndexController::class, 'updateFeedback']);
            Route::post('ticket/tags/update', [App\Http\Controllers\Ticket\IndexController::class, 'updateTicketTag'])->name('updateTicketTags');
            Route::post('ticket/reopen', [App\Http\Controllers\Ticket\IndexController::class, 'reopen']);
            Route::post('tickets/merge_tickets', [App\Http\Controllers\Ticket\IndexController::class, 'mergeTickets']);
            Route::post('tickets/update-master-cc/{ticket_id}', [App\Http\Controllers\Ticket\IndexController::class, 'updateMasterCc']);
            Route::post('ticket/ticket_history', [App\Http\Controllers\Ticket\IndexController::class, 'ticketHistory']);
            Route::post('ticket/ticket_history_archived', [App\Http\Controllers\Ticket\IndexController::class, 'ticketHistoryArchived']);
            Route::get('tickets/serviceRequestForm/{id}', [App\Http\Controllers\Ticket\TicketController::class, 'serviceRequestFormDetails']);
            Route::post('ticket/is_valid_workaround', [App\Http\Controllers\Ticket\IndexController::class, 'isValidWorkaround']);

            Route::post('ticket/attachment/add', [App\Http\Controllers\Ticket\AttachmentController::class, 'add']);
            Route::post('ticket/attachment/remove', [App\Http\Controllers\Ticket\AttachmentController::class, 'remove']);
            Route::get('ticket/attachment/download/{id}', [App\Http\Controllers\Ticket\AttachmentController::class, 'download']);

            // Route::post('ticket/getCustomFieldsByCategory', [App\Http\Controllers\Ticket\IndexController::class, 'getCustomFieldsByCategory'])->name('getCustomFieldsByCategory');
            Route::get('getCustomFieldNote/{id}', [App\Http\Controllers\CustomFieldController::class, 'getCustomFieldNote']);
            Route::post('ticket/type/updateDetails', [App\Http\Controllers\Ticket\IndexController::class, 'updateTicketTypeDetails'])->name('updateTicketTypeDetails');
            Route::any('tickets/departments/{ticket_id?}', [App\Http\Controllers\Ticket\IndexController::class, 'departments']);
            Route::any('tickets/ticket_getcustomview/{id?}', [App\Http\Controllers\Ticket\IndexController::class, 'getView']);

            Route::get('departments/by-company/privilage/{id}', [App\Http\Controllers\Config\DepartmentController::class, 'getUsersEnabledDepartment']); // get department based on the given privilage in tick configuration

            Route::get('tickets/service-types/by-dept/{id}', [App\Http\Controllers\TicketServiceTypeController::class, 'ajaxOptions']);
            Route::get('tickets/problem-types/by-st/{id}', [App\Http\Controllers\TicketProblemTypeController::class, 'ajaxOptions']);

            Route::get('tickets/service-types', [App\Http\Controllers\TicketServiceTypeController::class, 'index']);
            Route::post('tickets/ajax-service-types', [App\Http\Controllers\TicketServiceTypeController::class, 'ajaxIndex']);
            Route::get('tickets/delete-service-type/{id}', [App\Http\Controllers\TicketServiceTypeController::class, 'ajaxDelete']);
            Route::post('tickets/create-service-type', [App\Http\Controllers\TicketServiceTypeController::class, 'ajaxCreate']);
            Route::post('tickets/edit-service-type/{id}', [App\Http\Controllers\TicketServiceTypeController::class, 'ajaxUpdate']);
            Route::get('tickets/get-service-type-for-edit/{id}', [App\Http\Controllers\TicketServiceTypeController::class, 'ajaxGetEditInfo']);

            Route::get('tickets/problem-categories', [App\Http\Controllers\Ticket\ProblemCategoryController::class, 'index']);
            Route::post('tickets/ajax-problem-categories', [App\Http\Controllers\Ticket\ProblemCategoryController::class, 'ajaxIndex']);
            Route::post('tickets/create-problem-category', [App\Http\Controllers\Ticket\ProblemCategoryController::class, 'ajaxCreate']);
            Route::post('tickets/edit-problem-category/{id}', [App\Http\Controllers\Ticket\ProblemCategoryController::class, 'ajaxUpdate']);
            Route::get('tickets/delete-problem-category/{id}', [App\Http\Controllers\Ticket\ProblemCategoryController::class, 'ajaxDelete']);
            Route::get('tickets/get-problem-category-for-edit/{id}', [App\Http\Controllers\Ticket\ProblemCategoryController::class, 'ajaxGetEditInfo']);
            Route::get('tickets/problem-categories/by-dept/{id}/{company_id?}', [App\Http\Controllers\Ticket\ProblemCategoryController::class, 'ajaxOptions']);
            Route::get('kd/problem-categories/by-dept/{id}', [App\Http\Controllers\KnowledgeManagement\DocumentController::class, 'ajaxOptions']);
            Route::get('tickets/problem-categories/get-esclations-info/{pro_id}', [App\Http\Controllers\Ticket\ProblemCategoryController::class, 'getEsclationsInfo']);
            Route::post('tickets/problem-categories/add-esclation/{pro_id}', [App\Http\Controllers\Ticket\ProblemCategoryController::class, 'addEsclation']);
            Route::post('tickets/problem-categories/update-esclation/{escl_id}', [App\Http\Controllers\Ticket\ProblemCategoryController::class, 'updateEsclation']);
            Route::get('tickets/problem-categories/del-esclation/{escl_id}', [App\Http\Controllers\Ticket\ProblemCategoryController::class, 'deleteEsclation']);
            Route::any('tickets/problem-categories/get-esclatable-users/{pro_id}', [App\Http\Controllers\Ticket\ProblemCategoryController::class, 'getEsclatableUsers']);
            Route::get('tickets/problem-categories/export-problemcategory', [App\Http\Controllers\Ticket\ProblemCategoryController::class, 'exportProblemCategory']);
            Route::get('tickets/problem-categories/exportProblemCategoryPDF', [App\Http\Controllers\Ticket\ProblemCategoryController::class, 'exportProblemCategoryPDF']);
            Route::any('tickets/problem-categories-import', [App\Http\Controllers\Ticket\ProblemCategoryController::class, 'categoryImport'])->name('problemCat-import');
            Route::get('tickets/problem-categories-history/{id}', [App\Http\Controllers\Ticket\ProblemCategoryController::class, 'historyList']);
            Route::post('tickets/problem-categories-history-ajax/', [App\Http\Controllers\Ticket\ProblemCategoryController::class, 'historyListAjax']);
            Route::get('tickets/getAuthorityBoard', [App\Http\Controllers\Ticket\PabController::class, 'getAuthorityBoard']);
            Route::get('tickets/formList', [App\Http\Controllers\FormController::class, 'getFormList']);
            Route::get('tickets/problem-categories/esclation-history/{id}', [App\Http\Controllers\Ticket\ProblemCategoryController::class, 'esclationHistoryList']);
            Route::post('tickets/problem-categories/esclation-history-ajax/', [App\Http\Controllers\Ticket\ProblemCategoryController::class, 'esclationHistoryListAjax']);
            Route::get('tickets/problem-categories/tasklist/{id}', [App\Http\Controllers\Ticket\ProblemCategoryController::class, 'getTaskList']);
            Route::get('tickets/problem-categories/ajaxgetTask', [App\Http\Controllers\Ticket\ProblemCategoryController::class, 'ajaxgetTask']);
            Route::post('tickets/problem-categories/ajaxAddTask', [App\Http\Controllers\Ticket\ProblemCategoryController::class, 'ajaxAddTask']);
            Route::get('tickets/problem-categories/editTask', [App\Http\Controllers\Ticket\ProblemCategoryController::class, 'ajaxEditTask']);
            Route::post('tickets/problem-categories/updateTask/{id}', [App\Http\Controllers\Ticket\ProblemCategoryController::class, 'ajaxUpdateTask']);
            Route::get('tickets/problem-categories/deleteTask/{id}', [App\Http\Controllers\Ticket\ProblemCategoryController::class, 'ajaxDeleteTask']);
            Route::get('tickets/problem-categories/exportTasks', [App\Http\Controllers\Ticket\ProblemCategoryController::class, 'exportTasks']);
            Route::get('tickets/problem-categories/task-history/{id}', [App\Http\Controllers\Ticket\ProblemCategoryController::class, 'taskHistory']);
            Route::get('tickets/problem-categories/task-history-ajax', [App\Http\Controllers\Ticket\ProblemCategoryController::class, 'ajaxTaskHistory']);
            Route::post('tickets/problem-categories/reorder', [App\Http\Controllers\Ticket\ProblemCategoryController::class, 'updateTasksOrder']);
            // Escalation group route start
            Route::get('tickets/escalation-groups', [App\Http\Controllers\Ticket\EscalationGroupController::class, 'index']);
            Route::post('tickets/escalation-groups/store', [App\Http\Controllers\Ticket\EscalationGroupController::class, 'store']);
            Route::post('jx-get-escalation-groups', [App\Http\Controllers\Ticket\EscalationGroupController::class, 'fetchEscalationGroupByAjax']);
            Route::get('jx-escalation-group-delete/{id}', [App\Http\Controllers\Ticket\EscalationGroupController::class, 'deleteGroup']);
            Route::get('jx-escalation-group-edit/{id}', [App\Http\Controllers\Ticket\EscalationGroupController::class, 'editGroup']);
            Route::post('jx-escalation-group-edit/{id}', [App\Http\Controllers\Ticket\EscalationGroupController::class, 'updateGroup']);
            Route::get('user-escalation-groups/manage-users/{id}', [App\Http\Controllers\Ticket\EscalationGroupController::class, 'escalationUserManage']);
            Route::post('updateLocation', [App\Http\Controllers\Ticket\EscalationGroupController::class, 'updateLocation']);
            Route::get('escalation-groups-export', [App\Http\Controllers\Ticket\EscalationGroupController::class, 'exportGroups']);
            Route::get('tickets/get-esclatable-groups/{id}', [App\Http\Controllers\Ticket\EscalationGroupController::class, 'getEscalationGroup']);

            Route::any('jx-add-escalation-group-user', [App\Http\Controllers\Ticket\EscalationGroupController::class, 'addGroupUser']);
            Route::any('tickets/escalation-groups/manage-users', [App\Http\Controllers\Ticket\EscalationGroupController::class, 'manageUsers']);
            Route::any('jx-escalation-user-delete/{recordId}', [App\Http\Controllers\Ticket\EscalationGroupController::class, 'deleteGroupUser']);
            Route::any('jx-escalation-edit-group-member-details/{id}', [App\Http\Controllers\Ticket\EscalationGroupController::class, 'editGroupMemberDetails']);
            Route::any('jx-get-user-escalation-member/{id}', [App\Http\Controllers\Ticket\EscalationGroupController::class, 'getGroupUserForEdit']);
            Route::get('getEscalateUsers', [App\Http\Controllers\Ticket\EscalationGroupController::class, 'getEscalateUsers']);
            Route::any('tickets/bulk-escalation-import', [App\Http\Controllers\Ticket\EscalationGroupController::class, 'importBulkEscalation']);
            // Escalation group route end
             Route::get('getAutoAllocationDepartment/{id}', [App\Http\Controllers\Config\DepartmentController::class, 'getAutoAllocationDepartments']);

            Route::get('tickets/problem-types', [App\Http\Controllers\TicketProblemTypeController::class, 'index']);
            Route::post('tickets/ajax-problem-types', [App\Http\Controllers\TicketProblemTypeController::class, 'ajaxIndex']);
            Route::post('tickets/create-problem-type', [App\Http\Controllers\TicketProblemTypeController::class, 'ajaxCreate']);
            Route::post('tickets/edit-problem-type/{id}', [App\Http\Controllers\TicketProblemTypeController::class, 'ajaxUpdate']);
            Route::get('tickets/delete-problem-type/{id}', [App\Http\Controllers\TicketProblemTypeController::class, 'ajaxDelete']);
            Route::get('tickets/get-problem-type-for-edit/{id}', [App\Http\Controllers\TicketProblemTypeController::class, 'ajaxGetEditInfo']);

            Route::any('tickets/alias_account_list', [App\Http\Controllers\Ticket\EmailConfigurationController::class, 'ajaxAliasAccountList']);
            Route::any('tickets/ajax_add_alias_acc', [App\Http\Controllers\Ticket\EmailConfigurationController::class, 'ajax_add_alias_acc']);
            Route::any('tickets/ajax_load_alias_acc/{id}', [App\Http\Controllers\Ticket\EmailConfigurationController::class, 'ajax_load_alias_acc']);
            Route::any('tickets/ajax_update_alias_acc/{id}', [App\Http\Controllers\Ticket\EmailConfigurationController::class, 'ajax_update_alias_acc']);
            Route::any('tickets/ajax_delete_alias_acc/{id}', [App\Http\Controllers\Ticket\EmailConfigurationController::class, 'ajax_delete_alias_acc']);

            Route::get('tickets/email-config', [App\Http\Controllers\Ticket\EmailConfigurationController::class, 'ajaxindex']);
            Route::post('tickets/email_config_list', [App\Http\Controllers\Ticket\EmailConfigurationController::class, 'ajaxEmailConfigurationList']);
            Route::post('tickets/ajax-auto-acc-create', [App\Http\Controllers\Ticket\EmailConfigurationController::class, 'ajaxAddAutoCreation']);
            Route::any('tickets/ajax-auto-acc-delete/{id}', [App\Http\Controllers\Ticket\EmailConfigurationController::class, 'deleteAutoCreation']);
            Route::any('tickets/ajax-auto-acc-get/{id}', [App\Http\Controllers\Ticket\EmailConfigurationController::class, 'ajaxgetAutoCreation']);
            Route::any('tickets/ajax-auto-acc-edit/{id}', [App\Http\Controllers\Ticket\EmailConfigurationController::class, 'ajaxeditAutoCreation']);
            Route::any('tickets/ajax-auto-acc-force-complete/{id}', [App\Http\Controllers\Ticket\EmailConfigurationController::class, 'forcetoComplete']);

            Route::get('tickets/reply-email', [App\Http\Controllers\Ticket\EmailConfigurationController::class, 'ajaxReplyEmailIndex']);
            Route::get('tickets/reply-email-list', [App\Http\Controllers\Ticket\EmailConfigurationController::class, 'ajaxReplyEmailList']);
            Route::post('tickets/ajax-reply-email-create', [App\Http\Controllers\Ticket\EmailConfigurationController::class, 'ajaxReplyEmailAdd']);
            Route::any('tickets/ajax-reply-email-delete/{id}', [App\Http\Controllers\Ticket\EmailConfigurationController::class, 'ajaxReplyEmailDelete']);
            Route::any('tickets/ajax-reply-email-get/{id}', [App\Http\Controllers\Ticket\EmailConfigurationController::class, 'ajaxReplyEmailGet']);
            Route::any('tickets/ajax-reply-email-edit/{id}', [App\Http\Controllers\Ticket\EmailConfigurationController::class, 'ajaxReplyEmailUpdate']);

            Route::get('tickets/email-config-accounts', [App\Http\Controllers\Ticket\EmailConfigurationController::class, 'ajaxConfigAccounts']);
            Route::post('tickets/ajax-add-scheduled-block', [App\Http\Controllers\Ticket\EmailConfigurationController::class, 'ajaxAddScheduledBlock']);
            Route::post('tickets/scheduled-block-list', [App\Http\Controllers\Ticket\EmailConfigurationController::class, 'ajaxScheduledBlockList']);
            Route::any('tickets/ajax-delete-scheduled-block/{id}', [App\Http\Controllers\Ticket\EmailConfigurationController::class, 'deleteScheduledBlock']);
            Route::any('tickets/ajax-get-scheduled-block/{id}', [App\Http\Controllers\Ticket\EmailConfigurationController::class, 'getScheduledBlock']);
            Route::any('tickets/ajax-edit-scheduled-block/{id}', [App\Http\Controllers\Ticket\EmailConfigurationController::class, 'editScheduledBlock']);

            /* New Ticket Design */

            Route::get('tickets/archived-remote', [App\Http\Controllers\Ticket\ArchivedController::class, 'archivedRemote'])->name('archivedRemote');
            Route::get('tickets/archived-ajax-remote', [App\Http\Controllers\Ticket\ArchivedController::class, 'ajaxArchived'])->name('archivedRemote');
            Route::get('tickets/get-archived-ticket-details', [App\Http\Controllers\Ticket\ArchivedController::class, 'getArchivedTicketDetails'])->name('getArchivedTicketDetails');
            Route::post('tickets/get-archived-ticket-history', [App\Http\Controllers\Ticket\ArchivedController::class, 'getArchivedTicketHistory']);
            Route::get('tickets/archived-export', [App\Http\Controllers\Ticket\ArchivedController::class, 'exportArchivedTicket']);
            Route::post('tickets/archived-timeline', [App\Http\Controllers\Ticket\ArchivedController::class, 'getArchivedTimeline']);

            Route::get('tickets/newlist/{main_filter?}', [App\Http\Controllers\Ticket\TicketController::class, 'newdesign'])->middleware('client.route_restrict')->name('allTicketLists');
            Route::post('tickets/check-reopen-eligibility', [App\Http\Controllers\Ticket\TicketController::class, 'checkReopenEligibility']);
            Route::post('tickets/list/jx-ticket-detail', [App\Http\Controllers\Ticket\TicketController::class, 'ajaxTicketDetails']);
            Route::post('tickets/list/jx-ticket-archived-detail', [App\Http\Controllers\Ticket\TicketController::class, 'ajaxTicketArchivedDetails']);
            Route::post('ticket/delete_multiple_ticket', [App\Http\Controllers\Ticket\TicketController::class, 'deleteMultiple']);
            Route::post('ticket/resolved_multiple_ticket', [App\Http\Controllers\Ticket\TicketController::class, 'resolvedMultiple']);
            Route::post('tickets/bulkAssign', [App\Http\Controllers\Ticket\TicketController::class, 'bulkAssignTicket']);
            Route::post('user-ticket-handler-list',[App\Http\Controllers\UserController::class, 'userTicketHandlerList']);
            Route::any('ticket/getTagDetails', [App\Http\Controllers\Ticket\TicketController::class, 'ajaxTagDetail']);
            Route::any('ticket/getCategory', [App\Http\Controllers\Ticket\TicketController::class, 'ajaxCategory']);
            Route::post('tickets/list/jx-article-detail', [App\Http\Controllers\Ticket\TicketController::class, 'convertArticle']);
            Route::any('ticket/sla/{id}', [App\Http\Controllers\Ticket\TicketController::class, 'getTicketSla']);
            Route::post('tickets/ajaxBulkAssignTicketList', [App\Http\Controllers\Ticket\TicketController::class, 'ajaxBulkAssignTicketList']);
            Route::get('tickets/get-relevant-tasks', [App\Http\Controllers\Ticket\TicketController::class, 'getRelevantTasks']);
            Route::get('delete-task/{id}', [App\Http\Controllers\Ticket\TicketController::class, 'deleteTask']);
            Route::get('edit-task/{id}', [App\Http\Controllers\Ticket\TicketController::class, 'editTask']);
            Route::get('ticket-getSentiment/{id}', [App\Http\Controllers\Ticket\TicketController::class, 'getSentiment']);

            Route::get('tickets/out-going-mail', [App\Http\Controllers\Ticket\OutgoingMailController::class, 'mailList']);
            Route::post('jx-mail-list', [App\Http\Controllers\Ticket\OutgoingMailController::class, 'ajaxmailList']);
            Route::get('tickets/out-going-mail/deletemail/delete/{id}', [App\Http\Controllers\Ticket\OutgoingMailController::class, 'deleteMail']);
            Route::post('tickets/out-going-mail/createMail', [App\Http\Controllers\Ticket\OutgoingMailController::class, 'createMail']);
            Route::get('tickets/out-going-mail/edit/{id}', [App\Http\Controllers\Ticket\OutgoingMailController::class, 'editMail']);
            Route::post('tickets/out-going-mail/update', [App\Http\Controllers\Ticket\OutgoingMailController::class, 'updateMail']);

            Route::get('ticket-types', [App\Http\Controllers\Ticket\TicketTypeController::class, 'ticketTypes']);
            Route::post('ajax-ticket-type-list', [App\Http\Controllers\Ticket\TicketTypeController::class, 'ticketTypeData']);
            Route::post('tickets/ticket-types/add', [App\Http\Controllers\Ticket\TicketTypeController::class, 'addTicketType']);
            Route::get('tickets/ticket-types/delete/{id}', [App\Http\Controllers\Ticket\TicketTypeController::class, 'deleteTicketType']);
            Route::get('getTicketTypeInfo/{id}', [App\Http\Controllers\Ticket\TicketTypeController::class, 'getTicketTypeInfo']);
            Route::get('ticket-type-export/{search?}', [App\Http\Controllers\Ticket\TicketTypeController::class, 'exportTicketType']);
            Route::get('tickets/getTicketTypeByDepartment/{id}', [App\Http\Controllers\Ticket\TicketTypeController::class, 'getTicketTypeByDepartment']);
            Route::get('tickets/getTicketTypeByCompany', [App\Http\Controllers\Ticket\TicketTypeController::class, 'getTicketTypeByCompany']);

            // LTTS DU/BU listing Routes
            Route::get('getDuBuHead', [App\Http\Controllers\Ticket\DuBuHeadUserController::class, 'getDuBuHead']);
            Route::post('ajaxGetDuBuHead', [App\Http\Controllers\Ticket\DuBuHeadUserController::class, 'ajaxGetDuBuHead']);
            Route::any('ajaxGetDuBuHead/Export', [App\Http\Controllers\Ticket\DuBuHeadUserController::class, 'ajaxGetDuBuHeadExport']);

            // mail templates routes start
            Route::get('mail-templates', [App\Http\Controllers\Ticket\MailTemplateController::class, 'index']);
            Route::post('ajax-mailTemplate', [App\Http\Controllers\Ticket\MailTemplateController::class, 'ajaxMailTemplate']);
            Route::get('mail-template/add', [App\Http\Controllers\Ticket\MailTemplateController::class, 'create']);
            Route::post('mail-template/store', [App\Http\Controllers\Ticket\MailTemplateController::class, 'store']);
            Route::get('getProblemCategoryByAjax/{id}', [App\Http\Controllers\Ticket\MailTemplateController::class, 'getProblemCategoryByAjax']);
            Route::get('mail-template/edit/{id}', [App\Http\Controllers\Ticket\MailTemplateController::class, 'edit']);
            Route::post('mail-template/update/{id}', [App\Http\Controllers\Ticket\MailTemplateController::class, 'update'])->name('mail-template/update');
            Route::get('mail-templates/export/{search?}/', [App\Http\Controllers\Ticket\MailTemplateController::class, 'exportMailTempalte']);
            Route::get('mail-templates/delete/{id}', [App\Http\Controllers\Ticket\MailTemplateController::class, 'deleteMailTempalte']);
            Route::get('getShortcodeByAjax/{search}', [App\Http\Controllers\Ticket\MailTemplateController::class, 'getShortcodeByAjax']);
            // mail templates routes end

            //mail template short code routes start
            Route::get('mail-template-shortcodes', [App\Http\Controllers\Ticket\MailTemplateShortcodeController::class, 'index']);
            Route::post('ajax-all-mailTemplateShortcode', [App\Http\Controllers\Ticket\MailTemplateShortcodeController::class, 'FetchAllShortcode']);
            Route::post('mail-template-shortcode/add', [App\Http\Controllers\Ticket\MailTemplateShortcodeController::class, 'saveShortcode']);
            Route::get('mail-template-shortcode/edit/{id}', [App\Http\Controllers\Ticket\MailTemplateShortcodeController::class, 'EditSohortcode']);
            Route::get('getShortcodeForEdit/{id}', [App\Http\Controllers\Ticket\MailTemplateShortcodeController::class, 'getShortcodeForEdit']);
            Route::post('mail-template-shortcode/update', [App\Http\Controllers\Ticket\MailTemplateShortcodeController::class, 'updateShortcode']);
            Route::get('mail-template-shortcode/delete/{id}', [App\Http\Controllers\Ticket\MailTemplateShortcodeController::class, 'deleteShortcode']);
            Route::get('mail-template-shortcode/exportExcel/{search?}', [App\Http\Controllers\Ticket\MailTemplateShortcodeController::class, 'exportShortcode']);
            //mail template short code routes end

            // kanban related routes start
            Route::get('tickets/kanban-board', [App\Http\Controllers\Ticket\KanbanController::class, 'index']);
            Route::get('tickets/statuses', [App\Http\Controllers\Ticket\KanbanController::class, 'getStatuses']);
            Route::post('tickets/jx-kanban-list', [App\Http\Controllers\Ticket\KanbanController::class, 'ajaxKanbanList']);
            Route::post('tickets/js-kanban-store', [App\Http\Controllers\Ticket\KanbanController::class, 'ajaxKanbanStore']);
            Route::get('tickets/js-kanban-edit/{id}', [App\Http\Controllers\Ticket\KanbanController::class, 'ajaxKanbanEdit']);
            Route::post('tickets/js-kanban-update/{id}', [App\Http\Controllers\Ticket\KanbanController::class, 'ajaxKanbanUpdate']);
            Route::get('tickets/js-kanban-delete/{id}', [App\Http\Controllers\Ticket\KanbanController::class, 'ajaxKanbanDelete']);
            Route::get('tickets/kanban-member/{id}', [App\Http\Controllers\Ticket\KanbanController::class, 'kanbanMemberList']);
            Route::post('ticket/get-board-data', [App\Http\Controllers\Ticket\KanbanController::class, 'GetBoardData']);
            Route::post('ticket/get-grouped-board-data', [App\Http\Controllers\Ticket\KanbanController::class, 'getGroupedBoardData']);
            Route::post('tickets/js-kanban-member', [App\Http\Controllers\Ticket\KanbanController::class, 'ajaxKanbanMemberList']);
            Route::post('tickets/js-kanban-member-store', [App\Http\Controllers\Ticket\KanbanController::class, 'ajaxKanbanMemberStore']);
            Route::get('tickets/js-kanban-edit-member/{id}', [App\Http\Controllers\Ticket\KanbanController::class, 'ajaxKanbanMemberEdit']);
            Route::post('tickets/js-kanban-update-member/{id}', [App\Http\Controllers\Ticket\KanbanController::class, 'ajaxKanbanMemberupdate']);
            Route::get('tickets/js-kanban-delete-member/{id}', [App\Http\Controllers\Ticket\KanbanController::class, 'ajaxKanbanMemberDelete']);
            Route::get('tickets/kanban-board/boards', [App\Http\Controllers\Ticket\KanbanController::class, 'kanbanBoardCardList']);
            Route::any('tickets/kanban-board/config', [App\Http\Controllers\Ticket\KanbanConfigController::class, 'index']);
            Route::any('tickets/kanban-board/board-list', [App\Http\Controllers\Ticket\KanbanController::class, 'ajaxBoardList']);
            Route::post('tickets/list/jx-ticket-card-view', [App\Http\Controllers\Ticket\KanbanController::class, 'cardView']);
            Route::post('tickets/list/jx-ticket-card-details-view', [App\Http\Controllers\Ticket\KanbanController::class, 'cardDetailsView']);
            Route::post('tickets/loadCards', [App\Http\Controllers\Ticket\KanbanController::class, 'loadCard']);
            Route::post('tickets/loadMemberImage', [App\Http\Controllers\Ticket\KanbanController::class, 'loadMemberImage']);
            Route::post('tickets/removeBoardMember', [App\Http\Controllers\Ticket\KanbanController::class, 'removeBoardMember']);
            Route::get('tickets/kanban-card-export', [App\Http\Controllers\Ticket\KanbanController::class, 'exportCard']);
            Route::get('tickets/fetch-sub-category', [App\Http\Controllers\Ticket\ProblemCategoryController::class, 'fetchSubCategory']);
            Route::get('tickets/getTickets', [App\Http\Controllers\Ticket\KanbanController::class, 'getTickets']);
            Route::post('tickets/store-board-item-ticket', [App\Http\Controllers\Ticket\KanbanController::class, 'storeBoardItemTicket']);
            Route::post('tickets/loadCustomCards', [App\Http\Controllers\Ticket\KanbanController::class, 'loadCustomCard']);
            Route::get('kanban-card/attachments', [App\Http\Controllers\Ticket\KanbanController::class, 'getAttachments']);
            Route::post('tickets/board-item-ticket-update', [App\Http\Controllers\Ticket\KanbanController::class, 'boardItemTicketUpdate']);
            Route::post('tickets/attachment/card_attachment', [App\Http\Controllers\Ticket\KanbanController::class, 'cardAttachment']);
            Route::post('tickets/attachment/card_attachment_remove', [App\Http\Controllers\Ticket\KanbanController::class, 'cardAttachmentRemove']);
            Route::get('tickets/attachment/card_attachment_download/{id}', [App\Http\Controllers\Ticket\KanbanController::class, 'cardAttachmentDownload']);
            Route::get('tickets/kanban-board/get-card/{id}', [App\Http\Controllers\Ticket\KanbanController::class, 'getCard']);
            Route::post('tickets/kanban-board/update-card/{id}', [App\Http\Controllers\Ticket\KanbanController::class, 'updateCard']);
            Route::post('tickets/kanban-board/add_custom_comment', [App\Http\Controllers\Ticket\KanbanController::class, 'addComment']);
            Route::get('ticket/kanban-board/card_attachment/view/{id}/{thumb?}', [App\Http\Controllers\Ticket\KanbanController::class, 'viewAttachment']);
            Route::post('ticket/kanban-board/card_get_timeline', [App\Http\Controllers\Ticket\KanbanController::class, 'getTimelineCustomCard']);
            Route::Post('ticket/kanban-board/delete_card/{id}', [App\Http\Controllers\Ticket\KanbanController::class, 'deleteCard']);
            Route::Post('ticket/kanban-board/add_card', [App\Http\Controllers\Ticket\KanbanController::class, 'addCard']);
            Route::any('tickets/kanban-board/import', [App\Http\Controllers\Ticket\KanbanController::class, 'boardImport']);
            Route::post('tickets/kanban-board/updateColumnOrder', [App\Http\Controllers\Ticket\KanbanController::class, 'updateColumnOrder']);
            Route::get('tickets/fetch-active-access/{id}', [App\Http\Controllers\Ticket\TicketController::class, 'fetchActiveTicket']);
            Route::post('tickets/kanban-board/update-kanban-title', [App\Http\Controllers\Ticket\KanbanController::class, 'updateTitle']);
            Route::post('tickets/kanban-board/clone-item', [App\Http\Controllers\Ticket\KanbanController::class, 'cloneItem']);
            Route::get('tickets/kanban-board/get-items-by-board', [App\Http\Controllers\Ticket\KanbanController::class, 'getItemsByBoard']);
            Route::post('tickets/getKanbanBoardFieldSet', [App\Http\Controllers\Ticket\KanbanController::class, 'getKanbanBoardFieldSet']);
            Route::get('tickets/getKanbanBoardFields/{id}', [App\Http\Controllers\Ticket\KanbanController::class, 'getKanbanBoardFields']);
            Route::get('tickets/getKanbanItemStatusInfo/{id}', [App\Http\Controllers\Ticket\KanbanController::class, 'getKanbanItemStatusInfo']);
            Route::post('custom/archive-cards/{board}', [App\Http\Controllers\Ticket\KanbanController::class, 'archiveCards'])->name('archive.cards');
            Route::post('tickets/kanban-board/get-board-member-history', [App\Http\Controllers\Ticket\KanbanController::class, 'getBoardMemberHistory']);
            Route::post('tickets/js-kanban-history/{id}',  [App\Http\Controllers\Ticket\KanbanController::class, 'ajaxKanbanHistory']);
            Route::post('tickets/kanban-board/getCardHistory/{id}', [App\Http\Controllers\Ticket\KanbanController::class, 'getCardHistory']);
            Route::post('tickets/list/jx-ticket-card-details-view', [App\Http\Controllers\Ticket\KanbanController::class, 'cardDetailsView']);
            Route::get('ticket/getKanbanCustomFieldsValueForFilter/{id}', [App\Http\Controllers\Ticket\KanbanController::class, 'getKanbanCustomFieldsValueForFilter']);
            // kanban related routes end

            Route::post('tickets/fetch-git-form/{name}', [App\Http\Controllers\FormController::class, 'fetchGitForm']);

            Route::group(['prefix' => 'reports'], function() {
                Route::get('tickets/location-wise', [App\Http\Controllers\Report\TicketController::class, 'locationWise']);
                Route::post('tickets/jx-location-wise', [App\Http\Controllers\Report\TicketController::class, 'ajaxLocationWise']);
                Route::get('tickets/jx-download-location-wise', [App\Http\Controllers\Report\TicketController::class, 'ajaxDownloadTicketLocationWise']);
            });
        });
        /* ---------------------- */

        Route::get('locations/config', [App\Http\Controllers\Location\ConfigController::class, 'index']);
        Route::any('locations/config/ajax-user-privileges', [App\Http\Controllers\Location\ConfigController::class, 'ajaxUserPrivileges']);
        Route::any('locations/config/update-privilege', [App\Http\Controllers\Location\ConfigController::class, 'updatePrivilege']);
        Route::any('export-location-configuration-excel', [App\Http\Controllers\Location\ConfigController::class, 'exportLocationConfiguration']);

        Route::group(['prefix' => 'projects'], function () {
            Route::get('list', [App\Http\Controllers\ProjectManagement\ProjectController::class, 'getIndex']);
            Route::get('ajaxList', [App\Http\Controllers\ProjectManagement\ProjectController::class, 'ajaxList']);
            Route::post('ajaxAdd', [App\Http\Controllers\ProjectManagement\ProjectController::class, 'ajaxAdd']);
            Route::post('ajaxEdit/{id}', [App\Http\Controllers\ProjectManagement\ProjectController::class, 'ajaxEdit']);
            Route::get('ajaxGet/{id}', [App\Http\Controllers\ProjectManagement\ProjectController::class, 'ajaxGet']);
            Route::get('ajaxDelete/{id}', [App\Http\Controllers\ProjectManagement\ProjectController::class, 'ajaxDelete']);
            Route::get('getProjectsByQuery', [App\Http\Controllers\ProjectManagement\ProjectController::class, 'getProjectsByQuery']);
            Route::any('import', [App\Http\Controllers\ProjectManagement\ProjectController::class, 'projectImport']);
            Route::get('export-project-info', [App\Http\Controllers\ProjectManagement\ProjectController::class, 'projectInfoExport']);
            Route::any('bulk-import-info', [App\Http\Controllers\Bulk\ConfigBulkActionController::class, 'ajaxBulkImportProjectInfo']);
        });

        Route::group(['prefix' => 'visitor'], function () {
            Route::get('/', [App\Http\Controllers\Visitor\VisitorController::class, 'index']);
            Route::post('jx-visitor-list', [App\Http\Controllers\Visitor\VisitorController::class, 'visitor_list']);
            Route::post('create-visitor', [App\Http\Controllers\Visitor\VisitorController::class, 'create_visitor']);
            Route::get('edit-visitor/{id}', [App\Http\Controllers\Visitor\VisitorController::class, 'edit_visitor']);
            Route::post('update-visitor/{id}', [App\Http\Controllers\Visitor\VisitorController::class, 'update_visitor']);
            Route::get('delete-visitor/{id}', [App\Http\Controllers\Visitor\VisitorController::class, 'delete_visitor']);
        });

        Route::group(['prefix' => 'project-management'], function () {
            Route::get('myproject', [App\Http\Controllers\ProjectManagement\IndexController::class, 'myProject']);
            Route::get('project-details/{id}', [App\Http\Controllers\ProjectManagement\IndexController::class, 'projectDetails']);
            Route::get('project-devices', [App\Http\Controllers\ProjectManagement\IndexController::class, 'projectDevices']);
            Route::get('project-tickets', [App\Http\Controllers\ProjectManagement\IndexController::class, 'projectTickets']);
            Route::get('project-users', [App\Http\Controllers\ProjectManagement\IndexController::class, 'projectUsers']);
            Route::get('project-info', [App\Http\Controllers\ProjectManagement\IndexController::class, 'projectInfo']);
            Route::get('list/{param?}', [App\Http\Controllers\ProjectManagement\IndexController::class, 'getIndex']);
            Route::get('ajaxList', [App\Http\Controllers\ProjectManagement\IndexController::class, 'ajaxList']);
            Route::post('ajaxAdd', [App\Http\Controllers\ProjectManagement\IndexController::class, 'ajaxAdd']);
            Route::post('ajaxEdit/{id}', [App\Http\Controllers\ProjectManagement\IndexController::class, 'ajaxEdit']);
            Route::get('ajaxGet/{id}', [App\Http\Controllers\ProjectManagement\IndexController::class, 'ajaxGet']);
            Route::get('ajaxDelete/{id}', [App\Http\Controllers\ProjectManagement\IndexController::class, 'ajaxDelete']);
            Route::get('getProjectsByQuery', [App\Http\Controllers\ProjectManagement\IndexController::class, 'getProjectsByQuery']);
            Route::any('import', [App\Http\Controllers\ProjectManagement\IndexController::class, 'projectManagementImport']);
            Route::get('status', [App\Http\Controllers\ProjectManagement\StatusController::class, 'index'])->name('status');
            Route::post('status', [App\Http\Controllers\ProjectManagement\StatusController::class, 'store'])->name('status.store');
            Route::get('status/edit/{id}', [App\Http\Controllers\ProjectManagement\StatusController::class, 'editStatus'])->name('status.edit');
            Route::post('status/update', [App\Http\Controllers\ProjectManagement\StatusController::class, 'updateStatus'])->name('status.update');
            Route::get('status/delete/{id}', [App\Http\Controllers\ProjectManagement\StatusController::class, 'deleteStatus'])->name('status.delete');
            Route::post('jx-project-status-list', [App\Http\Controllers\ProjectManagement\StatusController::class, 'ajaxstatusList'])->name('jx.project.status.list');
            Route::get('project-users', [App\Http\Controllers\ProjectManagement\IndexController::class, 'projectUsers']);
            Route::get('get-project-users', [App\Http\Controllers\ProjectManagement\IndexController::class, 'getProjectsUsers'])->name('get.project.users');
            Route::post('add-milestone', [App\Http\Controllers\ProjectManagement\IndexController::class, 'addProjectMilestone'])->name('add.milestone');
            Route::get('ajax-project-milestone-list', [App\Http\Controllers\ProjectManagement\IndexController::class, 'ajaxProjectMilestoneList'])->name('ajax.project.milestone.list');
            Route::post('edit-milestone/{id}', [App\Http\Controllers\ProjectManagement\IndexController::class, 'editMilestone'])->name('edit.milestone');
            Route::get('get-milestone/{id}', [App\Http\Controllers\ProjectManagement\IndexController::class, 'getMilestone'])->name('get.milestone');
            Route::get('delete-milestone/{id}', [App\Http\Controllers\ProjectManagement\IndexController::class, 'deleteMilestone'])->name('delete.milestone');
            Route::get('project-mileston-info/{id}', [App\Http\Controllers\ProjectManagement\IndexController::class, 'projectMilestoneInfo'])->name('project.mileston.info');
            Route::get('mileston-info', [App\Http\Controllers\ProjectManagement\IndexController::class, 'milestoneInfo'])->name('mileston.info');
            Route::get('get-mileston-handlers', [App\Http\Controllers\ProjectManagement\IndexController::class, 'getMilestoneHandlers'])->name('get.mileston.handlers');
            Route::post('get-mileston-docs', [App\Http\Controllers\ProjectManagement\IndexController::class, 'getMilestoneDocs'])->name('get.mileston.docs');
            Route::post('get-project-docs', [App\Http\Controllers\ProjectManagement\IndexController::class, 'getProjectDocs'])->name('get.project.docs');
            Route::post('add-project-docs', [App\Http\Controllers\ProjectManagement\IndexController::class, 'addProjectDocs'])->name('add.project.docs');
            Route::get('delete-project-docs/{id}', [App\Http\Controllers\ProjectManagement\IndexController::class, 'deleteProjectDocs'])->name('delete.project.docs');
            Route::post('project-history', [App\Http\Controllers\ProjectManagement\IndexController::class, 'getProjectHistory'])->name('project.history');
            Route::get('project-history-detail', [App\Http\Controllers\ProjectManagement\IndexController::class, 'getHistoryDetails'])->name('project.history-detail');
            Route::post('project-milestone-history', [App\Http\Controllers\ProjectManagement\IndexController::class, 'getProjectMilestonHistory'])->name('project.milestone.history');
            Route::get('project-milestone-history-detail', [App\Http\Controllers\ProjectManagement\IndexController::class, 'getMilestoneHistoryDetails'])->name('project.milestone.history.detail');
            Route::post('project-users-history', [App\Http\Controllers\ProjectManagement\IndexController::class, 'getProjectUsersHistory'])->name('project.users.history');
            Route::get('project-users-history-detail', [App\Http\Controllers\ProjectManagement\IndexController::class, 'getUserseHistoryDetails'])->name('project.users.history.detail');
            Route::post('update-project-users/{id}', [App\Http\Controllers\ProjectManagement\IndexController::class, 'updateProjectUsers']);
            Route::get('client', [App\Http\Controllers\ProjectManagement\ClientController::class, 'index']);
            Route::post('client-list', [App\Http\Controllers\ProjectManagement\ClientController::class, 'clientList'])->name('client.list');
            Route::post('client-add', [App\Http\Controllers\ProjectManagement\ClientController::class, 'createClient'])->name('add.client');
            Route::get('client-edit/{id}', [App\Http\Controllers\ProjectManagement\ClientController::class, 'editClient'])->name('edit.client');
            Route::get('client-delete/{id}', [App\Http\Controllers\ProjectManagement\ClientController::class, 'deleteClient'])->name('delete.client');
            Route::post('client-update/{id}', [App\Http\Controllers\ProjectManagement\ClientController::class, 'updateClient'])->name('update.client');
            Route::get('client-view/{id}', [App\Http\Controllers\ProjectManagement\ClientController::class, 'viewClientInfo'])->name('client.view');
            Route::get('client-info', [App\Http\Controllers\ProjectManagement\ClientController::class, 'viewClient'])->name('client.info');
            Route::post('get-client-docs', [App\Http\Controllers\ProjectManagement\ClientController::class, 'getClientDocs'])->name('get.client.docs');
            Route::post('add-client-docs', [App\Http\Controllers\ProjectManagement\ClientController::class, 'addClientDocs'])->name('add.client.docs');
            Route::get('delete-client-docs/{id}', [App\Http\Controllers\ProjectManagement\ClientController::class, 'deleteClientDocs'])->name('delete.client.docs');
            Route::get('client-projects', [App\Http\Controllers\ProjectManagement\ClientController::class, 'clientProjectList'])->name('client.projects');
            Route::post('client-members', [App\Http\Controllers\ProjectManagement\ClientController::class, 'clientMemberList'])->name('client.members');
            Route::post('create-members', [App\Http\Controllers\ProjectManagement\ClientController::class, 'createMember'])->name('create.members');
            Route::get('edit-members/{id}', [App\Http\Controllers\ProjectManagement\ClientController::class, 'editMember'])->name('edit.members');
            Route::post('update-members/{id}', [App\Http\Controllers\ProjectManagement\ClientController::class, 'updateMember'])->name('update.members');
            Route::get('delete-member/{id}', [App\Http\Controllers\ProjectManagement\ClientController::class, 'deleteMember'])->name('delete.members');
            Route::post('client-history', [App\Http\Controllers\ProjectManagement\ClientController::class, 'clientHistory'])->name('client.history');
            Route::post('client-member-history', [App\Http\Controllers\ProjectManagement\ClientController::class, 'clientMemberHistory'])->name('client.member.history');
            Route::get('client-member-history-detail', [App\Http\Controllers\ProjectManagement\ClientController::class, 'clientMemberHistoryDetails'])->name('client.member.history.detail');
            Route::get('client-history-details', [App\Http\Controllers\ProjectManagement\ClientController::class, 'clientHistoryDetails'])->name('client.history.details');
            Route::match(['get', 'post'], '/import-project-clients', [App\Http\Controllers\ProjectManagement\ClientController::class, 'importProjectClients'])->name('import.project.clients');
            Route::get('getClientByQuery', [App\Http\Controllers\ProjectManagement\ClientController::class, 'getClientByQuery']);
        });

        // block calendar for ticket start
        Route::group(['prefix' => 'block-calendar'], function () {
            Route::get('get-users', [App\Http\Controllers\BlockCalendar\BlockCalendarController::class, 'getUsers'])->name('get.users');
            Route::post('add-block-calendar', [App\Http\Controllers\BlockCalendar\BlockCalendarController::class, 'addBlockCalendar'])->name('add.block.calendar');
            Route::get('edit-block-calendar/{id}', [App\Http\Controllers\BlockCalendar\BlockCalendarController::class, 'editBlockCalendar'])->name('edit.block.calendar');
            Route::post('update-block-calendar/{id}', [App\Http\Controllers\BlockCalendar\BlockCalendarController::class, 'updateBlockCalendar'])->name('update.block.calendar');
            Route::get('get-block-calendars', [App\Http\Controllers\BlockCalendar\BlockCalendarController::class, 'blockCalendalList'])->name('get.block.calendars');
            Route::get('get-events', [App\Http\Controllers\BlockCalendar\BlockCalendarController::class, 'getCalendarEvent']);
            Route::get('get-detail', [App\Http\Controllers\BlockCalendar\BlockCalendarController::class, 'getCalendarEventDetails']);
            Route::get('get-refresh-token', [App\Http\Controllers\BlockCalendar\BlockCalendarController::class, 'getRefreshToken'])->name('get.refresh.token');
            Route::get('microsoft', [App\Http\Controllers\BlockCalendar\BlockCalendarController::class, 'redirectToProvider'])->name('login.microsoft');
            Route::get('callback', [App\Http\Controllers\BlockCalendar\BlockCalendarController::class, 'handleProviderCallback']);
            Route::get('event_feedback/{id}', [App\Http\Controllers\BlockCalendar\BlockCalendarController::class, 'eventFeedback']);
            Route::post('update_event_feedback', [App\Http\Controllers\BlockCalendar\BlockCalendarController::class, 'updateEventFeedback']);
        });

        // api integration all route define here
        Route::group(['prefix' => 'api-integration'], function () {
            Route::get('/', [App\Http\Controllers\ApiIntegration\IndexController::class, 'index'])->name('index');
            Route::post('store', [App\Http\Controllers\ApiIntegration\IndexController::class, 'store'])->name('store');
            Route::get('list/{main_filter?}', [App\Http\Controllers\ApiIntegration\IndexController::class, 'ajaxApiList'])->name('apiList');
            Route::get('edit/{id}', [App\Http\Controllers\ApiIntegration\IndexController::class, 'editApiDefination'])->name('editApiDefination');
            Route::post('update/{id}', [App\Http\Controllers\ApiIntegration\IndexController::class, 'updateApiDefination'])->name('updateApiDefination');
            Route::get('delete/{id}', [App\Http\Controllers\ApiIntegration\IndexController::class, 'deleteApiDefination'])->name('deleteApiDefination');
        });
        // end of api integration route

        Route::group(['prefix' => 'procurements'], function () {
            Route::get('dashboard', [App\Http\Controllers\Procurement\DashboardController::class, 'dashboard']);
            Route::get('ajax-dashboard', [App\Http\Controllers\Procurement\DashboardController::class, 'ajaxDashboard']);
            Route::get('list/{main_filter?}', [App\Http\Controllers\Procurement\ListController::class, 'list']);
            Route::post('ajax-list', [App\Http\Controllers\Procurement\ListController::class, 'ajaxList']);
            Route::get('info/{id}', [App\Http\Controllers\Procurement\ListController::class, 'info']);
            // Route::get('add', [App\Http\Controllers\Procurement\ListController::class, 'add']);
            // Route::get('financialyear', [App\Http\Controllers\Procurement\ListController::class, 'financialyear']);
            // Route::get('budget', [App\Http\Controllers\Procurement\ListController::class, 'budget']);
            // Route::get('budgetwise', [App\Http\Controllers\Procurement\ListController::class, 'budgetwise']);

            // delivery chalan
            Route::get('delivery-challan-list', [App\Http\Controllers\Procurement\DeliveryChallanList::class, 'chalanList']);
            Route::post('delivery-challan', [App\Http\Controllers\Procurement\DeliveryChallanList::class, 'createChalan']);
            Route::get('edit-challan/{id}', [App\Http\Controllers\Procurement\DeliveryChallanList::class, 'editChalan']);
            Route::post('update-challan/{id}', [App\Http\Controllers\Procurement\DeliveryChallanList::class, 'updateChalan']);
            Route::get('delete-challan/{id}', [App\Http\Controllers\Procurement\DeliveryChallanList::class, 'deleteChalan']);
            // procure invoice
            Route::get('invoice-list', [App\Http\Controllers\Procurement\InvoiceController::class, 'invoiceList']);
            Route::post('procure-invoice', [App\Http\Controllers\Procurement\InvoiceController::class, 'createInvoice']);
            Route::get('edit-invoice/{id}', [App\Http\Controllers\Procurement\InvoiceController::class, 'editInvoice']);
            Route::post('update-invoice/{id}', [App\Http\Controllers\Procurement\InvoiceController::class, 'updateInvoice']);
            Route::get('delete-invoice/{id}', [App\Http\Controllers\Procurement\InvoiceController::class, 'deleteInvoice']);

            Route::get('users', [App\Http\Controllers\Procurement\ListController::class, 'users']);
            Route::post('listItems', [App\Http\Controllers\Procurement\ListController::class, 'listItems']);
            Route::get('getItemLastPrice/{id}', [App\Http\Controllers\Procurement\ListController::class, 'getItemLastPrice']);
            Route::get('view', [App\Http\Controllers\Procurement\ListController::class, 'view']);
            Route::get('export-procurement', [App\Http\Controllers\Procurement\ListController::class, 'downloadProcurement']);
            // Route::get('custom_tax', [App\Http\Controllers\Procurement\CustomTaxController::class, 'getIndex']);
            // Route::post('custom_tax/getCustomTax', [App\Http\Controllers\Procurement\CustomTaxController::class, 'ajaxCustomTax']);
            // Route::post('custom_tax/save', [App\Http\Controllers\Procurement\CustomTaxController::class, 'saveCustomTax']);
            // Route::post('custom_tax/remove', [App\Http\Controllers\Procurement\CustomTaxController::class, 'removeCustomTax']);
            // Route::post('custom_tax/remove-element', [App\Http\Controllers\Procurement\CustomTaxController::class, 'removeCustomElement']);
            // Route::post('custom_tax/update', [App\Http\Controllers\Procurement\CustomTaxController::class, 'updateCustomTax']);
            // Route::get('custom_tax/edit', [App\Http\Controllers\Procurement\CustomTaxController::class, 'editCustomTax']);
            Route::any('get-customtax/{custom_id?}', [App\Http\Controllers\Procurement\CustomTaxController::class, 'getCustomtax']);
            Route::get('get-element/{id}', [App\Http\Controllers\Procurement\CustomTaxController::class, 'getElement']);
            Route::any('get-companies/{custom_id?}', [App\Http\Controllers\Procurement\PoCompanyController::class, 'getCompanies']);
            Route::get('get-summernote/{id}', [App\Http\Controllers\Procurement\PoCompanyController::class, 'getSummernote']);
            Route::get('getInchargeByProcurement', [App\Http\Controllers\Procurement\UserPrivilegeController::class, 'getInchargeByProcurement']);
            Route::get('getInchargeByProcurementAndSupplier', [App\Http\Controllers\Procurement\UserPrivilegeController::class, 'getInchargeByProcurementAndSupplier']);
            Route::post('bulkInchargeByProcurement', [App\Http\Controllers\Procurement\ListController::class, 'bulkInchargeSubmit']);
            Route::get('custom_charge_elements/list', [App\Http\Controllers\Procurement\ChargesElementController::class, 'getIndex']);
            Route::post('ajaxlistCustomCharge', [App\Http\Controllers\Procurement\ChargesElementController::class, 'ajaxList']);
            Route::post('create-charges', [App\Http\Controllers\Procurement\ChargesElementController::class, 'saveCustomCharges']);
            Route::get('charge_delete/{id}', [App\Http\Controllers\Procurement\ChargesElementController::class, 'deleteCharge']);
            Route::post('update-charges/{id}', [App\Http\Controllers\Procurement\ChargesElementController::class, 'updateCustomCharges']);
            Route::get('edit-charges/{id}', [App\Http\Controllers\Procurement\ChargesElementController::class, 'editCharges']);
            Route::get('get_extra_charges', [App\Http\Controllers\Procurement\ChargesElementController::class, 'getExtraCharge']);
            Route::any('charge_history/{id}', [App\Http\Controllers\Procurement\ChargesElementController::class, 'getHistory']);
            // procurement po company
            Route::get('companies', [App\Http\Controllers\Procurement\PoCompanyController::class, 'index']);
            Route::get('getCompanyByQuery', [App\Http\Controllers\Procurement\PoCompanyController::class, 'getCompanyByQuery']);
            Route::post('getPoCompanyByAjax', [App\Http\Controllers\Procurement\PoCompanyController::class, 'getPoCompanyByAjax']);
            Route::post('AddPoCompanyByAjax', [App\Http\Controllers\Procurement\PoCompanyController::class, 'store']);
            Route::get('getPoCompanyByAjaxForEdit/{id}', [App\Http\Controllers\Procurement\PoCompanyController::class, 'edit']);
            Route::post('getPoCompany/update', [App\Http\Controllers\Procurement\PoCompanyController::class, 'update']);
            Route::get('getPoCompany/export/{search?}', [App\Http\Controllers\Procurement\PoCompanyController::class, 'exportReport']);
            Route::get('getPoCompany/delete/{id}', [App\Http\Controllers\Procurement\PoCompanyController::class, 'deletePoCompay']);

            Route::get('print-po/{pr_id}', [App\Http\Controllers\Procurement\QuotationController::class, 'printPo']);
            Route::get('print-cer/{pr_id}', [App\Http\Controllers\Procurement\QuotationController::class, 'printCer']);
            Route::get('getLocationByProcurement', [App\Http\Controllers\Procurement\QuotationController::class, 'getLocationByProcurement']);

            Route::group(['prefix' => 'requests'], function () {
                Route::get('add', [App\Http\Controllers\Procurement\RequestController::class, 'add']);
                Route::post('delete', [App\Http\Controllers\Procurement\RequestController::class, 'delete']);
                Route::any('info/{id}', [App\Http\Controllers\Procurement\RequestController::class, 'info']);
                Route::get('ajax-get/{id}', [App\Http\Controllers\Procurement\RequestController::class, 'ajaxGet']);
                Route::post('ajax-add', [App\Http\Controllers\Procurement\RequestController::class, 'ajaxAdd']);
                Route::post('ajax-savedraft', [App\Http\Controllers\Procurement\RequestController::class, 'ajaxsaveDraft']); //
                Route::post('ajax-edit/{id}', [App\Http\Controllers\Procurement\RequestController::class, 'ajaxEdit']);
                Route::get('ajax-delete/{id}', [App\Http\Controllers\Procurement\RequestController::class, 'ajaxDelete']);
                Route::post('ajax-update/{id}', [App\Http\Controllers\Procurement\RequestController::class, 'ajaxUpdate']);
                Route::get('ajax-approval-page-charts', [App\Http\Controllers\Procurement\DashboardController::class, 'ajaxApprovalPageCharts']);
                Route::any('history/{id}', [App\Http\Controllers\Procurement\RequestController::class, 'getHistory']);
                Route::post('ajax-comments/{id}', [App\Http\Controllers\Procurement\RequestController::class, 'ajaxComments']);
                Route::post('get_timeline', [App\Http\Controllers\Procurement\RequestController::class, 'getTimeline']);
                Route::post('attachment/add', [App\Http\Controllers\Procurement\AttachmentController::class, 'add']);
                Route::post('attachment/remove', [App\Http\Controllers\Procurement\AttachmentController::class, 'remove']);
                Route::get('attachment/download/{id}', [App\Http\Controllers\Procurement\AttachmentController::class, 'download']);
                Route::get('attachment/view/{id}', [App\Http\Controllers\Procurement\AttachmentController::class, 'viewItem']);
                // Route::post('ajax-update-supplier/{id}', [App\Http\Controllers\Procurement\RequestController::class, 'ajaxUpdateSupplier']);
                Route::post('incharge_to', [App\Http\Controllers\Procurement\RequestController::class, 'inchargeTo']);
                Route::get('get-supplier-documents/{id}', [App\Http\Controllers\Procurement\RequestController::class, 'getSupplierDocuments']);
                Route::get('supplier-document/download/{id}', [App\Http\Controllers\Procurement\RequestController::class, 'downloadSupplierDocument']);
                Route::get('supplier-document/view/{id}', [App\Http\Controllers\Procurement\RequestController::class, 'viewSupplierDocument']);
                Route::any('getcustomview/{id?}', [App\Http\Controllers\Procurement\RequestController::class, 'getCustomView']);
                Route::post('custom_form_save', [App\Http\Controllers\Procurement\RequestController::class, 'requestedFormSave']);

                Route::group(['prefix' => 'quotations'], function () {
                    Route::get('update/{pr_id}/{qnum}', [App\Http\Controllers\Procurement\QuotationController::class, 'updateScreen']);
                    Route::post('ajax-update/{pr_id}/{qnum}', [App\Http\Controllers\Procurement\QuotationController::class, 'ajaxUpdate']);
                    Route::get('ajax-remove/{pr_id}/{qnum}', [App\Http\Controllers\Procurement\QuotationController::class, 'ajaxRemove']);
                    Route::post('ajax-approve/{pr_id}', [App\Http\Controllers\Procurement\QuotationController::class, 'ajaxApprove']);
                    Route::post('ajax-choose-quotate/{pr_id}', [App\Http\Controllers\Procurement\QuotationController::class, 'ajaxChooseQuotation']);
                    Route::post('ajax-po-generate/{pr_id}', [App\Http\Controllers\Procurement\QuotationController::class, 'ajaxGeneratePo']);
                    Route::get('download/{pr_id}/{attachment_name}', [App\Http\Controllers\Procurement\QuotationController::class, 'download']);
                    Route::get('preview/{pr_id}/{attachment_name}', [App\Http\Controllers\Procurement\QuotationController::class, 'preview']);
                    Route::get('file/preview/{path}/{name}', [App\Http\Controllers\Procurement\QuotationController::class, 'previewFile'])->name('procurements.file.preview');
                    Route::post('ajax-update-quotate/{pr_id}', [App\Http\Controllers\Procurement\QuotationController::class, 'ajaxUpdateQuotation']);
                    Route::post('ajax-revoke-decision/{pr_id}', [App\Http\Controllers\Procurement\QuotationController::class, 'ajaxRevokeDecision'])->name('procurement:quotationRevokeDecision');
                    Route::post('payment_list', [App\Http\Controllers\Procurement\QuotationController::class, 'paymentList']);
                    Route::post('addpayment', [App\Http\Controllers\Procurement\QuotationController::class, 'paymentAdd']);
                    Route::post('ajax-update-quotation-po/{pr_id}', [App\Http\Controllers\Procurement\QuotationController::class, 'ajaxupdateQuotationPO']);
                    Route::get('check-po-number', [App\Http\Controllers\Procurement\QuotationController::class, 'checkPONumber']);
                });

                Route::post('vendor/{id}', [App\Http\Controllers\Procurement\RequestController::class, 'vendorRequest']);
                // Route::post('ajax-update-supplier/{id}', 'Procurement\RequestController@ajaxUpdateSupplier');
            });

            Route::group(['prefix' => 'budgets'], function () {
                Route::get('list', [App\Http\Controllers\Procurement\BudgetController::class, 'getIndex']);
                Route::post('ajax-list', [App\Http\Controllers\Procurement\BudgetController::class, 'ajaxList']);
                Route::get('ajax-get/{id}', [App\Http\Controllers\Procurement\BudgetController::class, 'ajaxGet']);
                Route::post('ajax-add', [App\Http\Controllers\Procurement\BudgetController::class, 'ajaxAdd']);
                Route::post('ajax-edit/{id}', [App\Http\Controllers\Procurement\BudgetController::class, 'ajaxEdit']);
                Route::get('ajax-delete/{id}', [App\Http\Controllers\Procurement\BudgetController::class, 'ajaxDelete']);
                Route::get('ajax-by-department/{dep_id}', [App\Http\Controllers\Procurement\BudgetController::class, 'ajaxBudgetsByDepartment']);
                Route::get('account-type', [App\Http\Controllers\Procurement\BudgetController::class, 'accountType']);
                Route::get('financial-year', [App\Http\Controllers\Procurement\BudgetController::class, 'financialYear']);
                Route::get('history/{id}', [App\Http\Controllers\Procurement\BudgetController::class, 'historyView']);
                Route::post('history-ajax', [App\Http\Controllers\Procurement\BudgetController::class, 'historylistAjax']);
            });

            Route::group(['prefix' => 'financial-years'], function () {
                Route::get('list', [App\Http\Controllers\Procurement\FinancialYearController::class, 'getIndex']);
                Route::post('ajax-list', [App\Http\Controllers\Procurement\FinancialYearController::class, 'ajaxList']);
                Route::get('ajax-get/{id}', [App\Http\Controllers\Procurement\FinancialYearController::class, 'ajaxGet']);
                Route::post('ajax-add', [App\Http\Controllers\Procurement\FinancialYearController::class, 'ajaxAdd']);
                Route::post('ajax-edit/{id}', [App\Http\Controllers\Procurement\FinancialYearController::class, 'ajaxEdit']);
                Route::get('ajax-delete/{id}', [App\Http\Controllers\Procurement\FinancialYearController::class, 'ajaxDelete']);
            });

            Route::group(['prefix' => 'budget-categories'], function () {
                Route::get('list', [App\Http\Controllers\Procurement\BudgetCategoryController::class, 'getIndex']);
                Route::post('ajax-list', [App\Http\Controllers\Procurement\BudgetCategoryController::class, 'ajaxList']);
                Route::get('ajax-get/{id}', [App\Http\Controllers\Procurement\BudgetCategoryController::class, 'ajaxGet']);
                Route::post('ajax-add', [App\Http\Controllers\Procurement\BudgetCategoryController::class, 'ajaxAdd']);
                Route::post('ajax-edit/{id}', [App\Http\Controllers\Procurement\BudgetCategoryController::class, 'ajaxEdit']);
                Route::get('ajax-delete/{id}', [App\Http\Controllers\Procurement\BudgetCategoryController::class, 'ajaxDelete']);
            });

            Route::group(['prefix' => 'pab'], function () {
                Route::get('list', [App\Http\Controllers\Procurement\PabController::class, 'list']);
                Route::post('ajax-list', [App\Http\Controllers\Procurement\PabController::class, 'ajaxPabList']);
                Route::any('add', [App\Http\Controllers\Procurement\PabController::class, 'add']);
                Route::any('edit/{id}', [App\Http\Controllers\Procurement\PabController::class, 'edit']);
                Route::any('delete/{id}', [App\Http\Controllers\Procurement\PabController::class, 'delete']);
                Route::any('members/{id}', [App\Http\Controllers\Procurement\PabController::class, 'members']);
                Route::any('members/add/{id}', [App\Http\Controllers\Procurement\PabController::class, 'addMember']);
                Route::any('members/remove/{id}', [App\Http\Controllers\Procurement\PabController::class, 'removeMember']);
                Route::any('members/edit/{id}', [App\Http\Controllers\Procurement\PabController::class, 'editMember']);
                Route::any('members/update/{id}', [App\Http\Controllers\Procurement\PabController::class, 'updateMember']);
                Route::any('updateLocation', [App\Http\Controllers\Procurement\PabController::class, 'updateLocation']);
                Route::any('getBudgetRange', [App\Http\Controllers\Procurement\PabController::class, 'ajaxBudgetRange']);
                Route::any('addBudgetRange', [App\Http\Controllers\Procurement\PabController::class, 'addBudgetRange']);
                Route::any('fetchBudgetRange/{id}', [App\Http\Controllers\Procurement\PabController::class, 'fetchBudgetRange']);
                Route::any('updateBudgetRange/{id}', [App\Http\Controllers\Procurement\PabController::class, 'updateBudgetRange']);
                Route::post('checkBudgetExists', [App\Http\Controllers\Procurement\PabController::class, 'checkBudgetExists']);
                Route::any('deleteBudgetExists/{id}', [App\Http\Controllers\Procurement\PabController::class, 'deleteBudgetExists']);
                Route::any('getmemberList', [\App\Http\Controllers\Procurement\PabController::class, 'ajaxMemberList']);
                Route::post('updateLevelByDrag', [App\Http\Controllers\Procurement\PabController::class, 'updateLevelByDrag']);
                Route::get('export',  [App\Http\Controllers\Procurement\PabController::class, 'export']);
            });

            Route::group(['prefix' => 'users-privileges'], function () {
                Route::get('/', [App\Http\Controllers\Procurement\UserPrivilegeController::class, 'index']);
                Route::post('ajax-users', [App\Http\Controllers\Procurement\UserPrivilegeController::class, 'ajaxIndex']);
                Route::post('ajax-update', [App\Http\Controllers\Procurement\UserPrivilegeController::class, 'ajaxUpdate']);
            });

            Route::group(['prefix' => 'users'], function () {
                Route::get('/', [App\Http\Controllers\Procurement\UserPrivilegeController::class, 'users']);
                Route::post('ajax-list', [App\Http\Controllers\Procurement\UserPrivilegeController::class, 'ajaxList']);
                Route::post('ajax-add', [App\Http\Controllers\Procurement\UserPrivilegeController::class, 'ajaxAdd']);
                Route::any('ajax-edit/{id}', [App\Http\Controllers\Procurement\UserPrivilegeController::class, 'ajaxEdit']);
                Route::get('ajax-delete/{id}', [App\Http\Controllers\Procurement\UserPrivilegeController::class, 'ajaxDelete']);
                Route::any('ajax-get/{id}', [App\Http\Controllers\Procurement\UserPrivilegeController::class, 'ajaxGet']);
            });
        });

        Route::group(['prefix' => 'change-management'], function () {
            Route::get('dashboard', [App\Http\Controllers\ChangeManagement\ListController::class, 'dashboard']);
            Route::get('ajaxDashboard', [App\Http\Controllers\ChangeManagement\ListController::class, 'ajaxDashboard']);
            Route::get('list', [App\Http\Controllers\ChangeManagement\ListController::class, 'list']);
            Route::get('myRequestList', [App\Http\Controllers\ChangeManagement\ListController::class, 'list']);
            Route::get('myRequestList/{main_filter?}', [App\Http\Controllers\ChangeManagement\ListController::class, 'list']);
            Route::post('ajaxList', [App\Http\Controllers\ChangeManagement\ListController::class, 'ajaxList']);
            Route::post('add', [App\Http\Controllers\ChangeManagement\ListController::class, 'add']);
            Route::any('edit/{param}', [App\Http\Controllers\ChangeManagement\ListController::class, 'edit']);
            Route::get('delete/{param}', [App\Http\Controllers\ChangeManagement\ListController::class, 'delete']);
            Route::any('info/{id}', [App\Http\Controllers\ChangeManagement\ListController::class, 'info']);
            Route::any('history/{id}', [App\Http\Controllers\ChangeManagement\ListController::class, 'getHistory']);
            Route::post('ajaxUpdateDescription/{param}', [App\Http\Controllers\ChangeManagement\ListController::class, 'updateDescription']);
            Route::get('export-change-management', [App\Http\Controllers\ChangeManagement\ListController::class, 'downloadChangeManagement']);
            Route::get('getCategoriesByCompanyAccess', [App\Http\Controllers\ChangeManagement\ListController::class, 'getCategoriesByCompanyAccess']);


            Route::get('get-attachable-tasks/{param?}', [App\Http\Controllers\ChangeManagement\ListController::class, 'getAttachableTasks']);
            Route::get('get-tasks/{param}', [App\Http\Controllers\ChangeManagement\ListController::class, 'getTasksSelected']);
            Route::post('add-task/{param}', [App\Http\Controllers\ChangeManagement\ListController::class, 'addTask']);
            Route::get('remove-task/{param}', [App\Http\Controllers\ChangeManagement\ListController::class, 'removeTask']);

            Route::get('get-attachable-tickets/{param}', [App\Http\Controllers\ChangeManagement\ListController::class, 'getAttachableTickets']);
            Route::post('add-ticket/{param}', [App\Http\Controllers\ChangeManagement\ListController::class, 'addTicket']);
            Route::get('remove-ticket/{param}', [App\Http\Controllers\ChangeManagement\ListController::class, 'removeTicket']);

            Route::get('get-attachable-tickets', [App\Http\Controllers\ChangeManagement\ListController::class, 'getAttachableTickets']);
            Route::get('get-attachable-alltickets', [App\Http\Controllers\ChangeManagement\ListController::class, 'getAllAttachableTickets']);
            Route::get('get-attachable-devices/{param}', [App\Http\Controllers\ChangeManagement\ListController::class, 'getAttachableDevices']);
            Route::post('add-device/{param}', [App\Http\Controllers\ChangeManagement\ListController::class, 'addDevice']);
            Route::get('remove-device/{param}', [App\Http\Controllers\ChangeManagement\ListController::class, 'removeDevice']);

            Route::get('getChangesByQuery', [App\Http\Controllers\ChangeManagement\ListController::class, 'getChangesByQuery']);
            // Route::get('/', [App\Http\Controllers\ChangeManagement\SummaryController::class, 'index']);
            Route::post('ajax-comments/{id}', [App\Http\Controllers\ChangeManagement\ListController::class, 'ajaxComments']);
            Route::post('get_timeline', [App\Http\Controllers\ChangeManagement\ListController::class, 'getTimeline']);
            Route::get('getReportData', [App\Http\Controllers\ChangeManagement\ListController::class, 'getReportData']);
            Route::get('getUser', [App\Http\Controllers\ChangeManagement\ListController::class, 'getUser']);
            Route::post('impacted_ticket_list', [App\Http\Controllers\ChangeManagement\ListController::class, 'impactedTicketList']);
            Route::post('relevant_device_list', [App\Http\Controllers\ChangeManagement\ListController::class, 'relevantDeviceList']);
            Route::post('relevant_task_list', [App\Http\Controllers\ChangeManagement\ListController::class, 'relevantTaskList']);
            Route::post('attachment_list', [App\Http\Controllers\ChangeManagement\ListController::class, 'attachmentList']);
            Route::post('change-attachment-detail-page', [App\Http\Controllers\ChangeManagement\ListController::class, 'uploadAttachmentDetailFiles']);

            Route::group(['prefix' => 'cab'], function () {
                Route::get('list', [App\Http\Controllers\ChangeManagement\CabController::class, 'list']);
                Route::post('ajax-list', [App\Http\Controllers\ChangeManagement\CabController::class, 'ajaxList']);
                Route::any('add', [App\Http\Controllers\ChangeManagement\CabController::class, 'add']);
                Route::any('edit/{id}', [App\Http\Controllers\ChangeManagement\CabController::class, 'edit']);
                Route::any('delete/{id}', [App\Http\Controllers\ChangeManagement\CabController::class, 'delete']);
                Route::any('members/{id}', [App\Http\Controllers\ChangeManagement\CabController::class, 'members']);
                Route::any('members/add/{id}', [App\Http\Controllers\ChangeManagement\CabController::class, 'addMember']);
                Route::any('members/remove/{id}/{member_id}', [App\Http\Controllers\ChangeManagement\CabController::class, 'removeMember']);
                Route::any('members/edit/{id}', [App\Http\Controllers\ChangeManagement\CabController::class, 'editMember']);
                Route::any('members/update/{id}', [App\Http\Controllers\ChangeManagement\CabController::class, 'updateMember']);
                Route::post('actionCabUpdate', [App\Http\Controllers\ChangeManagement\CabController::class, 'actionUpdate'])->name('actionCabUpdate');
                Route::post('ajaxMembers/{id}', [App\Http\Controllers\ChangeManagement\CabController::class, 'ajaxMembers']);
                Route::get('fetchCab', [App\Http\Controllers\ChangeManagement\CabController::class, 'fetchCab']);
                Route::get('getCabForEdit/{id}', [App\Http\Controllers\ChangeManagement\CabController::class, 'getCabForEdit']);
                Route::post('updateCAB/{id}', [App\Http\Controllers\ChangeManagement\CabController::class, 'edit']);
                Route::post('viewHistory/{id}', [App\Http\Controllers\ChangeManagement\CabController::class, 'viewHistory']);
                Route::get('export', [App\Http\Controllers\ChangeManagement\CabController::class, 'export']);
            });

            Route::get('categories', [App\Http\Controllers\ChangeManagement\CategoryController::class, 'index']);
            Route::post('ajaxCategoryList', [App\Http\Controllers\ChangeManagement\CategoryController::class, 'ajaxCategoryList']);
            Route::post('store_category', [App\Http\Controllers\ChangeManagement\CategoryController::class, 'storeCategory']);
            Route::post('getCategory', [App\Http\Controllers\ChangeManagement\CategoryController::class, 'getCategory']);
            Route::post('update-category/{id}', [App\Http\Controllers\ChangeManagement\CategoryController::class, 'updateCategory']);
            Route::get('delete-category/{id}', [App\Http\Controllers\ChangeManagement\CategoryController::class, 'deleteCategory']);
            Route::get('export-categories', [App\Http\Controllers\ChangeManagement\CategoryController::class, 'exportCategories']);
            Route::post('change-attachment', [App\Http\Controllers\ChangeManagement\ListController::class, 'uploadAttachment']);
            Route::post('remove-attachment', [App\Http\Controllers\ChangeManagement\ListController::class, 'removeAttachment']);
            Route::get('view-image/{id}/{temp?}', [App\Http\Controllers\ChangeManagement\ListController::class, 'viewItem']);
            Route::get('download-image/{id}/{temp?}', [App\Http\Controllers\ChangeManagement\ListController::class, 'downloadItem']);
            Route::get('attachment/ajaxGet/{id}', [App\Http\Controllers\ChangeManagement\ListController::class, 'ajaxGetAttachment']);
            Route::post('attachment/ajaxEdit/{id}', [App\Http\Controllers\ChangeManagement\ListController::class, 'updateAttachment']);
        });

        Route::group(['prefix' => 'task-management'], function () {
            Route::get('list/{mainfilter?}', [App\Http\Controllers\TaskManagement\ListController::class, 'list']);
            Route::post('ajaxList', [App\Http\Controllers\TaskManagement\ListController::class, 'ajaxList']);
            Route::any('add', [App\Http\Controllers\TaskManagement\ListController::class, 'add']);
            Route::any('edit/{param}', [App\Http\Controllers\TaskManagement\ListController::class, 'edit']);
            Route::post('ajaxAddTask', [App\Http\Controllers\TaskManagement\ListController::class, 'ajaxAddTask']);
            Route::post('ajaxEditTask/{id}', [App\Http\Controllers\TaskManagement\ListController::class, 'ajaxEditTask']);
            Route::post('ajaxStatusEditTask/{id}', [App\Http\Controllers\TaskManagement\ListController::class, 'ajaxStatusEditTask']);
            Route::get('info/{id}', [App\Http\Controllers\TaskManagement\ListController::class, 'info']);
            Route::get('delete/{id}', [App\Http\Controllers\TaskManagement\ListController::class, 'delete']);
            Route::get('getTaskStatus', [App\Http\Controllers\TaskManagement\ListController::class, 'getTaskStatus']);
            Route::get('getTaskPriority', [App\Http\Controllers\TaskManagement\ListController::class, 'getTaskPriority']);
            Route::get('export', [App\Http\Controllers\TaskManagement\ListController::class, 'exportTask']);
            Route::get('getTickets', [App\Http\Controllers\TaskManagement\ListController::class, 'getTickets']);
            Route::post('taskhistory', [App\Http\Controllers\TaskManagement\ListController::class, 'history']);
            Route::post('add_comment', [App\Http\Controllers\TaskManagement\ListController::class, 'addComment']);
            Route::post('get_comment', [App\Http\Controllers\TaskManagement\ListController::class, 'getComments']);
            Route::post('add_attachment', [App\Http\Controllers\TaskManagement\ListController::class, 'addAttachment']);
            Route::get('download_attachment/{id}', [App\Http\Controllers\TaskManagement\ListController::class, 'downloadAttachment']);
            Route::get('view_attachment/{id}', [App\Http\Controllers\TaskManagement\ListController::class, 'viewAttachment']);
            Route::post('remove_attachment', [App\Http\Controllers\TaskManagement\ListController::class, 'removeAttachment']);
            Route::post('self-assign', [App\Http\Controllers\TaskManagement\ListController::class, 'selfAssign']);
            Route::get('getRelevantArchivedTasks', [App\Http\Controllers\TaskManagement\ArchivedTaskController::class, 'getRelevantArchivedTasks']);
            Route::post('get_archived_comment', [App\Http\Controllers\TaskManagement\ArchivedTaskController::class, 'getComments']);
            Route::post('archivedAjaxList', [App\Http\Controllers\TaskManagement\ArchivedTaskController::class, 'archivedAjaxList']);
            Route::get('archivedTaskInfo/{id}', [App\Http\Controllers\TaskManagement\ArchivedTaskController::class, 'archivedTaskInfo']);
            Route::post('archivedTaskhistory', [App\Http\Controllers\TaskManagement\ArchivedTaskController::class, 'archivedTaskhistory']);
            Route::get('archivedExport', [App\Http\Controllers\TaskManagement\ArchivedTaskController::class, 'exportArchivedTask']);
            Route::get('get-department-category', [App\Http\Controllers\TaskManagement\ListController::class, 'getDepartmentAndCategory']);
        });

        Route::group(['prefix' => 'mailroom-management'], function () {
            // site routes
            Route::get('site', [App\Http\Controllers\Mailroom\SiteController::class, 'index'])->name('mailroom.management.site');
            Route::post('site-list', [App\Http\Controllers\Mailroom\SiteController::class, 'siteList'])->name('mailroom.management.site.list');
            Route::post('create-site', [App\Http\Controllers\Mailroom\SiteController::class, 'createSite'])->name('mailroom.management.site.create');
            Route::get('edit-site/{id}', [App\Http\Controllers\Mailroom\SiteController::class, 'editSite'])->name('mailroom.management.site.edit');
            Route::post('update-site/{id}', [App\Http\Controllers\Mailroom\SiteController::class, 'updateSite'])->name('mailroom.management.site.update');
            Route::get('delete-site/{id}', [App\Http\Controllers\Mailroom\SiteController::class, 'deleteSite'])->name('mailroom.management.site.delete');
            Route::get('ajaxSite', [App\Http\Controllers\Mailroom\SiteController::class, 'ajaxSite']);
            //  mailroom status
            Route::get('status', [App\Http\Controllers\Mailroom\StatusController::class, 'index'])->name('mailroom.management.status');
            Route::get('getStatus', [App\Http\Controllers\Mailroom\StatusController::class, 'getStatus'])->name('mailroom.management.statusget');
            Route::post('status-list', [App\Http\Controllers\Mailroom\StatusController::class, 'statusList'])->name('mailroom.management.status.list');
            Route::post('create-status', [App\Http\Controllers\Mailroom\StatusController::class, 'createStatus'])->name('mailroom.management.status.create');
            Route::get('edit-status/{id}', [App\Http\Controllers\Mailroom\StatusController::class, 'editStatus'])->name('mailroom.management.status.edit');
            Route::post('update-status/{id}', [App\Http\Controllers\Mailroom\StatusController::class, 'updateStatus'])->name('mailroom.management.status.update');
            Route::get('delete-status/{id}', [App\Http\Controllers\Mailroom\StatusController::class, 'deleteStatus'])->name('mailroom.management.status.delete');
            Route::get('getMailroomCustomFields', [App\Http\Controllers\Mailroom\StatusController::class, 'getMailroomCustomFields'])->name('mailroom.management.getmailroom.customfields');
            Route::get('mailroom-users', [App\Http\Controllers\Mailroom\MailroomUserController::class, 'index'])->name('mailroom.management.users.index');
            Route::post('mailroom-user-store', [App\Http\Controllers\Mailroom\MailroomUserController::class, 'userStore'])->name('mailroom.management.users.store');
            Route::get('getMailRoomRole', [App\Http\Controllers\Mailroom\MailroomUserController::class, 'getMailRoomRole']);
            Route::get('getMailRoomSite/{id}', [App\Http\Controllers\Mailroom\MailroomUserController::class, 'getMailRoomSite']);
            Route::post('mailroom-user-list', [App\Http\Controllers\Mailroom\MailroomUserController::class, 'mailroomUserList'])->name('mailroom.management.users.list');
            Route::get('mailroom-user-edit/{id}', [App\Http\Controllers\Mailroom\MailroomUserController::class, 'editMailroomUser'])->name('mailroom.management.users.edit');
            Route::post('mailroom-user-update/{id}', [App\Http\Controllers\Mailroom\MailroomUserController::class, 'updateMailroomUser'])->name('mailroom.management.users.update');
            Route::get('mailroom-user-delete/{id}', [App\Http\Controllers\Mailroom\MailroomUserController::class, 'deleteMailroomUser'])->name('mailroom.management.users.delete');
            Route::get('view-user/{id}', [App\Http\Controllers\Mailroom\MailroomUserController::class, 'viewUser']);
            Route::get('view-site/{id}', [App\Http\Controllers\Mailroom\MailroomUserController::class, 'viewSite']);

            // parcels
            Route::get('mailroom-parcel/{mainfilter?}', [App\Http\Controllers\Mailroom\ParcelController::class, 'index'])->name('mailroom.management.parcel.index');
            Route::get('get-user-details', [App\Http\Controllers\Mailroom\ParcelController::class, 'getUserDetails'])->name('mailroom.management.parcel.details');
            Route::post('mailroom-parcel-store', [App\Http\Controllers\Mailroom\ParcelController::class, 'parcelStore']);
            Route::post('get-parcel-list', [App\Http\Controllers\Mailroom\ParcelController::class, 'getParcelList'])->name('mailroom.management.parcel.list');
            Route::post('update-parcel-status', [App\Http\Controllers\Mailroom\ParcelController::class, 'updateParcelStatus']);
            Route::get('mailroom-parcel-edit/{id}', [App\Http\Controllers\Mailroom\ParcelController::class, 'editParcel']);
            Route::post('mailroom-parcel-update/{id}', [App\Http\Controllers\Mailroom\ParcelController::class, 'updateMailroomParcel']);
            Route::get('mailroom-parcel-delete/{id}', [App\Http\Controllers\Mailroom\ParcelController::class, 'deleteMailroomParcel']);
            Route::post('mailroom-parcel-history', [App\Http\Controllers\Mailroom\ParcelController::class, 'parcelHistory']);
            Route::post('mailroom-parcel-tracking-history', [App\Http\Controllers\Mailroom\ParcelController::class, 'parcelTrackingHistory']);
            Route::get('mailroom-parcel/pdf/{tagId}', [App\Http\Controllers\Mailroom\ParcelController::class, 'downloadPdf']);
            Route::get('getMailroomCustomFields', [App\Http\Controllers\Mailroom\StatusController::class, 'getMailroomCustomFields'])->name('mailroom.management.getmailroom.customfields');
            Route::post('mailroomCustomForm', [App\Http\Controllers\Mailroom\ParcelController::class, 'mailroomCustomForm'])->name('mailroom.management.mailroom.customform');
            Route::get('mailroom-parcel-info/{id}', [App\Http\Controllers\Mailroom\ParcelController::class, 'parcelInfo']);
            Route::get('printLabel', [App\Http\Controllers\Mailroom\ParcelController::class, 'printLabel']);
            Route::get('bulkPrintLabel', [App\Http\Controllers\Mailroom\ParcelController::class, 'bulkPrintLabel']);
            Route::post('parcel-bulk-actions', [App\Http\Controllers\Mailroom\ParcelController::class, 'parcelBulkAction']);
            Route::get('get-proof-document', [App\Http\Controllers\Mailroom\ParcelController::class, 'getProof']);
            Route::get('parcel-bulk-bach-pdf', [App\Http\Controllers\Mailroom\ParcelController::class, 'parcelBulkPachPDF']);
            Route::get('parcel-import', [App\Http\Controllers\Mailroom\ParcelController::class, 'parcelBulkImport']);
            Route::post('parcel-import', [App\Http\Controllers\Mailroom\ParcelController::class, 'bulkImportParcel']);
            Route::any('outgoing-parcel-import', [App\Http\Controllers\Mailroom\ParcelController::class, 'importOutgoingParcel']);
            Route::get('mailroom-dashboard', [App\Http\Controllers\Mailroom\DashboardController::class, 'dashboard']);
            Route::get('mailroom-dashboard-ajax', [App\Http\Controllers\Mailroom\DashboardController::class, 'ajaxdashboard']);
            Route::get('getReportData', [App\Http\Controllers\Mailroom\DashboardController::class, 'getReportData']);
            Route::get('getCourierProviderCharges', [App\Http\Controllers\Mailroom\ParcelController::class, 'getCourierProviderCharges']);
            Route::get('mailroom-parcel-report/{flow}', [App\Http\Controllers\Report\Mailroom\ParcelController::class, 'indexParcel']);
            Route::post('get-parcel-list-report/{flow}', [App\Http\Controllers\Report\Mailroom\ParcelController::class, 'getParcelReportList']);
            Route::post('get-parcel-list-report', [App\Http\Controllers\Report\Mailroom\ParcelController::class, 'getParcelReportList']);
            Route::get('download', [App\Http\Controllers\Report\Mailroom\ParcelController::class, 'getParcelReportDownload']);
            Route::get('mailroom-status-report', [App\Http\Controllers\Report\Mailroom\ParcelController::class, 'indexStatus']);
            Route::get('mailroom-vendor-report', [App\Http\Controllers\Report\Mailroom\ParcelController::class, 'indexVendor']);
            Route::post('mailroom-vendor-report', [App\Http\Controllers\Report\Mailroom\ParcelController::class, 'getVendorReportList']);
            Route::post('mailroom-vendor-report', [App\Http\Controllers\Report\Mailroom\ParcelController::class, 'getVendorReportList']);
            Route::get('reconciliation-report', [App\Http\Controllers\Mailroom\ReconciliationController::class, 'reconciliationReport']);
            Route::get('reconciliation-data', [App\Http\Controllers\Mailroom\ReconciliationController::class, 'reconciliationData']);
            Route::get('reconciliation-export', [App\Http\Controllers\Mailroom\ReconciliationController::class, 'exportReconciliation']);
            Route::post('mailroom-reconciliation-data', [App\Http\Controllers\Mailroom\ReconciliationController::class, 'mailroomReconciliationData']);
            Route::get('summary-report', [App\Http\Controllers\Report\Mailroom\ParcelController::class, 'summary_report']);
            Route::get('vendor-report-download', [App\Http\Controllers\Report\Mailroom\ParcelController::class, 'getVendorReportDownload']);
            Route::post('get-parcel-status-report', [App\Http\Controllers\Report\Mailroom\ParcelController::class, 'getStatusReportList']);
            Route::get('status-download', [App\Http\Controllers\Report\Mailroom\ParcelController::class, 'getStatusReportDownload']);
            Route::get('print-po/{id}', [App\Http\Controllers\Mailroom\ParcelController::class, 'printInvoice']);
            Route::post('mailroom-parcel-tracking-history', [App\Http\Controllers\Mailroom\ParcelController::class, 'parcelTrackingHistory']);
            Route::get('mailroom-parcel/pdf/{tagId}', [App\Http\Controllers\Mailroom\ParcelController::class, 'downloadPdf']);

            // Batch mailroom
            Route::get('batches', [App\Http\Controllers\Mailroom\BatchController::class, 'index']);
            Route::post('create-batch', [App\Http\Controllers\Mailroom\BatchController::class, 'createBatch']);
            Route::post('batchList', [App\Http\Controllers\Mailroom\BatchController::class, 'batchList']);
            Route::get('batch/pdf/{batch_id}', [App\Http\Controllers\Mailroom\BatchController::class, 'generatePdf'])->name('mailroom.batch.pdf');
            Route::get('edit-batch/{id}', [App\Http\Controllers\Mailroom\BatchController::class, 'editBatch'])->name('mailroom.management.batch.edit');
            Route::post('update-batch/{id}', [App\Http\Controllers\Mailroom\BatchController::class, 'updateBatch'])->name('mailroom.management.batch.update');
            Route::get('delete-batch/{id}', [App\Http\Controllers\Mailroom\BatchController::class, 'deleteBatch'])->name('mailroom.management.batch.delete');
            Route::get('getBatchList', [App\Http\Controllers\Mailroom\BatchController::class, 'getBatchList']);
            Route::get('get-remainitems', [App\Http\Controllers\Mailroom\BatchController::class, 'getRemainItems']);

            //Configure
            Route::get('mailroom_configuration', [App\Http\Controllers\Mailroom\ConfigureController::class, 'index']);
            Route::post('update-config-notification', [App\Http\Controllers\Mailroom\ConfigureController::class, 'updateConfigNotification']);
            Route::post('mailroom-delegate-list', [App\Http\Controllers\Mailroom\ConfigureController::class, 'mailroomDelegateUserList']);
            Route::post('mailroom-user-delegate', [App\Http\Controllers\Mailroom\ConfigureController::class, 'mailroomAddDelegateUser']);
            Route::get('mailroom-user-delegate-edit/{id}', [App\Http\Controllers\Mailroom\ConfigureController::class, 'mailroomEditDelegateUser']);
            Route::POST('mailroom-user-delegate-update', [App\Http\Controllers\Mailroom\ConfigureController::class, 'mailroomUpdateDelegateUser']);
            Route::get('mailroom-user-delegate-delete/{id}', [App\Http\Controllers\Mailroom\ConfigureController::class, 'mailroomDeleteDelegateUser']);
        });

        // Problem Management
        Route::get('problem_manager/{problem}', [App\Http\Controllers\ProblemManagement\ProblemManagementController::class, 'problemList']);
        Route::post('problem_management/ajaxlist/{listType}', [App\Http\Controllers\ProblemManagement\ProblemManagementController::class, 'ajaxprobleList']);
        Route::get('problem_manager/add_problem', [App\Http\Controllers\ProblemManagement\ProblemManagementController::class, 'problemAdd']);
        Route::post('problem_management/addproblem', [App\Http\Controllers\ProblemManagement\ProblemManagementController::class, 'ajaxInsert']);
        Route::get('problem_management/edit/{id}', [App\Http\Controllers\ProblemManagement\ProblemManagementController::class, 'editProblem']);
        Route::post('problem_management/update', [App\Http\Controllers\ProblemManagement\ProblemManagementController::class, 'updateProblem']);
        Route::get('problem_management/delete/{id}', [App\Http\Controllers\ProblemManagement\ProblemManagementController::class, 'deleteProblem']);
        Route::get('problem_management/filterproblem/{id}', [App\Http\Controllers\ProblemManagement\ProblemManagementController::class, 'filterProblem']);
        Route::get('problem_management/filterproblemdevice/{id}', [App\Http\Controllers\ProblemManagement\ProblemManagementController::class, 'filterProblemDevice']);
        Route::get('problem_management/export-problem', [App\Http\Controllers\ProblemManagement\ProblemManagementController::class, 'exportProblem']);
        Route::any('problem_management/view/{Id}', [App\Http\Controllers\ProblemManagement\ProblemManagementController::class, 'manageGroupProblem']);
        Route::any('jx-get-user-problem-management', [App\Http\Controllers\ProblemManagement\ProblemManagementController::class, 'getAjaxPrbolemManageDevice']);
        Route::any('jx-get-user-problem-management-ticket', [App\Http\Controllers\ProblemManagement\ProblemManagementController::class, 'getAjaxPrbolemManageTicket']);
        Route::post('problem-management/select-multiple-problem', [App\Http\Controllers\ProblemManagement\ProblemManagementController::class, 'solveProblem']);
        Route::post('problem-management/select-multiple-problem-unsolve', [App\Http\Controllers\ProblemManagement\ProblemManagementController::class, 'unsolveProblem']);
        Route::post('problem-management/select-multiple-problem-device', [App\Http\Controllers\ProblemManagement\ProblemManagementController::class, 'solveDeviceProblem']);
        Route::post('problem-management/select-multiple-problem-device-unsolve', [App\Http\Controllers\ProblemManagement\ProblemManagementController::class, 'unsolveDeviceProblem']);
        Route::any('problem-management/getTickets', [App\Http\Controllers\ProblemManagement\ProblemManagementController::class, 'ajaxTickets']);
        Route::any('problem-management/groups', [App\Http\Controllers\ProblemManagement\ProblemManagementController::class, 'groups']);
        Route::post('problem_management/ajaxGroupList', [App\Http\Controllers\ProblemManagement\ProblemManagementController::class, 'ajaxGroupList']);
        Route::post('problem_management/addproblemGroup', [App\Http\Controllers\ProblemManagement\ProblemManagementController::class, 'ajaxAddGroup']);
        Route::post('problem_management/searchFilteredDevice', [App\Http\Controllers\ProblemManagement\ProblemManagementController::class, 'searchFilteredDevice']);
        Route::post('problem-management/pm_history', [App\Http\Controllers\ProblemManagement\ProblemManagementController::class, 'pmHistory']);
        Route::post('problem-management/add_impacted_ticket', [App\Http\Controllers\ProblemManagement\ProblemManagementController::class, 'submitImapactTicket']);
        Route::get('problem-management/remove_impacted_ticket', [App\Http\Controllers\ProblemManagement\ProblemManagementController::class, 'removeImapactTicket']);
        Route::get('jx-get-problem-mgt-select2', [App\Http\Controllers\ProblemManagement\ProblemManagementController::class, 'getProblemListSelect2'])->name('getProblemListSelect2');

        // status_board access only
        // Route::group(['middleware' => 'auth.role:status_board'], function() {
        Route::get('itam-status-board', [App\Http\Controllers\StatusBoardController::class, 'publicStatusboard']);    // public route

        Route::get('status-board-items', [App\Http\Controllers\StatusBoardController::class, 'getItem']);
        Route::post('jx-get-items', [App\Http\Controllers\StatusBoardController::class, 'ajaxItems']);
        Route::get('status-board-item-new', [App\Http\Controllers\StatusBoardController::class, 'getNewItem']);
        Route::post('status-board-item-new', [App\Http\Controllers\StatusBoardController::class, 'postNewItem']);
        Route::get('status-board-item-edit/{itemId}', [App\Http\Controllers\StatusBoardController::class, 'getEditItem']);
        Route::post('status-board-item-edit/{itemId}', [App\Http\Controllers\StatusBoardController::class, 'postEditItem']);
        Route::get('status-board-item-delete/{itemId}', [App\Http\Controllers\StatusBoardController::class, 'deleteItem']);

        Route::get('status-board-groups', [App\Http\Controllers\StatusBoardController::class, 'getGroups']);
        Route::post('jx-get-groups', [App\Http\Controllers\StatusBoardController::class, 'ajaxGroup']);
        Route::get('status-board-groups-new', [App\Http\Controllers\StatusBoardController::class, 'getNewGroup']);
        Route::post('status-board-groups-new', [App\Http\Controllers\StatusBoardController::class, 'postNewGroup']);
        Route::get('status-board-groups-edit/{groupId}', [App\Http\Controllers\StatusBoardController::class, 'getEditGroup']);
        Route::post('status-board-groups-edit/{groupId}', [App\Http\Controllers\StatusBoardController::class, 'postEditGroup']);
        Route::get('status-board-groups-delete/{groupId}', [App\Http\Controllers\StatusBoardController::class, 'deleteGroup']);

        Route::get('/incidents', ['as' => 'status-board-incidents', 'uses' => [App\Http\Controllers\StatusBoardController::class, 'getIncidents']]);

        Route::get('status-board-open-incidents', [App\Http\Controllers\StatusBoardController::class, 'getOpenIncidents']);
        Route::get('status-board-fixed-incidents', [App\Http\Controllers\StatusBoardController::class, 'getFixedIncidents']);
        Route::get('status-board-archived-incidents',  [App\Http\Controllers\StatusBoardController::class, 'getArchivedIncidents']);
        Route::get('status-board-incident-history/{Id}', [App\Http\Controllers\StatusBoardController::class, 'getIncidentHistory']);

        Route::get('status-board-incident-new', [App\Http\Controllers\StatusBoardController::class, 'getNewIncident']);
        Route::post('status-board-incident-new', [App\Http\Controllers\StatusBoardController::class, 'postNewIncident']);
        Route::get('status-board-incident-edit/{Id}', [App\Http\Controllers\StatusBoardController::class, 'getEditIncident']);
        Route::post('status-board-incident-edit/{Id}', [App\Http\Controllers\StatusBoardController::class, 'postEditIncident']);
        Route::get('status-board-incident-update/{Id}', [App\Http\Controllers\StatusBoardController::class, 'getUpdateIncident']);
        Route::post('status-board-incident-update/{Id}', [App\Http\Controllers\StatusBoardController::class, 'postUpdateIncident']);
        Route::get('status-board-get-available-item-status/{item_id}', [App\Http\Controllers\StatusBoardController::class, 'getAvailableItemStatus']);
        // });

        Route::get('status-board', [App\Http\Controllers\StatusBoardController::class, 'getIndex'])->middleware('client.route_restrict');

        // reports access only
        Route::group(['prefix' => 'reports'], function () {
            // Blocked IP/MAC device Report
            Route::get('blockIp',  [App\Http\Controllers\Report\BlockedIpController::class, 'getIndex']);
            Route::post('jx-blockIp', [App\Http\Controllers\Report\BlockedIpController::class, 'ajaxIndex']);
            Route::get('export-blockIp', [App\Http\Controllers\Report\BlockedIpController::class, 'downloadReport']);

            Route::get('tickets/email-triggers', [App\Http\Controllers\Report\EmailTriggerController::class, 'index']);
            Route::post('tickets/getEmailReportTriggers', [App\Http\Controllers\Report\EmailTriggerController::class, 'getEmailReportTriggers'])->name('getEmailReportTriggers');
            Route::post('tickets/addEmailReportTriggers', [App\Http\Controllers\Report\EmailTriggerController::class, 'addEmailReportTriggers'])->name('addEmailReportTriggers');
            Route::get('tickets/deleteEmailReportTriggers/{id?}', [App\Http\Controllers\Report\EmailTriggerController::class, 'deleteEmailReportTriggers'])->name('deleteEmailReportTriggers');
            Route::get('tickets/getEmailTrigger/{id?}', [App\Http\Controllers\Report\EmailTriggerController::class, 'getEmailTrigger'])->name('getEmailTrigger');
            Route::post('tickets/editEmailTrigger/{id?}', [App\Http\Controllers\Report\EmailTriggerController::class, 'editEmailTrigger'])->name('editEmailTrigger');
            Route::get('tickets/emailReportTriggerData/{id}', [App\Http\Controllers\Report\EmailTriggerController::class, 'emailReportTriggerData']);

            // User Assigned Assets Count
            Route::get('usersAsset', [App\Http\Controllers\Report\UserController::class, 'getUsersAsset']);
            Route::post('jx-asset-wise-report', [App\Http\Controllers\Report\UserController::class, 'ajaxUsersAsset']);
            Route::get('export-users-asset', [App\Http\Controllers\Report\UserController::class, 'downloadReport']);

            Route::get('assetsummary', [App\Http\Controllers\Report\DeviceController::class, 'getAssetSummaryReport']);
            Route::post('jx-assetsummary', [App\Http\Controllers\Report\DeviceController::class, 'ajaxAssetSammary']);
            Route::get('dispose-asset', [App\Http\Controllers\Report\DeviceController::class, 'getDisposeAssetReport']);
            Route::post('jx-dispose-asset', [App\Http\Controllers\Report\DeviceController::class, 'ajaxDisposeAssetList']);
            Route::get('export-dispose-asset', [App\Http\Controllers\Report\DeviceController::class, 'downloadDisposeAsset']);

            // NI Error Logs report
            Route::get('ni-error-logs', [App\Http\Controllers\Report\NiErrorLogsController::class, 'getIndex']);
            Route::post('jx-ni-error-logs', [App\Http\Controllers\Report\NiErrorLogsController::class, 'ajaxIndex']);
            Route::get('export-ni-error-logs', [App\Http\Controllers\Report\NiErrorLogsController::class, 'downloadReport']);

            Route::get('devices', [App\Http\Controllers\Report\DeviceController::class, 'getIndex']);
            Route::post('jx-devices', [App\Http\Controllers\Report\DeviceController::class, 'ajaxIndex']);
            Route::get('jx-devices', [App\Http\Controllers\Report\DeviceController::class, 'downloadDeviceReport']);

            Route::get('device-maintenance', [App\Http\Controllers\Report\DeviceController::class, 'getMaintenance']);
            Route::post('jx-device-maintenance', [App\Http\Controllers\Report\DeviceController::class, 'ajaxMaintenance']);
            Route::get('jx-device-maintenance-export', [App\Http\Controllers\Report\DeviceController::class, 'downloadDeviceMaintainanceReport']);

            Route::get('accessories', [App\Http\Controllers\Report\AccessoryController::class, 'getIndex']);
            Route::post('jx-accessories', [App\Http\Controllers\Report\AccessoryController::class, 'ajaxIndex']);
            Route::get('jx-accessories', [App\Http\Controllers\Report\AccessoryController::class, 'downloadReport']);

            Route::get('assigned-accessories', [App\Http\Controllers\Report\AccessoryController::class, 'getassignedAccessories']);
            Route::post('jx-assigned-accessories', [App\Http\Controllers\Report\AccessoryController::class, 'ajaxAssignedAccessories']);
            Route::get('export-assigned-accessories', [App\Http\Controllers\Report\AccessoryController::class, 'downloadAssignedReport']);
            Route::get('export-assigned-wise-details-pdf', [App\Http\Controllers\Report\AccessoryController::class, 'downloadAssignedAccessoryReportPDF']);

            Route::get('assigned-license', [App\Http\Controllers\Report\LicenseController::class, 'getassignedLicense']);
            Route::post('jx-assigned-license', [App\Http\Controllers\Report\LicenseController::class, 'ajaxAssignedLicense']);
            Route::get('export-assigned-license', [App\Http\Controllers\Report\LicenseController::class, 'downloadAssignedlicenseReport']);

            Route::get('ni-unique-license', [App\Http\Controllers\Report\LicenseController::class, 'getNIUniqueLicense']);
            Route::post('jx-ni-unique-license', [App\Http\Controllers\Report\LicenseController::class, 'ajaxNIUniqueLicense']);
            Route::get('export-ni-unique-license', [App\Http\Controllers\Report\LicenseController::class, 'downloadNIUniqueLicenseReport']);
            
            
            Route::get('licenses', [App\Http\Controllers\Report\LicenseController::class, 'getIndex']);
            Route::post('jx-licenses', [App\Http\Controllers\Report\LicenseController::class, 'ajaxIndex']);
            Route::get('export-licenses-details', [App\Http\Controllers\Report\LicenseController::class, 'downloadReport']);

            Route::get('consumables', [App\Http\Controllers\Report\ConsumableController::class, 'getIndex']);
            Route::post('jx-consumables', [App\Http\Controllers\Report\ConsumableController::class, 'ajaxIndex']);
            Route::get('export-consumables-details', [App\Http\Controllers\Report\ConsumableController::class, 'downloadReport']);
            Route::get('export-consumable-wise-details-pdf', [App\Http\Controllers\Report\ConsumableController::class, 'downloadReportPDF']);

            Route::get('components', [App\Http\Controllers\Report\ComponentController::class, 'getIndex']);
            Route::post('jx-components', [App\Http\Controllers\Report\ComponentController::class, 'ajaxIndex']);
            Route::get('export-components-details', [App\Http\Controllers\Report\ComponentController::class, 'downloadReport']);

            Route::get('depreciations', [App\Http\Controllers\Report\DeviceController::class, 'getDepreciations']);
            Route::post('jx-depreciations', [App\Http\Controllers\Report\DeviceController::class, 'ajaxDepreciations']);
            Route::get('jx-depreciations-download', [App\Http\Controllers\Report\DeviceController::class, 'downloadDeprReport']);

            Route::get('activity', [App\Http\Controllers\Report\ActivityController::class, 'getActivity']);
            Route::post('jx-activities', [App\Http\Controllers\Report\ActivityController::class, 'ajaxActivities']);
            Route::get('export-activity-details', [App\Http\Controllers\Report\ActivityController::class, 'downloadReport']);

            Route::get('consumable-activity-report', [App\Http\Controllers\Report\ConsumableController::class, 'getConsumableActivity']);
            Route::post('jx-consumable-activities', [App\Http\Controllers\Report\ConsumableController::class, 'ajaxConsumableActivities']);
            Route::get('export-consumable-activity-details', [App\Http\Controllers\Report\ConsumableController::class, 'downloadConsumableActivityReport']);

            Route::get('status-wise-reports', [App\Http\Controllers\Report\DeviceController::class, 'getStatusWise']);
            Route::post('jx-status-wise-devices', [App\Http\Controllers\Report\DeviceController::class, 'ajaxStatusWise']);
            Route::get('export-status-wise-devices', [App\Http\Controllers\Report\DeviceController::class, 'downloadStatusWise']);

            Route::get('location-wise-purchase', [App\Http\Controllers\Report\LocationController::class, 'getLocationWisePurchase']);
            Route::post('jx-location-wise-purchase', [App\Http\Controllers\Report\LocationController::class, 'ajaxLocationWise']);
            Route::get('export-location-wise-purchase', [App\Http\Controllers\Report\LocationController::class, 'downloadLocationWise']);

            Route::get('asset-purchase', [App\Http\Controllers\Report\PurchaseController::class, 'getAssetWisePurchase']);
            Route::post('asset-purchase-list', [App\Http\Controllers\Report\PurchaseController::class, 'ajaxAssetWisePurchase'])->name('asset.purchase.list');
            Route::get('asset-purchase-download', [App\Http\Controllers\Report\PurchaseController::class, 'exportAssetWisePurchase']);

            Route::get('status-wise-stock-report', [App\Http\Controllers\Report\DeviceController::class, 'getStatusWiseStocks']);
            Route::post('jx-status-wise-stocks', [App\Http\Controllers\Report\DeviceController::class, 'ajaxStatusWiseStocks']);
            Route::get('export-status-wise-stocks', [App\Http\Controllers\Report\DeviceController::class, 'downloadStatusWiseStocks']);

            Route::get('stock-devices-report', [App\Http\Controllers\Report\DeviceController::class, 'getStockedDevices']);
            Route::post('jx-stock-wise-devices', [App\Http\Controllers\Report\DeviceController::class, 'ajaxStockedDevices']);
            Route::get('export-stock-wise-devices', [App\Http\Controllers\Report\DeviceController::class, 'downloadStockedDevices']);

            Route::get('scrap-accessories', [App\Http\Controllers\Report\AccessoryController::class, 'getScrapWise']);
            Route::post('jx-scrap-wise-accessories', [App\Http\Controllers\Report\AccessoryController::class, 'ajaxScrapWise']);
            Route::get('export-scrap-wise-accessories', [App\Http\Controllers\Report\AccessoryController::class, 'downloadScrapWise']);

            Route::get('contract-report', [App\Http\Controllers\Report\ContractController::class, 'getIndex']);
            Route::post('jx-contract-wise-report', [App\Http\Controllers\Report\ContractController::class, 'ajaxContractList']);
            Route::get('export-contract-report', [App\Http\Controllers\Report\ContractController::class, 'downloadContractReport']);

            Route::get('audit-report', [App\Http\Controllers\Report\AuditController::class, 'getIndex']);
            Route::post('jx-audit-wise-report', [App\Http\Controllers\Report\AuditController::class, 'ajaxAuditList']);
            Route::get('export-audit-report', [App\Http\Controllers\Report\AuditController::class, 'downloadAuditReport']);

            Route::get('accessory', [App\Http\Controllers\Report\AccessoryController::class, 'getAccessoryIndex']);
            Route::post('jx-accessory', [App\Http\Controllers\Report\AccessoryController::class, 'ajaxAccessoryIndex']);
            Route::get('export-accessory-details', [App\Http\Controllers\Report\AccessoryController::class, 'downloadAccessorytotalReport']);
            Route::get('export-accessory-details-pdf', [App\Http\Controllers\Report\AccessoryController::class, 'downloadAccessorytotalReportPDF']);

            Route::get('accessory-location-wise', [App\Http\Controllers\Report\AccessoryController::class, 'getAccessoryLoctionWise']);
            Route::post('jx-accessory-location-wise', [App\Http\Controllers\Report\AccessoryController::class, 'ajaxAccessoryLoctionWise']);
            Route::get('export-accessory-location-wise-details', [App\Http\Controllers\Report\AccessoryController::class, 'downloadAccessoryLoctionWise']);
            Route::get('export-accessory-location-wise-details-pdf', [App\Http\Controllers\Report\AccessoryController::class, 'downloadAccessoryLoctionWisePDF']);

            Route::get('consumable-location-wise', [App\Http\Controllers\Report\ConsumableController::class, 'getConsumableLocationWise']);
            Route::post('jx-consumable-location-wise', [App\Http\Controllers\Report\ConsumableController::class, 'ajaxConsumableLocationWise']);
            Route::get('export-consumable-location-wise-details', [App\Http\Controllers\Report\ConsumableController::class, 'downloadConsumableLocationWise']);
            Route::get('export-consumable-location-wise-details-pdf', [App\Http\Controllers\Report\ConsumableController::class, 'downloadConsumableLocationWisePDF']);

            Route::get('accessory-report', [App\Http\Controllers\Report\AccessoryController::class, 'getAccessoryReport']);
            Route::post('jx-accessory-report', [App\Http\Controllers\Report\AccessoryController::class, 'ajaxAccessoryReport']);
            Route::get('export-accessory-report', [App\Http\Controllers\Report\AccessoryController::class, 'downloadAccessoryReport']);
            Route::get('export-accessory-report-pdf', [App\Http\Controllers\Report\AccessoryController::class, 'downloadAccessoryReportPDF']);

            Route::get('accessory-checkout-report', [App\Http\Controllers\Report\AccessoryController::class, 'getAccessoryCheckout']);
            Route::post('jx-accessory-checkout-report', [App\Http\Controllers\Report\AccessoryController::class, 'ajaxAccessoryCheckout']);
            Route::get('export-accessory-checkout-report', [App\Http\Controllers\Report\AccessoryController::class, 'downloadAccessoryCheckout']);

            Route::get('consumable-report', [App\Http\Controllers\Report\ConsumableController::class, 'getConsumableReport']);
            Route::post('jx-consumable-report', [App\Http\Controllers\Report\ConsumableController::class, 'ajaxConsumableReport']);
            Route::get('export-consumable-report', [App\Http\Controllers\Report\ConsumableController::class, 'downloadConsumableReport']);

            Route::get('consumable-checkout-report', [App\Http\Controllers\Report\ConsumableController::class, 'getConsumableCheckoutReport']);
            Route::post('jx-consumable-checkout-report', [App\Http\Controllers\Report\ConsumableController::class, 'ajaxConsumableCheckoutReport']);
            Route::get('export-consumable-checkout-report', [App\Http\Controllers\Report\ConsumableController::class, 'downloadConsumableCheckoutReport']);

            Route::get('deleted-devices', [App\Http\Controllers\Report\DeviceController::class, 'getDeletedDevices']);
            Route::post('jx-deleted-devices', [App\Http\Controllers\Report\DeviceController::class, 'ajaxDeletedDevices']);
            Route::get('export-deleted-devices', [App\Http\Controllers\Report\DeviceController::class, 'downloadDeletedDevices']);
            Route::get('export-deleted-devices-pdf', [App\Http\Controllers\Report\DeviceController::class, 'downloadDeletedDevicesPDF']);

            Route::get('devices-with-ms-version', [App\Http\Controllers\Report\DeviceController::class, 'getDevicesMsVersion']);
            Route::post('jx-devices-with-ms-version', [App\Http\Controllers\Report\DeviceController::class, 'ajaxDevicesMsVersion']);
            Route::get('export-devices-with-ms-version', [App\Http\Controllers\Report\DeviceController::class, 'downloadDevicesMsVersion']);
            Route::get('export-devices-with-ms-version-pdf', [App\Http\Controllers\Report\DeviceController::class, 'downloadDevicesMsVersionPDF']);

            Route::get('device-aging-report', [App\Http\Controllers\Report\DeviceController::class, 'getDeviceAgingReport']);
            Route::post('jx-device-aging-report', [App\Http\Controllers\Report\DeviceController::class, 'ajaxDeviceAgingReport']);
            Route::get('export-device-aging-report', [App\Http\Controllers\Report\DeviceController::class, 'downloadDeviceAgingReport']);
            // Device with MSOffice

            Route::get('device-with-msoffice', [App\Http\Controllers\Report\NetWorkInventoryController::class, 'getNIDevicesMsVersion']);
            Route::post('jx-ni-devices-with-ms-version', [App\Http\Controllers\Report\NetWorkInventoryController::class, 'ajaxNIDevicesMsVersion']);
            Route::get('export-ni-devices-with-ms-version', [App\Http\Controllers\Report\NetWorkInventoryController::class, 'downloadNIDevicesMsVersion']);
            Route::get('export-ni-devices-with-ms-version-pdf', [App\Http\Controllers\Report\NetWorkInventoryController::class, 'downloadNIDevicesMsVersionPDF']);

            Route::get('license-usage-time', [App\Http\Controllers\Report\NetWorkInventoryController::class, 'licenseWiseDeviceUseTime']);
            Route::post('ajax-license-usage-time', [App\Http\Controllers\Report\NetWorkInventoryController::class, 'ajaxLicenseWiseDeviceUseTime']);
            Route::get('export-license-usage-time', [App\Http\Controllers\Report\NetWorkInventoryController::class, 'exportLicenseWiseDeviceUseTime']);

            Route::get('deleted-accessories', [App\Http\Controllers\Report\AccessoryController::class, 'getDeletedAccessories']);
            Route::post('jx-deleted-accessories', [App\Http\Controllers\Report\AccessoryController::class, 'ajaxDeletedAccessories']);
            Route::get('export-deleted-accessories', [App\Http\Controllers\Report\AccessoryController::class, 'downloadDeletedAccessories']);
            Route::get('export-deleted-accessories-pdf', [App\Http\Controllers\Report\AccessoryController::class, 'downloadDeletedAccessoriesPDF']);

            Route::get('deleted-consumables', [App\Http\Controllers\Report\ConsumableController::class, 'getDeletedConsumables']);
            Route::post('jx-deleted-consumables', [App\Http\Controllers\Report\ConsumableController::class, 'ajaxDeletedConsumables']);
            Route::get('export-deleted-consumables', [App\Http\Controllers\Report\ConsumableController::class, 'downloadDeletedConsumables']);
            Route::get('export-deleted-consumables-pdf', [App\Http\Controllers\Report\ConsumableController::class, 'downloadDeletedConsumablesPDF']);

            Route::get('deleted-licenses', [App\Http\Controllers\Report\LicenseController::class, 'getDeletedLicense']);
            Route::post('jx-deleted-licenses', [App\Http\Controllers\Report\LicenseController::class, 'ajaxDeletedLicense']);
            Route::get('export-deleted-licenses', [App\Http\Controllers\Report\LicenseController::class, 'downloadDeletedLicense']);
            Route::get('export-deleted-licenses-pdf', [App\Http\Controllers\Report\LicenseController::class, 'downloadDeletedLicensePDF']);

            Route::get('ni-asset-not-detected', [App\Http\Controllers\Report\NetWorkInventoryController::class, 'getNIAssetNotDetected']);
            Route::post('jx-ni-asset-not-detected', [App\Http\Controllers\Report\NetWorkInventoryController::class, 'ajaxNIAssetNotDetected']);
            Route::get('export-ni-asset-not-connected', [App\Http\Controllers\Report\NetWorkInventoryController::class, 'downloadNIAssetNotDetectedExport']);
            Route::get('export-ni-asset-not-connected-pdf', [App\Http\Controllers\Report\NetWorkInventoryController::class, 'downloadNIAssetNotDetectedExportPDF']);

            Route::get('not-agent-in-ni-asset', [App\Http\Controllers\Report\NetWorkInventoryController::class, 'getNIAssetInNotAgent']);
            Route::post('jx-not-agent-in-ni-asset', [App\Http\Controllers\Report\NetWorkInventoryController::class, 'ajaxNIAssetInNotAgent']);
            Route::get('export-not-agent-in-ni-asset', [App\Http\Controllers\Report\NetWorkInventoryController::class, 'downloadNIAssetInNotAgentExport']);
            Route::get('export-not-agent-in-ni-asset-pdf', [App\Http\Controllers\Report\NetWorkInventoryController::class, 'downloadNIAssetInNotAgentPDF']);

            Route::get('asset-rfid', [App\Http\Controllers\Report\DeviceController::class, 'getAssetRfid']);
            Route::post('jx-asset-rfid', [App\Http\Controllers\Report\DeviceController::class, 'ajaxAssetRfid']);
            Route::get('export-asset-rfid', [App\Http\Controllers\Report\DeviceController::class, 'downloadAssetRfid']);

            // Route::get('inactive-user-assets', [App\Http\Controllers\Report\UserController::class, 'inactiveUserAssets']);
            // Route::post('jx-inactive-user-assets', [App\Http\Controllers\Report\UserController::class, 'ajaxInactiveUserAssets']);
            // Route::get('export-inactive-user-assets', [App\Http\Controllers\Report\UserController::class, 'downloadInactiveUserAssets']);

            Route::get('media-type', [App\Http\Controllers\Report\NetWorkInventoryController::class, 'getMediaType']);
            Route::post('jx-media-type', [App\Http\Controllers\Report\NetWorkInventoryController::class, 'ajaxMediaType']);
            Route::get('export-media-type-details', [App\Http\Controllers\Report\NetWorkInventoryController::class, 'downloadMediaTypeReport']);

            /* reports for custom report */
            Route::group(['prefix' => 'custom-reports'], function () {
                Route::get('/', [App\Http\Controllers\Report\Custom\ReportController::class, 'getIndex']);
                Route::post('get-reports', [App\Http\Controllers\Report\Custom\ReportController::class, 'ajaxIndex']);
                Route::post('add-report', [App\Http\Controllers\Report\Custom\ReportController::class, 'ajaxAdd']);
                Route::get('edit-report', [App\Http\Controllers\Report\Custom\ReportController::class, 'ajaxedit']);
                Route::get('delete-report/{id}', [App\Http\Controllers\Report\Custom\ReportController::class, 'ajaxDeleteReport']);
                Route::post('update-name/{id}', [App\Http\Controllers\Report\Custom\ReportController::class, 'ajaxUpdateName']);
                Route::get('get-fields/{id}', [App\Http\Controllers\Report\Custom\ReportController::class, 'ajaxGetFields']);
                Route::post('update-fields/{id}', [App\Http\Controllers\Report\Custom\ReportController::class, 'ajaxUpdateFields']);
                Route::get('get-criteria/{id}', [App\Http\Controllers\Report\Custom\ReportController::class, 'ajaxGetCriteria']);
                Route::get('get-rule/{id}', [App\Http\Controllers\Report\Custom\ReportController::class, 'ajaxGetRule']);
                Route::post('get-rules', [App\Http\Controllers\Report\Custom\ReportController::class, 'ajaxRules']);
                Route::post('update-rule/{id?}', [App\Http\Controllers\Report\Custom\ReportController::class, 'ajaxUpdateRule']);
                Route::get('delete-rule/{id?}', [App\Http\Controllers\Report\Custom\ReportController::class, 'ajaxDeleteRule']);
                Route::get('view/{id}', [App\Http\Controllers\Report\Custom\ReportController::class, 'viewReport']);
                Route::post('get-report-data', [App\Http\Controllers\Report\Custom\ReportController::class, 'ajaxReportData']);
                Route::post('export-report-data', [App\Http\Controllers\Report\Custom\ReportController::class, 'exportReportData']);
                Route::post('jx-download-custom-report', [App\Http\Controllers\Report\Custom\ReportController::class, 'ajaxDownloadCustomReport']);
                Route::post('update-dictation/{id}', [App\Http\Controllers\Report\Custom\ReportController::class, 'ajaxUpdateDictation']);
                Route::get('view-name', [App\Http\Controllers\Report\Custom\ReportController::class, 'viewReportName']);

                /* options of dropdowns */
                Route::get('get-models', [App\Http\Controllers\Report\Custom\ReportController::class, 'getModels']);
                Route::get('get-manufacturers', [App\Http\Controllers\Report\Custom\ReportController::class, 'getManufacturers']);
                Route::get('get-locations', [App\Http\Controllers\Report\Custom\ReportController::class, 'getLocations']);
                Route::get('get-places', [App\Http\Controllers\Report\Custom\ReportController::class, 'getPlaces']);
                Route::get('get-status-labels', [App\Http\Controllers\Report\Custom\ReportController::class, 'getStatusLabels']);
                Route::get('get-purchase-references', [App\Http\Controllers\Report\Custom\ReportController::class, 'getPurchaseReferences']);
                Route::get('get-categories', [App\Http\Controllers\Report\Custom\ReportController::class, 'getCategories']);
                Route::get('get-account-types', [App\Http\Controllers\Report\Custom\ReportController::class, 'getAccountTypes']);
                Route::get('get-suppliers', [App\Http\Controllers\Report\Custom\ReportController::class, 'getSuppliers']);
                Route::get('get-departments', [App\Http\Controllers\Report\Custom\ReportController::class, 'getDepartments']);
                Route::get('get-users', [App\Http\Controllers\Report\Custom\ReportController::class, 'getUsers']);
                Route::get('get-lease-types', [App\Http\Controllers\Report\Custom\ReportController::class, 'getLeaseTypes']);
                Route::get('get-maintenance-incharge', [App\Http\Controllers\Report\Custom\ReportController::class, 'getMaintenanceIncharge']);
                Route::get('get-status-list', [App\Http\Controllers\Report\Custom\ReportController::class, 'getStatusList']);
                Route::get('get-priority', [App\Http\Controllers\Report\Custom\ReportController::class, 'getPriority']);
                Route::get('get-problemcategory', [App\Http\Controllers\Report\Custom\ReportController::class, 'getProblemCategory']);
                Route::get('get-subcategory', [App\Http\Controllers\Report\Custom\ReportController::class, 'getSubCategory']);
                Route::get('getUser', [App\Http\Controllers\Report\Custom\ReportController::class, 'getUser']);
                Route::get('getDevice', [App\Http\Controllers\Report\Custom\ReportController::class, 'getDevice']);
                Route::get('getTags', [App\Http\Controllers\Report\Custom\ReportController::class, 'getTags']);
                Route::get('getTicketType', [App\Http\Controllers\Report\Custom\ReportController::class, 'getTicketType']);
            });

            Route::get('depreciation-report', [App\Http\Controllers\Report\DepreciationController::class, 'getDepreciationReport']);
            Route::post('jx-depreciations-report', [App\Http\Controllers\Report\DepreciationController::class, 'ajaxDepreciationDevices']);
            Route::get('jx-depreciations', [App\Http\Controllers\Report\DepreciationController::class, 'downloadDeprReport']);

            Route::get('blacklisted/{id}', [App\Http\Controllers\Report\BlacklistedSoftwareController::class, 'getIndex']);
            Route::post('jx-blacklist', [App\Http\Controllers\Report\BlacklistedSoftwareController::class, 'ajaxBlacklist']);
            // Route::get('export-blacklist-details', [App\Http\Controllers\Report\BlacklistedSoftwareController::class, 'downloadReport']);
            Route::get('export-blacklist-details/{id}', [App\Http\Controllers\Report\BlacklistedSoftwareController::class, 'downloadReport']);

            /* reports for procurement module */
            Route::group(['prefix' => 'procurements'], function () {
                Route::get('procurement_report', [App\Http\Controllers\Report\Procurement\BudgetCategoryController::class, 'getProcurement']);
                Route::post('jx-procurement-list', [App\Http\Controllers\Report\Procurement\BudgetCategoryController::class, 'ajaxProcurementReport']);
                Route::get('export-procurement', [App\Http\Controllers\Report\Procurement\BudgetCategoryController::class, 'downloadProcurementReport']);
                Route::get('budget_categories/monthly', [App\Http\Controllers\Report\Procurement\BudgetCategoryController::class, 'getBudgets']);
                Route::get('procurement_item_report', [App\Http\Controllers\Report\Procurement\BudgetCategoryController::class, 'getProcurementItam']);
                Route::post('jx-procurement-item-list', [App\Http\Controllers\Report\Procurement\BudgetCategoryController::class, 'ajaxProcurementItam']);
                Route::get('export-procurement-item', [App\Http\Controllers\Report\Procurement\BudgetCategoryController::class, 'downloadProcurementItemReport']);
            });

            // Route::get('financial', [App\Http\Controllers\Report\ActivityController::class, 'financial']);
            // Route::post('jx-financial', [App\Http\Controllers\Report\ActivityController::class, 'ajaxActivities']);
            // Route::get('jx-financial', [App\Http\Controllers\Report\ActivityController::class, 'downloadReport']);

            // Route::get('budgetwise', [App\Http\Controllers\Report\ActivityController::class, 'budgetwise']);
            // Route::post('jx-budgetwise', [App\Http\Controllers\Report\ActivityController::class, 'ajaxActivities']);
            // Route::get('jx-budgetwise', [App\Http\Controllers\Report\ActivityController::class, 'downloadReport']);

            Route::get('unaccepted-asset', [App\Http\Controllers\Report\ActivityController::class, 'getUnacceptedAsset']);
            Route::post('jx-unaccepted-asset', [App\Http\Controllers\Report\ActivityController::class, 'ajaxUnacceptedAsset']);
            Route::get('jx-unaccepted-asset', [App\Http\Controllers\Report\ActivityController::class, 'downloadUnacceptedAsset']);

            Route::get('account-type', [App\Http\Controllers\Report\DeviceController::class, 'getAccountType']);
            Route::post('jx-account-type', [App\Http\Controllers\Report\DeviceController::class, 'ajaxAccountTypeAsset']);
            Route::get('export-account-type', [App\Http\Controllers\Report\DeviceController::class, 'downloadAccountTypeAssetReport']);

            Route::get('categories', [App\Http\Controllers\Report\CategoryController::class, 'index']);
            Route::post('jx-category-wise-devices', [App\Http\Controllers\Report\CategoryController::class, 'ajaxDevice']);
            Route::get('export-category-report', [App\Http\Controllers\Report\CategoryController::class, 'downloadCategoryReport']);

            Route::post('jx-category-wise-consumables', [App\Http\Controllers\Report\CategoryController::class, 'ajaxConsumable']);
            Route::post('jx-category-wise-accessories', [App\Http\Controllers\Report\CategoryController::class, 'ajaxAccessories']);

            Route::get('device-category-wise', [App\Http\Controllers\Report\CategoryController::class, 'getIndex']);
            Route::post('jx-device-category-wise', [App\Http\Controllers\Report\CategoryController::class, 'ajaxDeviceList']);
            Route::get('export-category-devices', [App\Http\Controllers\Report\CategoryController::class, 'downloadDeviceReport']);

            Route::get('user-category-wise', [App\Http\Controllers\Report\CategoryController::class, 'getUser']);
            Route::post('jx-user-category-wise', [App\Http\Controllers\Report\CategoryController::class, 'ajaxUserList']);
            Route::get('export-category-users', [App\Http\Controllers\Report\CategoryController::class, 'downloadCatUserReport']);

            Route::get('accessory-category-wise', [App\Http\Controllers\Report\AccessoryController::class, 'getAccessory']);
            Route::post('jx-accessory-category-wise', [App\Http\Controllers\Report\AccessoryController::class, 'ajaxAccessoryList']);
            Route::get('export-category-Accessories', [App\Http\Controllers\Report\AccessoryController::class, 'downloadCategoryWiseAccessory']);

            Route::get('consumable-category-wise', [App\Http\Controllers\Report\ConsumableController::class, 'getConsumable']);
            Route::post('jx-consumable-category-wise', [App\Http\Controllers\Report\ConsumableController::class, 'ajaxConsumableList']);
            Route::get('export-category-consumables', [App\Http\Controllers\Report\ConsumableController::class, 'downloadCategoryWiseConsumables']);

            Route::get('manufacturers', [App\Http\Controllers\Report\ManufacturerController::class, 'index']);
            Route::post('jx-manufacturer-wise-devices', [App\Http\Controllers\Report\ManufacturerController::class, 'ajaxDevice']);
            Route::post('jx-manufacturer-wise-consumables', [App\Http\Controllers\Report\ManufacturerController::class, 'ajaxConsumable']);

            Route::get('licenses/seat-wise', [App\Http\Controllers\Report\LicenseController::class, 'Index']);
            Route::post('licenses/jx-seat-wise', [App\Http\Controllers\Report\LicenseController::class, 'ajaxSeatWiseList']);
            Route::get('licenses/export-seat-wise', [App\Http\Controllers\Report\LicenseController::class, 'downloadSeatWiseReport']);

            Route::get('licenses/manufacturer-wise', [App\Http\Controllers\Report\ManufacturerController::class, 'licenseManufacturerWise']);
            Route::post('licenses/jx-manufacturer-wise', [App\Http\Controllers\Report\ManufacturerController::class, 'getLicenseManufacturerWise']);

            Route::get('licenses/usage-wise', [App\Http\Controllers\Report\ManufacturerController::class, 'usageWise']);
            Route::post('licenses/jx-manufacturer-wise-usage', [App\Http\Controllers\Report\ManufacturerController::class, 'jxManufacturerWiseUsage']);

            Route::get('licenses/expire-info', [App\Http\Controllers\Report\LicenseController::class, 'expireInfo']);
            Route::post('licenses/jx-expire-info', [App\Http\Controllers\Report\LicenseController::class, 'jxExpireInfo']);
            Route::get('export-expire-info', [App\Http\Controllers\Report\LicenseController::class, 'downloadExpireInfoReport']);

            Route::get('locations', [App\Http\Controllers\Report\LocationController::class, 'index']);
            Route::post('jx-location-wise-devices', [App\Http\Controllers\Report\LocationController::class, 'ajaxDevice']);
            Route::get('export-location-reports', [App\Http\Controllers\Report\LocationController::class, 'downloadLocationWiseReport']);

            Route::post('jx-location-wise-consumables', [App\Http\Controllers\Report\LocationController::class, 'ajaxConsumable']);
            Route::post('jx-location-wise-accessories', [App\Http\Controllers\Report\LocationController::class, 'ajaxAccessories']);

            Route::get('device-location-wise', [App\Http\Controllers\Report\DeviceController::class, 'getDeviceLocationWise']);
            Route::post('jx-device-location-wise', [App\Http\Controllers\Report\DeviceController::class, 'ajaxDeviceLocationWise']);
            Route::get('export-devices-location-reports', [App\Http\Controllers\Report\DeviceController::class, 'downloadDeviceLocationWiseReport']);

            Route::get('places', [App\Http\Controllers\Report\PlaceController::class, 'index']);
            Route::post('jx-place-wise-list', [App\Http\Controllers\Report\PlaceController::class, 'ajaxList']);
            Route::get('export-place-wise-report', [App\Http\Controllers\Report\PlaceController::class, 'downloadPlaceReport']);

            Route::post('jx-place-wise-consumables', [App\Http\Controllers\Report\PlaceController::class, 'ajaxConsumable']);
            Route::post('jx-place-wise-accessories', [App\Http\Controllers\Report\PlaceController::class, 'ajaxAccessories']);

            Route::get('place-wise-devices', [App\Http\Controllers\Report\PlaceController::class, 'getdevice']);
            Route::post('jx-place-wise-deviceslist', [App\Http\Controllers\Report\PlaceController::class, 'ajaxDeviceList']);
            Route::get('export-place-wise-devices', [App\Http\Controllers\Report\PlaceController::class, 'downloadDeviceReport']);

            Route::get('sold-device', [App\Http\Controllers\Report\DeviceSoldController::class, 'getsolddevice']);
            Route::post('jx-sold-deviceslist', [App\Http\Controllers\Report\DeviceSoldController::class, 'ajaxSoldDeviceList']);
            Route::get('export-sold-devices', [App\Http\Controllers\Report\DeviceSoldController::class, 'downloadSoldDeviceReport']);

            Route::get('place-wise-accessories', [App\Http\Controllers\Report\PlaceController::class, 'getAccessory']);
            Route::post('jx-place-wise-accessorylist', [App\Http\Controllers\Report\PlaceController::class, 'ajaxAccessoryList']);
            Route::get('export-place-wise-accessories', [App\Http\Controllers\Report\PlaceController::class, 'downloadAccessoryReport']);

            Route::get('device-warranty', [App\Http\Controllers\Report\DeviceController::class, 'deviceWarrantyReport']);
            Route::post('jx-device-warrenty-report2', [App\Http\Controllers\Report\DeviceController::class, 'ajaxDeviceWarrentyreport']);
            Route::get('export-warranty', [App\Http\Controllers\Report\DeviceController::class, 'downloadDeviceWarranty']);

            Route::get('device-amc', [App\Http\Controllers\Report\DeviceController::class, 'deviceAmcReport']);
            Route::post('jx-device-amc-report2', [App\Http\Controllers\Report\DeviceController::class, 'ajaxDeviceAmcreport']);
            Route::get('export-amc', [App\Http\Controllers\Report\DeviceController::class, 'downloadDeviceAmc']);

            Route::get('threshold-wise', [App\Http\Controllers\Report\CategoryController::class, 'getThresholdWise']);
            Route::post('jx-threshold-wise-report', [App\Http\Controllers\Report\CategoryController::class, 'ajaxThresholdWiseReport']);
            Route::get('export-threshold-report', [App\Http\Controllers\Report\CategoryController::class, 'downloadThresholdReport']);

            Route::get('contract-devices', [App\Http\Controllers\Report\DeviceController::class, 'contractDeviceReport']);
            Route::post('jx-contract-devices-report', [App\Http\Controllers\Report\DeviceController::class, 'ajaxContractDevicesList']);
            Route::get('export-contract-devices', [App\Http\Controllers\Report\DeviceController::class, 'downloadContractDevices']);

            Route::get('departments', [App\Http\Controllers\Report\DeviceController::class, 'departmentWiseReport']);
            Route::post('jx-department-wise-report', [App\Http\Controllers\Report\DeviceController::class, 'jxDepartmentWiseReport']);

            Route::get('expected-return-items', [App\Http\Controllers\Report\DeviceController::class, 'expectedReturnItems']);
            Route::post('jx-expected-return-items', [App\Http\Controllers\Report\DeviceController::class, 'jxExpectedReturnItems']);
            Route::get('export-items', [App\Http\Controllers\Report\DeviceController::class, 'downloadItemsReport']);

            Route::get('expected-return-devices', [App\Http\Controllers\Report\DepartmentController::class, 'expectedReturnDevices']);
            Route::post('jx-expected-return-devices', [App\Http\Controllers\Report\DepartmentController::class, 'ajaxExpectedReturnDevices']);
            Route::get('export-device', [App\Http\Controllers\Report\DepartmentController::class, 'downloadDeviceReport']);

            Route::get('expected-return-accessories', [App\Http\Controllers\Report\AccessoryController::class, 'expectedReturnAccessories']);
            Route::post('jx-expected-return-accessories', [App\Http\Controllers\Report\AccessoryController::class, 'ajaxExpectedReturnAccessories']);
            Route::get('export-accessories', [App\Http\Controllers\Report\AccessoryController::class, 'downloadAccessoryReport']);

            Route::get('department-wise-devices', [App\Http\Controllers\Report\DepartmentController::class, 'departmentWiseReport']);
            Route::post('jx-department-wise-device', [App\Http\Controllers\Report\DepartmentController::class, 'ajaxDepartmentWiseDevices']);
            Route::get('export-department-wise-device', [App\Http\Controllers\Report\DepartmentController::class, 'depWiseDeviceReport']);

            Route::get('department-wise-licenses', [App\Http\Controllers\Report\DepartmentController::class, 'departmentWiseLicenses']);
            Route::post('jx-department-wise-licenses', [App\Http\Controllers\Report\DepartmentController::class, 'ajaxDepartmentWiseLicenses']);
            Route::get('export-department-wise-license', [App\Http\Controllers\Report\DepartmentController::class, 'depWiseLicenseReport']);

            Route::get('department-wise-accessories', [App\Http\Controllers\Report\DepartmentController::class, 'departmentWiseAccessories']);
            Route::post('jx-department-wise-accessories', [App\Http\Controllers\Report\DepartmentController::class, 'ajaxDepartmentWiseAccessories']);
            Route::get('export-department-wise-accessory', [App\Http\Controllers\Report\DepartmentController::class, 'depWiseAccessoryReport']);

            Route::get('department-wise-devices-report', [App\Http\Controllers\Report\DepartmentController::class, 'departmentWiseDeviceReport']);
            Route::post('ajax-department-wise-device-report', [App\Http\Controllers\Report\DepartmentController::class, 'ajaxDepartmentWiseDevicesReport']);
            Route::get('export-department-wise-device-report', [App\Http\Controllers\Report\DepartmentController::class, 'depWiseDeviceReportExport']);

            Route::get('department-wise-licenses-report', [App\Http\Controllers\Report\DepartmentController::class, 'departmentWiseLicensesReport']);
            Route::post('ajax-department-wise-licenses-report', [App\Http\Controllers\Report\DepartmentController::class, 'ajaxDepartmentWiseLicensesReport']);
            Route::get('export-department-wise-licenses-report', [App\Http\Controllers\Report\DepartmentController::class, 'depWiseLicensesReportExport']);

            Route::get('department-wise-accessories-report', [App\Http\Controllers\Report\DepartmentController::class, 'departmentWiseAccessoriesReport']);
            Route::post('ajax-department-wise-accessories-report', [App\Http\Controllers\Report\DepartmentController::class, 'ajaxDepartmentWiseAccessoriesReport']);
            Route::get('export-department-wise-accessories-report', [App\Http\Controllers\Report\DepartmentController::class, 'depWiseAccessoriesReportExport']);

            Route::get('department-wise-consumable-report', [App\Http\Controllers\Report\DepartmentController::class, 'departmentWiseConsumableReport']);
            Route::post('ajax-department-wise-consumable-report', [App\Http\Controllers\Report\DepartmentController::class, 'ajaxDepartmentWiseConsumableReport']);
            Route::get('export-department-wise-consumable-report', [App\Http\Controllers\Report\DepartmentController::class, 'depWiseConsumableReportExport']);

            Route::get('department-wise-component-report', [App\Http\Controllers\Report\DepartmentController::class, 'departmentWiseComponentReport']);
            Route::post('ajax-department-wise-component-report', [App\Http\Controllers\Report\DepartmentController::class, 'ajaxDepartmentWiseComponentReport']);
            Route::get('export-department-wise-component-report', [App\Http\Controllers\Report\DepartmentController::class, 'depWiseComponentReportExport']);

            Route::get('device-report', [App\Http\Controllers\Report\DeviceController::class, 'getDevice']);
            Route::post('jx-device-report', [App\Http\Controllers\Report\DeviceController::class, 'ajaxDeviceList']);
            Route::get('export-device-report', [App\Http\Controllers\Report\DeviceController::class, 'downloadDeviceList']);
            Route::get('export-device-report-pdf', [App\Http\Controllers\Report\DeviceController::class, 'deviceExportPDF']);

            Route::get('device-audit-acceptance', [App\Http\Controllers\Report\DeviceController::class, 'getAuditAcceptance']);
            Route::post('jx-device-audit-acceptance', [App\Http\Controllers\Report\DeviceController::class, 'ajaxAuditAcceptanceList']);
            Route::get('export-device-audit-acceptance', [App\Http\Controllers\Report\DeviceController::class, 'downloadDeviceList']);
            Route::get('export-device-audit-acceptance-pdf', [App\Http\Controllers\Report\DeviceController::class, 'deviceExportPDF']);

            Route::get('device-checkout-accept', [App\Http\Controllers\Report\DeviceController::class, 'getCheckoutAcceptReport']);
            Route::post('jx-device-checkout-accept', [App\Http\Controllers\Report\DeviceController::class, 'ajaxCheckoutAcceptReportList']);
            Route::get('export-device-checkout-accept-excel', [App\Http\Controllers\Report\DeviceController::class, 'downloadCheckoutAcceptReportList']);
            Route::get('export-device-checkout-accept-pdf', [App\Http\Controllers\Report\DeviceController::class, 'checkoutAcceptExportPDF']);

            Route::get('azure-device-utility', [App\Http\Controllers\Report\DeviceController::class, 'getAzureDeviceUtilityReport']);
            Route::post('jx-azureutility', [App\Http\Controllers\Report\DeviceController::class, 'ajaxAzureUtility']);
            Route::get('export-azureutility', [App\Http\Controllers\Report\DeviceController::class, 'downloadExportAzureUtility']);

            Route::get('term-conditions', [App\Http\Controllers\Report\TermConditionController::class, 'requestReport']);
            Route::post('jx-term-condition', [App\Http\Controllers\Report\TermConditionController::class, 'ajaxTermCondition']);
            Route::get('downloadTermCondition', [App\Http\Controllers\Report\TermConditionController::class, 'downloadTermCondition']);

            // deleted user assign asset report
            Route::get('deleted-users', [App\Http\Controllers\Report\UserController::class, 'deletedUsers']);
            Route::post('jx-deleted-users-wise-devices', [App\Http\Controllers\Report\UserController::class, 'ajaxDeletedUsers']);
            Route::post('jx-user-wise-accessories', [App\Http\Controllers\Report\UserController::class, 'ajaxAccessories']);
            Route::post('jx-user-wise-consumables', [App\Http\Controllers\Report\UserController::class, 'ajaxConsumable']);
            Route::get('export-user-reports', [App\Http\Controllers\Report\UserController::class, 'downloadDeletedUserWiseReport']);

            // export requests
            Route::get('export-requests', [App\Http\Controllers\Report\ExportRequestsController::class, 'index']);
            Route::post('getExportRequests', [App\Http\Controllers\Report\ExportRequestsController::class, 'getExportRequests']);
            Route::get('downloadExportRequests', [App\Http\Controllers\Report\ExportRequestsController::class, 'downloadExportRequests']);

            // Ticket Report
            Route::group(['middleware' => 'auth.service_ticket'], function () {
                Route::get('deleted-tickets', [App\Http\Controllers\Report\TicketController::class, 'getDeletedTickets']);
                Route::post('jx-deleted-tickets', [App\Http\Controllers\Report\TicketController::class, 'ajaxDeletedTickets']);
                Route::get('export-deleted-tickets', [App\Http\Controllers\Report\TicketController::class, 'downloadDeletedTickets']);
                Route::get('export-deleted-tickets-pdf', [App\Http\Controllers\Report\TicketController::class, 'downloadDeletedTicketsPDF']);

                Route::get('tickets/merged-tickets', [App\Http\Controllers\Report\TicketController::class, 'mergedtickets']);
                Route::post('tickets/jx-merged-tickets', [App\Http\Controllers\Report\TicketController::class, 'ajaxMergedTickets']);
                Route::get('export-merged-tickets', [App\Http\Controllers\Report\TicketController::class, 'downloadMergedTickets']);
                Route::get('export-merged-tickets-pdf', [App\Http\Controllers\Report\TicketController::class, 'downloadMergedTicketsPDF']);

                Route::get('tickets/esclate-tickets', [App\Http\Controllers\Report\TicketController::class, 'esclatetickets']);
                Route::post('tickets/jx-esclate-tickets', [App\Http\Controllers\Report\TicketController::class, 'ajaxEsclateTickets']);
                Route::get('export-esclate-tickets', [App\Http\Controllers\Report\TicketController::class, 'downloadEsclateTickets']);
                Route::get('export-esclate-tickets-pdf', [App\Http\Controllers\Report\TicketController::class, 'downloadEsclateTicketsPDF']);

                Route::get('tickets/overdue-tickets', [App\Http\Controllers\Report\TicketController::class, 'overduetickets']);
                Route::post('tickets/jx-overdue-tickets', [App\Http\Controllers\Report\TicketController::class, 'ajaxOverdueTickets']);
                Route::get('export-overdue-tickets', [App\Http\Controllers\Report\TicketController::class, 'downloadOverdueTickets']);
                Route::get('export-overdue-tickets-pdf', [App\Http\Controllers\Report\TicketController::class, 'downloadOverdueTicketsPDF']);

                /*Ticket exception Notifications*/
                Route::get('tickets/exception-notification', [App\Http\Controllers\Report\TicketController::class, 'exceptiontickets']);
                Route::post('tickets/jx-exception-notification', [App\Http\Controllers\Report\TicketController::class, 'ajaxExceptionTickets']);
                Route::get('export-exception-notification', [App\Http\Controllers\Report\TicketController::class, 'downloadExceptionTickets']);
                Route::get('export-exception-notification-pdf', [App\Http\Controllers\Report\TicketController::class, 'downloadExceptionTicketsPDF']);

                Route::get('tickets/tech-log-report', [App\Http\Controllers\Report\TicketController::class, 'technicianLogReports']);
                Route::post('tickets/jx-list-tech-logs', [App\Http\Controllers\Report\TicketController::class, 'ajaxTechLogs']);
                Route::get('tickets/downloadTechLogReport', [App\Http\Controllers\Report\TicketController::class, 'downloadTechLogReport']);

                Route::get('tickets/ticket-count-wise', [App\Http\Controllers\Report\TicketController::class, 'ticketcount']);
                Route::post('tickets/jx-ticket-count-wise', [App\Http\Controllers\Report\TicketController::class, 'ajaxTicketCount']);

                Route::get('tickets/ticket-count-problem-category-wise', [App\Http\Controllers\Report\TicketController::class, 'ticketcountProblemCategory']);
                Route::post('tickets/jx-ticket-count-problem-category-wise', [App\Http\Controllers\Report\TicketController::class, 'ajaxticketcountProblemCategory']);

                /*Handler tickets info*/
                Route::get('tickets/hand-tic-info', [App\Http\Controllers\Report\TicketController::class, 'handlerTicketsInfo']);
                Route::post('tickets/jx-hand_tic_info', [App\Http\Controllers\Report\TicketController::class, 'ajaxHandlerticketsinfo']);
                Route::get('export-hand_tic_info', [App\Http\Controllers\Report\TicketController::class, 'downloadHandlerticketsinfo']);
                Route::get('export-hand_tic_info-pdf', [App\Http\Controllers\Report\TicketController::class, 'downloadHandlerticketsinfoPDF']);

                Route::get('tickets/ticket-handler-wise', [App\Http\Controllers\Report\TicketController::class, 'ticketHandlerWise']);
                Route::post('tickets/jx-handler-wise', [App\Http\Controllers\Report\TicketController::class, 'ajaxTicketHandlerWise']);
                Route::post('tickets/average-response-details', [App\Http\Controllers\Report\TicketController::class, 'averageResponseDetails']);
                Route::get('tickets/jx-download-handler-wise', [App\Http\Controllers\Report\TicketController::class, 'ajaxDownloadTicketHandlerWise']);

                Route::get('tickets/department-wise', [App\Http\Controllers\Report\TicketController::class, 'departmentWise']);
                Route::post('tickets/jx-department-wise', [App\Http\Controllers\Report\TicketController::class, 'ajaxDepartmentWise']);
                Route::get('tickets/jx-download-department-wise', [App\Http\Controllers\Report\TicketController::class, 'ajaxDownloadTicketDepartmentWise']);
                Route::get('tickets/export-department-tickets-pdf', [App\Http\Controllers\Report\TicketController::class, 'downloadDepartmentTicketsPDF']);
                Route::get('tickets/info/{main_filter?}', [App\Http\Controllers\Report\TicketController::class, 'handlerTicketsInfo']);

                Route::get('tickets/location-wise', [App\Http\Controllers\Report\TicketController::class, 'locationWise']);
                Route::post('tickets/jx-location-wise', [App\Http\Controllers\Report\TicketController::class, 'ajaxLocationWise']);
                Route::get('tickets/jx-download-location-wise', [App\Http\Controllers\Report\TicketController::class, 'ajaxDownloadTicketLocationWise']);

                Route::get('tickets/ticket-task-report', [App\Http\Controllers\Report\TicketController::class, 'ticketTaskReport']);
                Route::post('tickets/jx-ticket-task-report', [App\Http\Controllers\Report\TicketController::class, 'ajaxTicketTaskReport']);
                Route::get('export-ticket-task-report', [App\Http\Controllers\Report\TicketController::class, 'downloadTicketTaskReport']);

                Route::get('tickets/hvac-report', [App\Http\Controllers\Report\TicketController::class, 'hvacReport']);
                Route::post('tickets/jx-hvac-report', [App\Http\Controllers\Report\TicketController::class, 'ajaxHvacReport']);
                Route::get('export-hvac-report-xls', [App\Http\Controllers\Report\TicketController::class, 'exportHvacReport']);

                Route::get('tickets/ticket-report', [App\Http\Controllers\Report\TicketController::class, 'ticketReport']);
                Route::post('tickets/jx-ticket-report', [App\Http\Controllers\Report\TicketController::class, 'ajaxTicketReport']);
                Route::get('export-ticket-report-xls', [App\Http\Controllers\Report\TicketController::class, 'downloadTicketReportXLS']);
                Route::get('export-ticket-report-pdf', [App\Http\Controllers\Report\TicketController::class, 'downloadTicketReportPDF']);

                Route::get('tickets/request-report', [App\Http\Controllers\Report\ServiceRequestController::class, 'requestReport']);
                Route::post('tickets/jx-request-report', [App\Http\Controllers\Report\ServiceRequestController::class, 'ajaxRequestReport']);
                Route::get('export-request-report-xls', [App\Http\Controllers\Report\ServiceRequestController::class, 'exportServiceRequestReport']);

                /*service request member report */
                Route::get('tickets/request-member-wise', [App\Http\Controllers\Report\ServiceRequestController::class, 'memberRequestReport']);
                Route::post('tickets/jx-request-member-wise', [App\Http\Controllers\Report\ServiceRequestController::class, 'ajaxRequestMemberReport']);
                Route::get('tickets/jx-request-download-member-wise', [App\Http\Controllers\Report\ServiceRequestController::class, 'exportMemberRequestReport']);

                Route::get('tickets/feedback-report', [App\Http\Controllers\Report\TicketController::class, 'feedbackReport']);
                Route::post('tickets/jx-feedback-report', [App\Http\Controllers\Report\TicketController::class, 'ajaxFeedbackReport']);
                Route::get('export-feedback-report-xls', [App\Http\Controllers\Report\TicketController::class, 'downloadFeedbackReportXLS']);
                Route::get('export-feedback-report-pdf', [App\Http\Controllers\Report\TicketController::class, 'downloadFeedbackReportPDF']);

                Route::get('tickets/reopen-ticket-report', [App\Http\Controllers\Report\TicketController::class, 'reopenTicketReport']);
                Route::post('tickets/jx-reopen-report', [App\Http\Controllers\Report\TicketController::class, 'ajaxReopenTicketReport']);
                Route::get('export-ticket-reopen-report-xls', [App\Http\Controllers\Report\TicketController::class, 'downloadTicketReopenReportXLS']);
                Route::get('export-ticket-reopen-report-pdf', [App\Http\Controllers\Report\TicketController::class, 'downloadTicketReopenReportPDF']);

                // Ticket categorywise-agening-report
                Route::get('tickets/categorywise-agening-report', [App\Http\Controllers\Report\TicketController::class, 'catWiseAgeningReport']);
                Route::post('tickets/jx-catwise-agening-report', [App\Http\Controllers\Report\TicketController::class, 'ajaxcatWiseAgeningReport']);
                Route::get('downloaCatWiseAgeningReport', [App\Http\Controllers\Report\TicketController::class, 'downloaCatWiseAgeningReport']);

                // Ticket handlerwise-agening-report
                Route::get('tickets/handlerwise-agening-report', [App\Http\Controllers\Report\TicketController::class, 'handlerWiseAgeningReport']);
                Route::post('tickets/jx-handlerwise-agening-report', [App\Http\Controllers\Report\TicketController::class, 'ajaxhandlerWiseAgeningReport']);
                Route::get('downloaHandlerWiseAgeningReport', [App\Http\Controllers\Report\TicketController::class, 'downloadHandlerWiseAgingReport']);

                // Ticket auto-Resolved-ticket-report
                Route::get('tickets/auto-resolved-ticket-report', [App\Http\Controllers\Report\TicketController::class, 'autoResolvedTicketReport']);
                Route::post('tickets/jx-auto-resolved-ticket-report', [App\Http\Controllers\Report\TicketController::class, 'ajaxAutoResolvedTicketReport']);
                Route::get('export-auto-resolved-ticket-xls', [App\Http\Controllers\Report\TicketController::class, 'downloadAutoResolvedTicketReport']);

                // Report for service request hostname
                Route::get('tickets/usb-admin-access-report', [App\Http\Controllers\Report\ServiceRequestController::class, 'serviceRequestApprovedReport']);
                Route::post('tickets/jx-request-approved-report', [App\Http\Controllers\Report\ServiceRequestController::class, 'ajaxRequestApprovedReport']);
                Route::get('export-request-approve-report-xls', [App\Http\Controllers\Report\ServiceRequestController::class, 'downloadApprovedReport']);

                Route::get('tickets/email-reminder-report', [App\Http\Controllers\Report\ServiceRequestController::class, 'emailReminderReport']);
                Route::post('tickets/jx-email-reminder-report', [App\Http\Controllers\Report\ServiceRequestController::class, 'ajaxEmailReminderReport']);
                Route::get('export-email-reminder-report-xls', [App\Http\Controllers\Report\ServiceRequestController::class, 'exportEmailReminderReport']);

                // Ticket Handler Wise SLA Report for specific time period
                Route::get('tickets/day-wise-handler-report', [App\Http\Controllers\Report\TicketController::class, 'dayWiseHandlerReport']);
                Route::post('tickets/jx-day-wise-handler-report', [App\Http\Controllers\Report\TicketController::class, 'ajaxDayWiseHandlerReport']);
                Route::get('downloadDayWiseHandlerReport', [App\Http\Controllers\Report\TicketController::class, 'downloadDayWiseHandlerReport']);

                // Inactive user open ticket report
                Route::get('tickets/inactive-user-report', [App\Http\Controllers\Report\TicketController::class, 'inactiveUserReport']);
                Route::post('tickets/inactive-user-ajaxList', [App\Http\Controllers\Report\TicketController::class, 'inactiveUserReportList']);
                Route::get('tickets/inactive-user-export', [App\Http\Controllers\Report\TicketController::class, 'inactiveUserReportExport']);
                Route::post('tickets/bulk_resolve', [App\Http\Controllers\Report\TicketController::class, 'bulkResolve']);

                // cr new report including max no. of days
                Route::get('tickets/exception-access-report', [App\Http\Controllers\Report\ServiceRequestController::class, 'exceptionAccessReport']);
                Route::post('tickets/jx-exception-access-report', [App\Http\Controllers\Report\ServiceRequestController::class, 'ajaxExceptionAccessReport']);
                Route::get('tickets/export-exception-access-report-xls', [App\Http\Controllers\Report\ServiceRequestController::class, 'exportExceptionAccessReport']);

                Route::get('tickets/ticket-pendency-report', [App\Http\Controllers\Report\TicketController::class, 'ticketPendencyReport']);
                Route::post('tickets/jx-pendency-report', [App\Http\Controllers\Report\TicketController::class, 'ajaxTicketPendencyReport']);
                Route::get('export-ticket-pendency-xls', [App\Http\Controllers\Report\TicketController::class, 'downloadTicketPendencyReportXLS']);
                Route::post('tickets/jx-technician-pendency-report', [App\Http\Controllers\Report\TicketController::class, 'ajaxTechnicianTicketPendencyReport']);
                Route::get('export-technician-pendency-report', [App\Http\Controllers\Report\TicketController::class, 'downloadTechnicianTicketPendencyReportXLS']);

            });
            Route::get('fetch-custom-graph', [App\Http\Controllers\Report\CustomGraphController::class, 'fetchCustomGraphForDropDown']);
            Route::get('change-request-report', [App\Http\Controllers\Report\ChangeRequestReportController::class, 'changeRequestlist']);
            Route::post('ajax_change_request', [App\Http\Controllers\Report\ChangeRequestReportController::class, 'ajaxChangeRequestlist']);
            Route::get('export_ajax_change_request', [App\Http\Controllers\Report\ChangeRequestReportController::class, 'ajaxChangeRequestDownload']);

            // CR problem category report which is check for include in priville access
            Route::get('category-priville-access', [App\Http\Controllers\Report\TicketReportController::class, 'categoryReport']);
            Route::post('jx-category-privilege-report', [App\Http\Controllers\Report\TicketReportController::class, 'ajaxCategoryReport']);
            Route::get('export-category-privilege-report', [App\Http\Controllers\Report\TicketReportController::class, 'exportCategoryReport']);
            Route::get('consumables/individual-material', [App\Http\Controllers\Report\ConsumableController::class, 'individualMaterial']);
            Route::post('consumables/jx-individual-material', [App\Http\Controllers\Report\ConsumableController::class, 'ajaxIndividualMaterial']);
            Route::get('consumables/export-individual-material', [App\Http\Controllers\Report\ConsumableController::class, 'exportIndividualMaterial']);
            Route::get('consumables/item-threshold-level', [App\Http\Controllers\Report\ConsumableController::class, 'itemThresholdLevel']);
            Route::post('consumables/jx-threshold-level', [App\Http\Controllers\Report\ConsumableController::class, 'ajaxItemThresholdLevel']);
            Route::get('consumables/export-threshold-level', [App\Http\Controllers\Report\ConsumableController::class, 'exportItemThresholdLevel']);
            Route::get('consumables/daily-distribution', [App\Http\Controllers\Report\ConsumableController::class, 'dailyDistribution']);
            Route::post('consumables/jx-daily-distribution', [App\Http\Controllers\Report\ConsumableController::class, 'ajaxDailyDistribution']);
            Route::get('consumables/export-daily-distribution', [App\Http\Controllers\Report\ConsumableController::class, 'exportDailyDistribution']);
            Route::get('consumables/month-wise', [App\Http\Controllers\Report\ConsumableController::class, 'monthWiseConsumable']);
            Route::post('consumables/jx-month-wise', [App\Http\Controllers\Report\ConsumableController::class, 'ajaxMonthWiseConsumable']);
            Route::get('consumables/download-month-wise', [App\Http\Controllers\Report\ConsumableController::class, 'exportMonthWiseConsumable']);
        });

        Route::get('knowledge-document/article/view/{id}', [App\Http\Controllers\KnowledgeManagement\DocumentController::class, 'viewArticle']);
        // Knowledge Document
        Route::group(['prefix' => 'knowledge_document'], function () {
            if (config("services.knowledge_document.enabled")) {
                Route::get('document', [App\Http\Controllers\KnowledgeManagement\DocumentController::class, 'faqKnowledgeDocument'])->name('kdLists');
                Route::post('document/create', [App\Http\Controllers\KnowledgeManagement\DocumentController::class, 'createDocuments']);
                Route::get('document/delete/{id}', [App\Http\Controllers\KnowledgeManagement\DocumentController::class, 'deleteDocuments']);
                Route::get('document/edit/{id}', [App\Http\Controllers\KnowledgeManagement\DocumentController::class, 'editDocuments']);
                Route::post('document/update', [App\Http\Controllers\KnowledgeManagement\DocumentController::class, 'updateDocuments']);
                Route::any('document/getTagDetails', [App\Http\Controllers\KnowledgeManagement\DocumentController::class, 'ajaxTagDetail']);
                Route::post('document/jx-document-list', [App\Http\Controllers\KnowledgeManagement\DocumentController::class, 'ajaxdocumentList']);
                Route::post('document/jx-article-list', [App\Http\Controllers\KnowledgeManagement\DocumentController::class, 'ajaxarticleList']);
                Route::get('article/view/{id}', [App\Http\Controllers\KnowledgeManagement\DocumentController::class, 'viewArticle']);
                Route::post('attachment/kd_attachment', [App\Http\Controllers\KnowledgeManagement\DocumentController::class, 'KnowledgeDocumentAttachment']);
                Route::post('attachment/kd_attachment_remove', [App\Http\Controllers\KnowledgeManagement\DocumentController::class, 'KnowledgeDocumentAttachmentRemove']);
                Route::get('attachment/download/{id}', [App\Http\Controllers\KnowledgeManagement\DocumentController::class, 'download']);
                Route::get('attachment/view/{id}', [App\Http\Controllers\KnowledgeManagement\DocumentController::class, 'viewItem']);
                Route::post('article/staring', [App\Http\Controllers\KnowledgeManagement\DocumentController::class, 'toggleStar']);
                Route::get('document/search', [App\Http\Controllers\KnowledgeManagement\DocumentController::class, 'search']);

                // category
                Route::get('categories', [App\Http\Controllers\KnowledgeManagement\KdCategoryController::class, 'index']);
                Route::post('jx-category-list', [App\Http\Controllers\KnowledgeManagement\KdCategoryController::class, 'ajaxGetList']);
                Route::get('problem-categories/by-dept/{id}', [App\Http\Controllers\KnowledgeManagement\KdCategoryController::class, 'ajaxOptionsGetParentCategory']);
                Route::post('categories/store', [App\Http\Controllers\KnowledgeManagement\KdCategoryController::class, 'store']);
                Route::get('edit_category/{id}', [App\Http\Controllers\KnowledgeManagement\KdCategoryController::class, 'ajaxCategoryEdit']);
                Route::post('update_category', [App\Http\Controllers\KnowledgeManagement\KdCategoryController::class, 'ajaxCategoryUpdate']);
                Route::get('delete_category/{id}', [App\Http\Controllers\KnowledgeManagement\KdCategoryController::class, 'deleteCategory']);
                Route::get('export-category', [App\Http\Controllers\KnowledgeManagement\KdCategoryController::class, 'CategoryExport']);
                Route::get('fetch_category_by_ajax', [App\Http\Controllers\KnowledgeManagement\KdCategoryController::class, 'fetchCategoryByAjax']);
                Route::get('fetch_subcategory_by_ajax', [App\Http\Controllers\KnowledgeManagement\KdCategoryController::class, 'fetchSubCategoryByAjax']);
            }
        });

        //for new scheduled maintenance plan
        Route::get('new-scheduled-maintenance', [App\Http\Controllers\NewScheduledMaintenanceController::class, 'getIndex']);
        Route::post('new-scheduled-maintenance/create', [App\Http\Controllers\NewScheduledMaintenanceController::class, 'ajaxAddScheduledMaintenance']);
        Route::post('new-scheduled-maintenance/list', [App\Http\Controllers\NewScheduledMaintenanceController::class, 'ajaxList']);
        Route::get('new-scheduled-maintenance/delete/{id}', [App\Http\Controllers\NewScheduledMaintenanceController::class, 'deleteScheduledMaintenance']);
        Route::get('new-export-scheduled-maintenance-excel', [App\Http\Controllers\NewScheduledMaintenanceController::class, 'downloadScheduledMaintenancePlan']);
        Route::post('new-scheduled-maintenance/edit/{id}', [App\Http\Controllers\NewScheduledMaintenanceController::class, 'ajaxEditScheduledMaintenance']);
        Route::get('new-scheduled-maintenance/get/{id}', [App\Http\Controllers\NewScheduledMaintenanceController::class, 'ajaxGetScheduleMaintenanceTask']);
        Route::post('new-scheduled-maintenance/clone/{id}', [App\Http\Controllers\NewScheduledMaintenanceController::class, 'cloneScheduledMaintenance']);

        /* Task List */
        Route::post('new-scheduled-task/view-list', [App\Http\Controllers\NewScheduledMaintenanceController::class, 'ajaxScheduledTaskList']);
        Route::post('new-scheduled-task/add', [App\Http\Controllers\NewScheduledMaintenanceController::class, 'ajaxAddScheduledTask']);
        Route::post('new-scheduled-task/edit/{id}', [App\Http\Controllers\NewScheduledMaintenanceController::class, 'ajaxEditScheduledTask']);
        Route::get('new-scheduled-task/get/{id}', [App\Http\Controllers\NewScheduledMaintenanceController::class, 'ajaxGetScheduledTask']);
        Route::get('new-scheduled-task/delete/{id}', [App\Http\Controllers\NewScheduledMaintenanceController::class, 'deleteScheduledTask']);
        Route::get('new-export-scheduled-task-excel/{plan_id}', [App\Http\Controllers\NewScheduledMaintenanceController::class, 'downloadTaskList']);
        Route::get('getPlanForDropDown', [App\Http\Controllers\NewScheduledMaintenanceController::class, 'getPlanForDropDown']);
        Route::post('allocate-plan', [App\Http\Controllers\NewScheduledMaintenanceController::class, 'ajaxAllocatePlanNew']);

        Route::get('schedule-maintenance-device-listing', [App\Http\Controllers\NewScheduledMaintenanceController::class, 'getDeviceIndex']);
        Route::post('schedule-maintenance-device-listing/list', [App\Http\Controllers\NewScheduledMaintenanceController::class, 'ajaxDeviceList']);
        Route::get('schedule-maintenance-device-listing/delete/{id}', [App\Http\Controllers\NewScheduledMaintenanceController::class, 'deleteDevice']);
        Route::get('schedule-maintenance-device-listing/edit/{id}', [App\Http\Controllers\NewScheduledMaintenanceController::class, 'editDevice']);
        Route::post('schedule-maintenance-device-listing/update', [App\Http\Controllers\NewScheduledMaintenanceController::class, 'updateDevice']);

        /** Plan allocation listing */
        Route::get('new-scheduled-maintenance-listing', [App\Http\Controllers\NewScheduledMaintenanceController::class, 'getAllocatedIndex']);
        Route::post('new-scheduled-maintenance-listing/allowcated-list', [App\Http\Controllers\NewScheduledMaintenanceController::class, 'ajaxAllocatedList']);
        Route::get('new-scheduled-maintenance-listing-excel', [App\Http\Controllers\NewScheduledMaintenanceController::class, 'downloadNewScheduledMaintenanceList']);
        Route::get('new-scheduled-maintenance-listing/calendarview', [App\Http\Controllers\NewScheduledMaintenanceController::class, 'getcalendarview']);
        Route::get('new-scheduled-maintenance-allow/delete/{id}', [App\Http\Controllers\NewScheduledMaintenanceController::class, 'deleteScheduledMaintenanceAllow']);
        Route::get('new-scheduled-maintenance-allow/get/{id}', [App\Http\Controllers\NewScheduledMaintenanceController::class, 'ajaxGetScheduledMaintenanceAllow']);
        Route::get('new-scheduled-maintenance-allow/edit/{id}', [App\Http\Controllers\NewScheduledMaintenanceController::class, 'ajaxEditScheduledMaintenanceAllow']);
        Route::get('new-scheduled-maintenance-allow/devices/{id}', [App\Http\Controllers\NewScheduledMaintenanceController::class, 'ajaxScheduledDeviceList']);
        Route::post('new-scheduled-maintenance-allow/update', [App\Http\Controllers\NewScheduledMaintenanceController::class, 'updateScheduleMaintenance']);
        Route::get('new-scheduled-task/view/{id}', [App\Http\Controllers\NewScheduledMaintenanceController::class, 'manageTask']);
        Route::post('jx-get-tasks', [App\Http\Controllers\NewScheduledMaintenanceController::class, 'getTask']);
        Route::post('new-scheduled-task/select-multiple-task', [App\Http\Controllers\NewScheduledMaintenanceController::class, 'solveTask']);
        Route::post('new-scheduled-task/select-multiple-task-unsolve', [App\Http\Controllers\NewScheduledMaintenanceController::class, 'unsolveTask']);

        /*New schedule maintenance flow by palak */
        Route::name('schedule.')->group(function () {
            Route::get('schedule-maintenance-list', [App\Http\Controllers\ScheduleMaintenance\ScheduleMaintenanceListController::class, 'getScheduleMaintenancePlanIndex'])->name('index');
            Route::post('schedule-maintenance-list/list', [App\Http\Controllers\ScheduleMaintenance\ScheduleMaintenanceListController::class, 'ajaxList'])->name('ajaxIndex');
            Route::post('schedule-maintenance-list/create', [App\Http\Controllers\ScheduleMaintenance\ScheduleMaintenanceListController::class, 'createScheduleMaintenancePlan'])->name('create');
            Route::get('new-export-scheduled-maintenance--list-excel', [App\Http\Controllers\ScheduleMaintenance\ScheduleMaintenanceListController::class, 'downloadScheduledMaintenancePlan'])->name('download');
            Route::get('schedule-maintenance-list/edit/{id}', [App\Http\Controllers\ScheduleMaintenance\ScheduleMaintenanceListController::class, 'editScheduleMaintenancePlan'])->name('edit');
            Route::post('schedule-maintenance-list/update/{id}', [App\Http\Controllers\ScheduleMaintenance\ScheduleMaintenanceListController::class, 'updateScheduleMaintenancePlan'])->name('update');
            Route::get('schedule-maintenance-list/delete/{id}', [App\Http\Controllers\ScheduleMaintenance\ScheduleMaintenanceListController::class, 'deleteScheduleMaintenancePlan'])->name('delete');
            Route::post('schedule-maintenance-list/history/{id}', [App\Http\Controllers\ScheduleMaintenance\ScheduleMaintenanceListController::class, 'scheduleMaintenancePlanHistory'])->name('history');
            Route::get('schedule-maintenance-list/del/attachment/{id}', [App\Http\Controllers\ScheduleMaintenance\ScheduleMaintenanceListController::class, 'scheduleMaintenancePlanDelAttach'])->name('del.attach');
            Route::name('task.')->group(function () {
                Route::get('maintenance-task-list/{id}', [App\Http\Controllers\ScheduleMaintenance\MaintenanceTaskController::class, 'getMaintenanceTaskIndex'])->name('index');
                Route::post('maintenance-task/list', [App\Http\Controllers\ScheduleMaintenance\MaintenanceTaskController::class, 'getajaxMaintenanceTask'])->name('ajaxIndex');
                Route::post('maintenance-task/add', [App\Http\Controllers\ScheduleMaintenance\MaintenanceTaskController::class, 'createMaintenanceTask'])->name('create');
                Route::post('maintenance-task/edit/{id}', [App\Http\Controllers\ScheduleMaintenance\MaintenanceTaskController::class, 'updateMaintenanceTask'])->name('update');
                Route::get('maintenance-task/delete/{id}', [App\Http\Controllers\ScheduleMaintenance\MaintenanceTaskController::class, 'deleteMaintenanceTask'])->name('delete');
                Route::get('maintenance-task/get/{id}', [App\Http\Controllers\ScheduleMaintenance\MaintenanceTaskController::class, 'ajaxEditMaintenanceTask'])->name('ajaxEdit');
                Route::get('maintenance-task-excel/{id}', [App\Http\Controllers\ScheduleMaintenance\MaintenanceTaskController::class, 'downloadMaintenanceTask'])->name('download');
                Route::post('maintenance-task/reorder', [App\Http\Controllers\ScheduleMaintenance\MaintenanceTaskController::class, 'reorderMaintenanceTask'])->name('reorder');
                Route::post('maintenance-task/history/{id}', [App\Http\Controllers\ScheduleMaintenance\MaintenanceTaskController::class, 'maintenanceTaskHistory'])->name('history');
            });
            Route::name('allocate.')->group(function () {
                Route::get('plan-allocate-list', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'getAllocatedPlanIndex'])->name('index');
                Route::post('plan-allocate-list/list', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'ajaxAllocatedPlanList'])->name('ajaxIndex');
                Route::get('plan-allocate-list/getPlanForDropDown', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'getPlanForDropDown'])->name('plan');
                Route::post('plan-allocate-list/create-plan', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'allocatePlan'])->name('allocate');
                Route::get('plan-allocate-list/edit/{id}', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'editAllocatedPlan'])->name('edit');
                Route::post('plan-allocate-list/update/{id}', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'updateAllocatedPlan'])->name('update');
                Route::get('plan-allocate-list/delete/{id}', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'deleteAllocatedPlan'])->name('delete');
                Route::get('plan-allocate-list-excel', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'exportAllocatedPlan'])->name('export');
                Route::post('plan-allocate-list/device-info-list', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'ajaxDeviceList'])->name('ajaxDevice');
                Route::get('schedule-maintenance/status', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'getStatus'])->name('status.index');
                Route::post('my-allocated-plan-status/list', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'ajaxStatusList'])->name('status.ajaxIndex');
                Route::post('my-allocated-plan-status/create', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'createStatus'])->name('status.create');
                Route::get('my-allocated-plan-status/edit/{id}', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'editStatus'])->name('status.edit');
                Route::post('my-allocated-plan-status/update/{id}', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'updateStatus'])->name('status.update');
                Route::get('my-allocated-plan-status/delete/{id}', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'deleteStatus'])->name('status.delete');
                Route::get('my-allocated-plan-status/delete/icon/{id}', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'deleteStatusIcon'])->name('status.icon.delete');
                Route::get('plan-approve/{id}', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'planApproval'])->name('plan.approve');
                Route::get('all-assigned-plan-list', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'allAssignedPlan'])->name('all.plan');
                Route::post('all-allocated-plan-ajax', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'ajaxAllAllocatedPlan'])->name('ajax.allPlans');
                Route::get('all-plan-allocate-list-excel', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'exportAllAllocatedPlan'])->name('allPlan.export');
                Route::get('all-schedule-plan/calendar', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'getAllCalendarView'])->name('plan.all.calendar');
                Route::post('all-schedule-plan/calendar-event/{id}', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'getAllUserCalendarEvent'])->name('plan.all.calendar.event');
                Route::get('allocated-task-list/{id}', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'getAllAllocatedTaskList'])->name('all.allocated.task');
                Route::get('schedule-maintenance/my-approval', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'getAllPlansForApproval'])->name('plan.approve');
                Route::post('schedule-maintenance/ajax-my-approval', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'ajaxGetAllPlansForApproval']);
                Route::post('schedule-maintenance/approve_maintenance/{id}', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'updateApproval']);
                Route::get('schedule-maintenance/get-my-approval/{id}', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'getPlanForApproval']);
                // my assigned route
                Route::get('maintenance-dashboard', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'myDashboardPlan'])->name('myDashboardPlan');
                Route::post('my-latest-active-plans', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'myActivePlans'])->name('ajax.myActivePlans');
                Route::get('assigned-plan-list', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'myAllocatedPlan'])->name('myPlans');
                Route::post('my-allocated-plan-ajax', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'ajaxMyAllocatedPlan'])->name('ajax.myPlans');
                Route::get('my-plan-allocate-list-excel', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'exportMyAllocatedPlan'])->name('myPlan.export');
                Route::get('task-scheduling', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'cronGetAllocatedPlan'])->name('cron.allocate');
                Route::get('my-allocated-task-list/{id}', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'getAllocatedTaskList'])->name('allocated.task');
                Route::post('my-allocated-plan-update-status/{id}', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'updatePlanStatus'])->name('update.status');
                Route::post('my-allocated-task/update', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'updateMyTask'])->name('task.update');
                Route::post('get-allocated-plan/{id}', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'getAllocatedPlan'])->name('allocated.plan');
                Route::post('get-status-history/{id}', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'getStatusHistory'])->name('status.history');
                Route::get('get-task/{id}', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'getTask'])->name('get.task');
                Route::post('check-task/{id}', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'checkTask']);
                Route::post('task-history/{id}', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'getTaskHistory'])->name('task.history');
                Route::get('schedule-plan/calendar', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'getCalendarView'])->name('plan.calendar');
                Route::post('my-allocated-plan/calendar/{id}', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'getCalendarEvent'])->name('plan.calendar');
                Route::get('schedule-maintenance/device-listing', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'allAllocatedDevice'])->name('device');
                Route::post('schedule-maintenance/get-device-list', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'ajaxAllAllocatedDevice'])->name('ajax.device');
                Route::get('getDescriptionByAjax/{status_id}', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'getDescriptionByAjax'])->name('task');
                Route::get('task/delete/attachment/{id}', [App\Http\Controllers\ScheduleMaintenance\AllocatePlanController::class, 'deleteTaskAttachment']);
            });
        });

        Route::post('getDashboardData', [App\Http\Controllers\HomeController::class, 'getDashboardData'])->name('getDashboardData');
        Route::post('getDelegation', [App\Http\Controllers\UserController::class, 'getDelegation'])->name('getDelegation');
        Route::get('faq', [App\Http\Controllers\HomeController::class, 'faq']);
        Route::get('getUsersParameterBase', [App\Http\Controllers\UserController::class, 'getUsersParameterBase']);
    });

    //routes encloded for auth for making route secure
    Route::group(['middleware' => ['auth']], function () {
        Route::post('/ai-search', [App\Http\Controllers\Ticket\IndexController::class, 'searchAIResponse']);
        Route::get('statusBoardGroup', [App\Http\Controllers\StatusBoardController::class, 'statusBoardGroup']);
        Route::get('getCompanyUsersByQuery', [App\Http\Controllers\UserController::class, 'getCompanyUsersByQuery']);
        Route::get('getEscCompanyUsersByQuery', [App\Http\Controllers\Ticket\EscalationGroupController::class, 'getEscCompanyUsersByQuery']);
        Route::get('getCompanyUsersByDepartment', [App\Http\Controllers\UserController::class, 'getCompanyUsersByDepartment']);
        Route::get('getCompanyUsers', [App\Http\Controllers\UserController::class, 'getCompanyUsers']);
        Route::post('store-default-company', [App\Http\Controllers\UserController::class, 'storeDefaultCompany'])->name('user.store.default.company');
        Route::get('getCompanyByUserAccess', [App\Http\Controllers\UserController::class, 'getCompanyByUserAccess']);
        Route::get('getTicketAttendersByDepartment/{groupId?}', [App\Http\Controllers\UserController::class, 'getTicketAttendersByDepartment']);
        Route::get('getLocationByQuery', [App\Http\Controllers\Config\LocationController::class, 'getLocationByQuery']);
        Route::get('getDepartmentsByQuery', [App\Http\Controllers\Config\DepartmentController::class, 'getDepartmentsByQuery']);

        //Project Management
        Route::get('getProjectStatus', [App\Http\Controllers\ProjectManagement\StatusController::class, 'getProjectStatus']);
        Route::get('getProjectTags', [App\Http\Controllers\ProjectManagement\StatusController::class, 'getProjectTags']);
        Route::get('getUserGroup', [App\Http\Controllers\Ticket\CCEmailUserGroupController::class, 'getGroup']);
        Route::get('getProjects', [App\Http\Controllers\ProjectManagement\IndexController::class, 'getProjects']);

        Route::post('getChangeRequestCommentByQuery', [App\Http\Controllers\ChangeManagement\ListController::class, 'getChangeRequestCommentByQuery']);
        Route::post('getSupplierCommentByQuery/{id}', [App\Http\Controllers\SupplierController::class, 'getSupplierCommentByQuery']);
        Route::get('getApproverSR', [App\Http\Controllers\Ticket\RequestController::class, 'getApproverSR']);
        Route::get('getExceptUserForDropDown', [App\Http\Controllers\UserController::class, 'getExceptUserForDropDown']);

        Route::get('getInternalPlaceByAjax/{id}', [App\Http\Controllers\PlaceController::class, 'getInternalPlaceByAjax']);
        Route::post('getInternalPlaceByLocation/{id}', [App\Http\Controllers\PlaceController::class, 'getInternalPlaceByLocation']);
        Route::get('ajaxGetInternalPlace', [App\Http\Controllers\PlaceController::class, 'ajaxGetInternalPlace']);

        //Device Dropdown
        Route::get('getDeviceByQuery', [App\Http\Controllers\DeviceController::class, 'getDeviceByQuery']);
        Route::get('getNIDeviceByQuery', [App\Http\Controllers\DeviceController::class, 'getNIDeviceByQuery']);
        Route::get('getDeviceByStatus', [App\Http\Controllers\DeviceController::class, 'getDeviceByStatus']);
        Route::get('getDeviceForDropDown', [App\Http\Controllers\DeviceController::class, 'getDeviceForDropDown']);
        Route::get('getEnabledAccountsForOptions', [App\Http\Controllers\Ticket\TicketController::class,'getEnabledAccountsForOptions']);
        Route::get('getReasonsByQuery', [App\Http\Controllers\DeviceController::class, 'getReasonOptions']);
        Route::get('getDeviceRateDropDown/{device_id}', [App\Http\Controllers\DeviceController::class, 'getDeviceRateDropDown']);
        Route::get('getUserByLocation/{id}', [App\Http\Controllers\UserController::class, 'getUserByLocation']);


        // User and Location Dropdown
        Route::get('getUserByQuery', [App\Http\Controllers\UserController::class, 'getActiveUsersByQuery']);
        Route::get('getUserForDropDown', [App\Http\Controllers\UserController::class, 'getUserForDropDown']);
        Route::post('getUserCCByQuery', [App\Http\Controllers\UserController::class, 'getUsersCCByQuery']);
        Route::post('getUserNIByQuery', [App\Http\Controllers\UserController::class, 'getUserNIByQuery']);
        Route::get('getUserSeatNoByQuery', [App\Http\Controllers\UserController::class, 'getUsersSeatNoSelect']); //add url

        Route::get('getManagerByQuery', [App\Http\Controllers\UserController::class, 'getManagerByQuery']);
        Route::get('getStatusByQuery', [App\Http\Controllers\LabelController::class, 'getStatusByQuery']);
        Route::get('getCountryByQuery', [App\Http\Controllers\CountryController::class, 'getCountryByQuery']);
        Route::get('getStateByAjax/{id}', [App\Http\Controllers\StateController::class, 'getStateByAjax']);
        Route::get('getCityByAjax/{id}', [App\Http\Controllers\CityController::class, 'getCityByAjax']);
        Route::get('fetchStateByAjax', [App\Http\Controllers\StateController::class, 'fetchStateByAjax']);
        Route::get('fetchCityByAjax', [App\Http\Controllers\CityController::class, 'fetchCityByAjax']);

        //manufacture
        Route::get('manufactures', [App\Http\Controllers\Config\ManufactureController::class, 'getIndex']);
        Route::any('manufactures/ajax-get/{id}', [App\Http\Controllers\Config\ManufactureController::class, 'ajaxGetManufacturer']);
        Route::any('manufactures/ajax-get-list', [App\Http\Controllers\Config\ManufactureController::class, 'ajaxGetList']);
        Route::any('manufacturer/ajax-add', [App\Http\Controllers\Config\ManufactureController::class, 'ajaxAdd']);
        Route::any('manufacturer/ajax-edit/{id}', [App\Http\Controllers\Config\ManufactureController::class, 'ajaxEdit']);
        Route::any('manufacture/add', [App\Http\Controllers\Config\ManufactureController::class, 'addManufacture']);
        Route::any('manufacture/edit/{id}', [App\Http\Controllers\Config\ManufactureController::class, 'editManufacture']);
        Route::post('manufacturere/del-attachment/{id}', [App\Http\Controllers\Config\ManufactureController::class, 'deleteManufactureAttachment']);
        Route::get('delete-manufacture/{id}', [App\Http\Controllers\Config\ManufactureController::class, 'deleteManufacture']);
        Route::get('export-manufactures', [App\Http\Controllers\Config\ManufactureController::class, 'ManufactureExport']);
        Route::get('delete-manufacture/{id}', [App\Http\Controllers\Config\ManufactureController::class, 'deleteManufacture']);


        /* Asset Allocation Type */
        Route::get('asset_allocation_type', [App\Http\Controllers\Config\AllocationTypeController::class, 'allocationTypeList']);
        Route::post('jx-allocationtype-list', [App\Http\Controllers\Config\AllocationTypeController::class, 'ajaxAllocationTypeList']);
        Route::get('asset_allocation_type/delete/{id}', [App\Http\Controllers\Config\AllocationTypeController::class, 'deleteAllocationType']);
        Route::post('asset_allocation_type/createAllocationtype', [App\Http\Controllers\Config\AllocationTypeController::class, 'createAllocationType']);
        Route::get('asset_allocation_type/edit/{id}', [App\Http\Controllers\Config\AllocationTypeController::class, 'editAllocationType']);
        Route::post('asset_allocation_type/update', [App\Http\Controllers\Config\AllocationTypeController::class, 'updateAllocationType']);
        Route::get('export-allocation', [App\Http\Controllers\Config\AllocationTypeController::class, 'exportAllocationTypes']);

        // categories
        Route::get('categories', [App\Http\Controllers\Config\CategoryController::class, 'getIndex']);
        Route::any('category/add', [App\Http\Controllers\Config\CategoryController::class, 'addCategory']);
        Route::any('category/ajax-list', [App\Http\Controllers\Config\CategoryController::class, 'ajaxCategories']);
        Route::any('category/ajax-add', [App\Http\Controllers\Config\CategoryController::class, 'ajaxAddCategory']);
        Route::any('category/ajax-edit/{id}', [App\Http\Controllers\Config\CategoryController::class, 'ajaxEditCategory']);
        Route::any('category/ajax-get/{id}', [App\Http\Controllers\Config\CategoryController::class, 'ajaxGetCategory']);
        Route::any('category/edit/{id}', [App\Http\Controllers\Config\CategoryController::class, 'editCategory']);
        Route::get('delete-category/{id}', [App\Http\Controllers\Config\CategoryController::class, 'deleteCategory']);
        Route::get('export-category', [App\Http\Controllers\Config\CategoryController::class, 'CategoryExport']);
        Route::get('category/history/{id}', [App\Http\Controllers\Config\CategoryController::class, 'categoryHistory']);
        Route::post('jx_categories_info', [App\Http\Controllers\Config\CategoryController::class, 'ajaxCategoryHistory']);

        Route::get('getAssetDepartments', [App\Http\Controllers\Config\DepartmentController::class, 'getAssetDepartments'])->name('getAssetDepartments');
        Route::get('getCustomFieldsetByModule/{id}', [App\Http\Controllers\CustomFieldController::class, 'getCustomFieldsetByModule']);

        /* Departments */
        Route::get('departments', [App\Http\Controllers\Config\DepartmentController::class, 'getIndex']);
        Route::get('department-attenders/{dept_id}', [App\Http\Controllers\Config\DepartmentController::class, 'getAttenders']);
        Route::get('jx-departments', [App\Http\Controllers\Config\DepartmentController::class, 'ajaxDepartment']);
        Route::any('department/add', [App\Http\Controllers\Config\DepartmentController::class, 'addDepartment']);
        Route::any('department/ajaxadd', [App\Http\Controllers\Config\DepartmentController::class, 'ajaxAddDepartment']);
        Route::any('department/ajaxedit/{id}', [App\Http\Controllers\Config\DepartmentController::class, 'ajaxEditDepartment']);
        Route::any('department/ajaxget/{id}', [App\Http\Controllers\Config\DepartmentController::class, 'ajaxGetDepartment']);
        Route::any('department/edit/{id}', [App\Http\Controllers\Config\DepartmentController::class, 'editDepartment']);
        Route::any('department/detail/{id}', [App\Http\Controllers\Config\DepartmentController::class, 'detailDepartment']);
        Route::any('department/delete/{id}', [App\Http\Controllers\Config\DepartmentController::class, 'deleteDepartment']);
        Route::get('export-departments', [App\Http\Controllers\Config\DepartmentController::class, 'departmentExport']);
        Route::get('getAssetDepartments', [App\Http\Controllers\Config\DepartmentController::class, 'getAssetDepartments'])->name('getAssetDepartments');
        Route::any('department-import', [App\Http\Controllers\Config\DepartmentController::class, 'departmentImport']);
        Route::get('department/getSubCategories', [App\Http\Controllers\Config\DepartmentController::class, 'getSubCategories']);
        Route::any('department/bulk-import-info', [App\Http\Controllers\Bulk\ConfigBulkActionController::class, 'ajaxBulkImportDepartmentInfo']);

        // email log routes
        Route::get('emails-details', [App\Http\Controllers\EmailLogController::class, 'index']);
        Route::post('jx-emails-details', [App\Http\Controllers\EmailLogController::class, 'getEmailDetails']);
        Route::get('export-email-details', [App\Http\Controllers\EmailLogController::class, 'downloadEmailDetails']);

    });

    Route::get('/', function () {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        } else {
            return redirect()->route('login');
        }
    });

    Route::group(['middleware' => ['auth']], function() {
        Route::get('getCompanyUsersByQuery', [App\Http\Controllers\UserController::class, 'getCompanyUsersByQuery']);
        Route::get('getCompanyUsersByQuery', [App\Http\Controllers\UserController::class, 'getCompanyUsersByQuery']);
        Route::get('getCompanyByUserAccess', [App\Http\Controllers\UserController::class, 'getCompanyByUserAccess']);
        Route::post('store-default-company', [App\Http\Controllers\UserController::class, 'storeDefaultCompany'])->name('user.store.default.company');
        Route::get('/view-secure-pdf/{name}', function ($name) {
                    $name = decrypt($name);
                    return view('news-letter.pdf_viewer',compact('name'));
                })->name('secure.pdf');

        // Call Route
        Route::post('/call/start',[App\Http\Controllers\CallSession\CallController::class,'startCall']);
        Route::post('call/accept', [App\Http\Controllers\CallSession\CallController::class, 'acceptCall']);
        Route::post('call/reject', [App\Http\Controllers\CallSession\CallController::class, 'rejectCall']);
        Route::post('call/signal', [App\Http\Controllers\CallSession\CallController::class,'sendSignal']);
        Route::post('/call/end', [App\Http\Controllers\CallSession\CallController::class, 'endCall']);
    });
    Route::get('getDepartmentsWithCompanyByQuery', [App\Http\Controllers\Config\DepartmentController::class, 'getDepartmentsWithCompanyByQuery']);
    Route::get('ajax-roles', [App\Http\Controllers\RolesPermissionController::class, 'ajaxRoles']);
    Route::get('getUserDeviceForDropDown', [App\Http\Controllers\DeviceController::class, 'getUserDeviceForDropDown']);
    Route::post('getUsersEmailSelect', [App\Http\Controllers\UserController::class, 'getUsersEmailSelect']);
});

Route::get('/clear-cache', function() {
    $config = Artisan::call('config:cache');
    $cache = Artisan::call('cache:clear');
    $view = Artisan::call('view:clear');
    $con = Artisan::call('config:clear');
    $permission = Artisan::call('permission:cache-reset');
    echo 'clear-cache';
    die();
});
/* Auth Routes end */
