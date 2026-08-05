<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => rtrim(env('APP_URL'), '/') . '/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        'storage_tkt' => [
            'driver' => 'local',
            'root' => storage_path('tkt_attachments'),
            'url' => env('APP_URL').'/tkt_attachments',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

        'avatar' => [
            'driver' => 'local',
            'root' => storage_path('app/public/avatar'),
            'url' => env('APP_URL') . '/storage/avatar',
            'visibility' => 'public',
        ],
        'documents' => [
            'driver' => 'local',
            'root' => public_path('/uploads/documents'),
            'url' => env('APP_URL') . '/uploads/documents',
            'visibility' => 'public',
        ],
        'storage_documents' => [
            'driver' => 'local',
            'root' => storage_path('app/public/uploads/documents'),
            'url' => env('APP_URL') . '/storage/uploads/documents',
            'visibility' => 'public',
        ],
        'bulk_documents' => [
            'driver' => 'local',
            'root' => public_path('/uploads/bulk_documents'),
            'url' => env('APP_URL') . '/uploads/bulk_documents',
            'visibility' => 'public',
        ],
        'category' => [
            'driver' => 'local',
            'root' => public_path('uploads/category'),
            'visibility' => 'public',
        ],
        'model' => [
            'driver' => 'local',
            'root' => public_path('uploads/models'),
            // 'url' => env('APP_URL').'/storage/models',
            'visibility' => 'public',
        ],

        'supplier' => [
            'driver' => 'local',
            'root' => storage_path('supplier'),
            'visibility' => 'private',
        ],
        'purchase_attachments' => [
            'driver' => 'local',
            'root' => public_path('purchase_attachments'),
            'url' => env('APP_URL') . '/purchase_attachments',
            'visibility' => 'private'
        ],

        'tickets' => [
            'driver' => 'local',
            'root' => storage_path('tkt_attachments'),
            'visibility' => 'private',
        ],
        'procurements' => [
            'driver' => 'local',
            'root' => storage_path('procurements'),
            'visibility' => 'private',
        ],
        'kd_attach' => [
            'driver' => 'local',
            'root' => storage_path('kd_attachments'),
            'visibility' => 'private',
        ],
        'tkt_incident' => [
            'driver' => 'local',
            'root' => storage_path('tkt_incident'),
            'visibility' => 'private',
        ],
        'schedulars' => [
            'driver' => 'local',
            'root' => storage_path('schedular_attachments'),
            'visibility' => 'private',
        ],
        'agent' => [
            'driver' => 'local',
            'root' => storage_path('agent'),
            'visibility' => 'private',
        ],

        'task_management' => [
            'driver' => 'local',
            'root' => storage_path('task_management'),
            'visibility' => 'private',
        ],

        'task' => [
            'driver' => 'local',
            'root' => public_path('uploads/task'),
            'visibility' => 'public',
        ],
        'plan_guide' => [
            'driver' => 'local',
            'root' => public_path('uploads/plan_guide'),
            'visibility' => 'public',
        ],
        'license_expire' => [
            'driver' => 'local',
            'root' => storage_path('license_expire'),
            'visibility' => 'private',
        ],
        'cm_attach' => [
            'driver' => 'local',
            'root' => storage_path('cm_attachments'),
            'visibility' => 'private',
        ],
        'not_detected_devices' => [
            'driver' => 'local',
            'root' => storage_path('not_detected_devices'). "/" .date("Y") . "/" . date('m') . "/" .date('d'),
            'visibility' => 'private',
        ],
        'assigned_devices' => [
            'driver' => 'local',
            'root' => storage_path('assigned_devices'). "/" .date("Y") . "/" . date('m') . "/" .date('d'),
            'visibility' => 'public',
        ],
        'category_threshold' => [
            'driver' => 'local',
            'root' => storage_path('category_threshold'). "/" .date("Y") . "/" . date('m') . "/" .date('d'),
            'visibility' => 'private',
        ],
        'black_listed_report' => [
            'driver' => 'local',
            'root' => storage_path('black_listed_report'),
            'visibility' => 'private',
        ],
        'project_attachment' => [
            'driver' => 'local',
            'root' => public_path('uploads/project'),
            'visibility' => 'private',
        ],
        'sw_patch' => [
            'driver' => 'local',
            'root' => storage_path('sw_patch'),
            'visibility' => 'private',
        ],
        'patch_master' => [
            'driver' => 'local',
            'root' => storage_path('patch_master'),
            'visibility' => 'private',
        ],
        'empty_os_model_devices' => [
            'driver' => 'local',
            'root' => storage_path('empty_os_model_devices'). "/" .date("Y") . "/" . date('m') . "/" .date('d'),
            'visibility' => 'private',
        ],
        'device_monthly_report' => [
            'driver' => 'local',
            'root' => storage_path('device_monthly_report'). "/" .date("Y") . "/" . date('m') . "/" .date('d'),
            'visibility' => 'private',
        ],
        'new_patch_detected_devices' => [
            'driver' => 'local',
            'root' => storage_path('new_patch_detected_devices'). "/" .date("Y") . "/" . date('m') . "/" .date('d'),
            'visibility' => 'private',
        ],
        'user_pending_asset' => [
            'driver' => 'local',
            'root' => storage_path('user_pending_asset'). "/" .date("Y") . "/" . date('m') . "/" .date('d'),
            'visibility' => 'private',
        ],
        'threshold' => [
            'driver' => 'local',
            'root' => storage_path('threshold'). "/" .date("Y") . "/" . date('m') . "/" .date('d', strtotime('+1 day')),
            'visibility' => 'private',
        ],
                'tkt_attachment' => [
            'driver' => 'local',
            'root' => 'C:\xampp_7.4\htdocs\custom\tkt_attachments',
            'url' => env('APP_URL') . '/tkt_attachments',
            'visibility' => 'public',
        ],
        'trvl_triangle_download' => [
            'driver' => 'local',
            'root' => storage_path('downloads'),
            'url' => env('APP_URL').'/storage/downloads',
            'visibility' => 'private',
        ],
        'device_audits' => [
            'driver' => 'local',
            'root' => public_path('/uploads/device_audits'),
            'url' => env('APP_URL').'/uploads/device_audits',
            'visibility' => 'public',
        ],
        'task_upload' => [
            'driver' => 'local',
            'root' => public_path('/uploads/task_upload'),
            'url' => env('APP_URL').'/uploads/task_upload',
            'visibility' => 'public',
        ],

        'uploads' => [
            'driver' => 'local',
            'root' => public_path('uploads'),
            'url' => env('APP_URL').'/uploads',
            'visibility' => 'public',
        ],
        'device' => [
            'driver' => 'local',
            'root' => public_path('uploads/devices'),
            // 'url' => env('APP_URL').'/storage/models',
            'visibility' => 'public',
        ],

        'article' => [
            'driver' => 'local',
            'root' => public_path('uploads/article'),
            'url' => env('APP_URL') . '/uploads/article',
            'visibility' => 'public',
        ],
        'purchase' => [
            'driver' => 'local',
            'root' => storage_path('purchase'),
            'visibility' => 'private',
        ],

        'tkt_customcard' => [
            'driver' => 'local',
            'root' => storage_path('tkt_customcard'),
            'visibility' => 'private',
        ],

        'scheduled_maintenance' => [
            'driver' => 'local',
            'root' => storage_path('scheduled_maintenance'),
            'visibility' => 'private',
        ],

        'device_amc' => [
            'driver' => 'local',
            'root' => storage_path('device_amc'),
            'visibility' => 'private',
        ],
        'device_warranty' => [
            'driver' => 'local',
            'root' => storage_path('device_warranty'),
            'visibility' => 'private',
        ],
        'consumables_uploads' => [
            'driver' => 'local',
            'root' => storage_path('app/public/uploads'),
            'url' => env('APP_URL').'/uploads',
            'visibility' => 'public',
        ],
        'settings' => [
            'driver' => 'local',
            'root' => storage_path('app/settings'),
            'throw' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
