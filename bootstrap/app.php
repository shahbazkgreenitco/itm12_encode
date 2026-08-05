<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    ->withSchedule(function (Schedule $schedule) {
        app(\App\Console\Kernel::class)->schedule($schedule);
    })

    ->withBroadcasting(
        __DIR__ . '/../routes/channels.php'
    )

    ->withMiddleware(function (Middleware $middleware): void {

        /*
        |--------------------------------------------------------------------------
        | Global Middleware (old $middleware)
        |--------------------------------------------------------------------------
        */
        $middleware->use([
            \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
            \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
            \App\Http\Middleware\TrimStrings::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
            \App\Http\Middleware\TrustProxies::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Middleware Groups (old $middlewareGroups)
        |--------------------------------------------------------------------------
        */
        $middleware->group('web', [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\XFrameHeadersMiddleware::class,
        ]);

        $middleware->group('api', [
            'throttle:50000,1',
            'bindings',
            'api.track',
            'api.language',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Route Middleware Aliases (old $routeMiddleware)
        |--------------------------------------------------------------------------
        */
        $middleware->alias([
            'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
            'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
            'can' => \Illuminate\Auth\Middleware\Authorize::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'auth.check' => \App\Http\Middleware\AuthCheck::class,
            'auth.role' => \App\Http\Middleware\CheckRole::class,
            'auth.app_token' => \App\Http\Middleware\CheckAppToken::class,
            'auth.access_token' => \App\Http\Middleware\CheckAccessToken::class,
            'auth.ni_agent' => \App\Http\Middleware\CheckNetworkInventoryAgentToken::class,
            'auth.service_ticket' => \App\Http\Middleware\CheckServiceTicketModule::class,
            'api.track' => \App\Http\Middleware\ApiTrack::class,
            'api.language' => \App\Http\Middleware\ApiLocalization::class,
            'language' => \App\Http\Middleware\Localization::class,
            'revalidate' => \App\Http\Middleware\RevalidateBackHistory::class,
            'user_session_follower' => \App\Http\Middleware\UserSessionFollower::class,
            '2fa' => \App\Http\Middleware\Check2FA::class,
            'Check2FA' => \App\Http\Middleware\Check2FA::class,
            'verify.user_trusted_device' => \App\Http\Middleware\VerifyUserTrustedDevice::class,
            'client.route_restrict' => \App\Http\Middleware\ClientBasedRouteRestriction::class,
            'client.plugin_verify' => \App\Http\Middleware\VerifyPluginClientAccess::class,
            'report_http_status' => \App\Http\Middleware\ReportHttpStatus::class,
            'littlegatekeeper' => \Spatie\LittleGateKeeper\AuthMiddleware::class,
            // 'Image' => Intervention\Image\Facades\Image::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
