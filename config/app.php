<?php

use Illuminate\Support\Facades\Facade;

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application, which will be used when the
    | framework needs to place the application's name in a notification or
    | other UI elements where an application name needs to be displayed.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    'client' => env('APP_CLIENT', 'rolepermission'),
    'sub_client' => env('SUB_CLIENT', ''),
    'version' => env('CODE_VERSION', '2.5.12.31'),
    'demo_site' => env('DEMO_SITE', ''),

    'impersonate' => env('IMPORSANATE', false),
    'impersonate_un' => env('IMPORSANATE_UN', ''),
    'impersonate_pw' => env('IMPORSANATE_PW', ''),

    'user_acceptance' => env('USER_ACCEPTANCE', false),
    'direct_accept_link' => env('DIRECT_ACCEPT_LINK', false),
    'direct_accept_link_type' => env('DIRECT_ACCEPT_LINK_TYPE', 2),

    'socket_notification' => env('SOCKET_NOTIFICATION', false),
    'socket_enabled' => env('SOCKET_ENABLED', false),
    'requestable_enabled' => env('REQUESTABLE_ENABLED', false),

    'modules' => [
        '3' => env('ASSETS'),
        '4' => env('NETWORK_INVENTORY_ENABLED'),
        '10' => env('STATUS_BOARD'),
        '11' => env('SERVICE_TICKET'),
        '19' => env('CHANGE_MODULE'),
        '20' => env('TASK_MODULE'),
        '21' => env('KNOWLEDGE_DOCUMENT_MODULE'),
        '28' => env('Patch_Management'),
        '29' => env('LIVE_MONITOR_ENABLED')
    ],

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | the application so that it's available within Artisan commands.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. The timezone
    | is set to "UTC" by default as it is suitable for most use cases.
    |
    */

    'timezone' => env('TIMEZONE', 'UTC'),
    'date_format' => env('APP_DATE_FORMAT', 'd M Y'),
    'time_format' => env('APP_TIME_FORMAT', 'h:i A'),

    'excel_date_format' => env('EXCEL_DATE_FORMAT', 'd M Y'),
    'excel_time_format' => env('EXCEL_TIME_FORMAT', 'h:i A'),
    'gemini_ai_key' => env('GEMINI_AI_KEY', ''),
    'ai_enabled' => env('AI_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by Laravel's translation / localization methods. This option can be
    | set to any locale for which you plan to have translation strings.
    |
    */

    'locale' => env('APP_LOCALE', 'en'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    // 'available_locale' => ['en', 'it', 'ar', 'de' , 'gu'],

    'available_locale' => [
        'en' => 'assets/lang/en.png',
        'it' => 'assets/lang/it.png',
        'ar' => 'assets/lang/ar.png',
        'de' => 'assets/lang/de.png',
        'gu' => 'assets/lang/gu.png',
    ],

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is utilized by Laravel's encryption services and should be set
    | to a random, 32 character string to ensure that all encrypted values
    | are secure. You should do this prior to deploying the application.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

    'aliases' => Facade::defaultAliases()->merge([
        'App' => Illuminate\Support\Facades\App::class,
        'Artisan' => Illuminate\Support\Facades\Artisan::class,
        'Auth' => Illuminate\Support\Facades\Auth::class,
        'Blade' => Illuminate\Support\Facades\Blade::class,
        'Broadcast' => Illuminate\Support\Facades\Broadcast::class,
        'Bus' => Illuminate\Support\Facades\Bus::class,
        'Cache' => Illuminate\Support\Facades\Cache::class,
        'Config' => Illuminate\Support\Facades\Config::class,
        'Cookie' => Illuminate\Support\Facades\Cookie::class,
        'Crypt' => Illuminate\Support\Facades\Crypt::class,
        'DB' => Illuminate\Support\Facades\DB::class,
        'Eloquent' => Illuminate\Database\Eloquent\Model::class,
        'Event' => Illuminate\Support\Facades\Event::class,
        'File' => Illuminate\Support\Facades\File::class,
        'Gate' => Illuminate\Support\Facades\Gate::class,
        'Hash' => Illuminate\Support\Facades\Hash::class,
        'Lang' => Illuminate\Support\Facades\Lang::class,
        'Log' => Illuminate\Support\Facades\Log::class,
        'Mail' => Illuminate\Support\Facades\Mail::class,
        'Notification' => Illuminate\Support\Facades\Notification::class,
        'Password' => Illuminate\Support\Facades\Password::class,
        'Queue' => Illuminate\Support\Facades\Queue::class,
        'Redirect' => Illuminate\Support\Facades\Redirect::class,
        'Redis' => Illuminate\Support\Facades\Redis::class,
        'Request' => Illuminate\Support\Facades\Request::class,
        'Response' => Illuminate\Support\Facades\Response::class,
        'Route' => Illuminate\Support\Facades\Route::class,
        'Schema' => Illuminate\Support\Facades\Schema::class,
        'Session' => Illuminate\Support\Facades\Session::class,
        'Storage' => Illuminate\Support\Facades\Storage::class,
        'URL' => Illuminate\Support\Facades\URL::class,
        'Validator' => Illuminate\Support\Facades\Validator::class,
        'View' => Illuminate\Support\Facades\View::class,
        'CommonHelper' => App\Helpers\Common::class,
        'PDF' => Spatie\LaravelPdf\Facades\Pdf::class,
    ])->toArray(),
];
