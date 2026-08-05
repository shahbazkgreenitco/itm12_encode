<?php

use Illuminate\Support\Facades\Crypt;

Route::get('support', function () {
    $token = config('app.app_token');
    $encryptedToken = Crypt::encryptString($token);
    return view('tickets.Support.support', compact('encryptedToken'));
});
Route::get('iframe', function () {
    return view('tickets.Support.iframe');
});
Route::post('support/submit', [App\Http\Controllers\Auth\Api\Ticket\TktPluginController::class, 'support']);

Route::group(['middleware' => ['language']], function () {
        Route::get('typography', [App\Http\Controllers\HomeController::class, 'typography'])->name('typograpghy');

        Route::any('login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login');
        Route::any('admin/login', [App\Http\Controllers\Auth\LoginController::class, 'adminLogin'])->name('adminLogin');
        Route::any('admin/validate-username/{userName}', [App\Http\Controllers\Auth\LoginController::class, 'validateUserName']);
        Route::get('login-with-google', [App\Http\Controllers\UserController::class, 'loginWithGoogle'])->name('loginWithGoogle');
        Route::any('resume-from-google', [App\Http\Controllers\UserController::class, 'resumeFromGoogle']);
        Route::get('google-login', [App\Http\Controllers\UserController::class, 'googleLogin']);
        Route::any('login-with-office', [App\Http\Controllers\UserController::class, 'loginWithOffice'])->name('loginWithOffice');
        Route::any('resume-from-office', [App\Http\Controllers\UserController::class, 'resumeFromOffice']);
        Route::any('resume-from-office-multiple', [App\Http\Controllers\UserController::class, 'resumeFromOfficeMultiple']);
        Route::any('forgot-password', [App\Http\Controllers\Auth\CustomPasswordController::class, 'reset']);
        Route::get('password/reset/{token?}/{id?}', [App\Http\Controllers\Auth\CustomPasswordController::class, 'showResetForm']);
        Route::post('/jx-password-update', [App\Http\Controllers\Auth\CustomPasswordController::class, 'update']);
        Route::get('dashboard', [App\Http\Controllers\HomeController::class, 'newdashboard'])->name('dashboard');
        Route::get('logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
        Route::get('2fa', [App\Http\Controllers\TwoFAController::class, 'index'])->name('2fa.index');
        Route::post('2fa', [App\Http\Controllers\TwoFAController::class, 'store'])->name('2fa.post');
        Route::get('2fa/reset/{userId}/{imposonateId?}', [App\Http\Controllers\TwoFAController::class, 'resend'])->name('2fa.resend');


        Route::get('/', function () {
            if (Auth::check()) {
                return redirect()->action([App\Http\Controllers\HomeController::class, 'newdashboard']);
            } else {
                return redirect()->to('login');
            }
        });

        /* Typography */
        Route::get('typography', [App\Http\Controllers\HomeController::class, 'typography'])->name('typograpghy');
        Route::group(['middleware' => ['auth']], function () {
            Route::any('set-locale/{locale}', [App\Http\Controllers\UserController::class, 'setLocale'])->name('setLocale');
            Route::get('versions', [App\Http\Controllers\VersionController::class, 'versions'])->name('versions');
        });

        Route::group(['middleware' => ['auth', 'revalidate', 'user_session_follower']], function () {
            Route::get('make-logout/{id}/{session_id}', [App\Http\Controllers\UserController::class, 'makeSessionOutForUser'])->name('makeLogout');
            Route::get('users', [App\Http\Controllers\UserController::class, 'getIndex'])->name('users-list');
            Route::post('users-list', [App\Http\Controllers\UserController::class, 'ajaxIndex']);
            Route::any('user/info/{id}', [App\Http\Controllers\UserController::class, 'userDetail']);
            Route::get('user/basic-info/{id}/{level?}', [App\Http\Controllers\UserController::class, 'basicInfo']);
            Route::get('user-permissions', [App\Http\Controllers\RolesPermissionController::class, 'userPermissions'])->name('userPermissions');
            Route::post('update-user-permission', [App\Http\Controllers\RolesPermissionController::class, 'updateUserPermissions'])->name('updateUserPermissions');

            Route::get('getAssetDepartments', [App\Http\Controllers\DepartmentController::class, 'getAssetDepartments'])->name('getAssetDepartments');

            // User Profile
            Route::get('profile', [App\Http\Controllers\ProfileController::class, 'getIndex'])->name('profile.index');
            Route::any('profile/edit', [App\Http\Controllers\ProfileController::class, 'editProfile']);
            Route::any('profile/change-password', [App\Http\Controllers\ProfileController::class, 'changePassword']);
            Route::post('user-qrcode', [App\Http\Controllers\UserController::class, 'userQrcode'])->name('user.qrcode');
            Route::post('profile/avatar-upload', [App\Http\Controllers\ProfileController::class, 'uploadAvatar'])->name('profile.avatar.upload');


            /** Users Roles Routes */
            Route::get('roles-permission', [App\Http\Controllers\RolesPermissionController::class, 'index'])->name('role-permission');
            Route::post('get-roles', [App\Http\Controllers\RolesPermissionController::class, 'getAllRoles'])->name('getRoles');
            Route::get('edit-role/{roleId}', [App\Http\Controllers\RolesPermissionController::class, 'roleView'])->name('roleView');
            Route::post('update-roles', [App\Http\Controllers\RolesPermissionController::class, 'updateRole'])->name('updateRole');
            Route::post('add-role', [App\Http\Controllers\RolesPermissionController::class, 'addRole']);
            Route::get('delete-role/{roleId}', [App\Http\Controllers\RolesPermissionController::class, 'deleteRole'])->name('deleteRole');
            Route::post('clone-role', [App\Http\Controllers\RolesPermissionController::class, 'cloneRole']);
        });
    }
);

/* Auth Routes end */
