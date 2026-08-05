<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'warrenty' => [
        'dell' => [
            'url' => 'https://sandbox.api.dell.com/support/assetinfo/v4/getassetwarranty',
            'key' => env('WARRENTY_DELL_KEY')
        ],
        'lenovo' => [
            'url' => 'https://supportapi.lenovo.com/V2.5/Warranty'
        ]
    ],

    'analytics' => [
        'google' => env('GOOGLE_ANANLYTICS_ENABLED', 1)
    ],

    'network_inventory' => [
        'enabled' => env('NETWORK_INVENTORY_ENABLED', 0),
        'agent_token' => env('NETWORK_INVENTORY_AGENT_TOKEN')
    ],

    'procurement_module' => [
        'enabled' => env('PROCUREMENT_MODULE', 0),
        'procureTeamLimit' => env('PROCUREMENT_TEAM_LIMIT', 0)
    ],

    'change_module' => [
        'enabled' => env('CHANGE_MODULE', 0)
    ],

    'task_module' => [
        'enabled' => env('TASK_MODULE', 0)
    ],

    'service_ticket' => [
        'enabled' => env('SERVICE_TICKET', 0),
        'service_ticket_user_limit' => env('SERVICE_TICKET_USER_LIMIT', 10)
    ],

    'knowledge_document' => [
        'enabled' => env('KNOWLEDGE_DOCUMENT_MODULE', 0)
    ],

    'fcm' => [
        'key' => env('FCM_KEY', 0)
    ],

    'rdp' => [
        'enabled' => env('RDP_ENABLED', 0),
        'url' => env('RDP_URL', ''),
        'username' => env('RDP_USERNAME', ''),
        'password' => env('RDP_PASSWORD', ''),
        'group_id' => env('RDP_GROUP_ID', ''),
    ],

    'assets' => [
        'enabled' => env('ASSETS', 0),
        'asset_limit' => env('ASSET_LIMIT', 500),
        'rfid_integration' => env('RFID_INTEGRATION', false),
    ],

    'live_monitor' => [
        'enabled' => env('LIVE_MONITOR_ENABLED', 0),
        'license' => env('LIVE_MONITOR_WEBSITE_LIMIT', 0),
    ],

    'patch_management' => [
        'enabled' => env('Patch_Management', 0)
    ],

    'azure' => [
        'client_id' => env('OFFICE365_APP_ID'),
        'client_secret' => env('OFFICE365_SECRET_APP_KEY'),
        'redirect' => env('OFFICE365_REDIRECT_URI'),
        'app_redirect' => env('OFFICE365_APP_REDIRECT_URI'),
        'tenant' => env('OFFICE365_TENANT_ID'),
        'logout_url' => 'https://login.microsoftonline.com/'.env('OFFICE365_TENANT_ID').'/oauth2/v2.0/logout?post_logout_redirect_uri=',
        'group_ids' => env('OFFICE365_GROUP_ID'),
        'user_group_id' => env('OFFICE365_USER_GROUP_ID', null),
        'expire_date' => env('OFFICE365_EXPIRE_DATE', null),
    ],

    'azure_multi' => [
        'client_id' => env('OFFICE365_APP_ID_MULTI', ""),
        'client_secret' => env('OFFICE365_SECRET_APP_KEY_MULTI', ""),
        'tenant' => env('OFFICE365_TENANT_ID_MULTI', "")
    ],

    'microsoft' => [
        'client_id' => env('OFFICE365_APP_ID'),
        'client_secret' => env('OFFICE365_SECRET_APP_KEY'),
        'redirect' => env('OFFICE365_REDIRECT_URI'),
        'tenant' => env('OFFICE365_TENANT_ID'),
    ],

    'status_board' => [
        'enabled' => env('STATUS_BOARD', 0)
    ],

    'bot' => [
        'enabled' => env('BOT_ENABLE', 0)
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT'),
        'domain' => env('GOOGLE_DOMAIN'),
        'google_workspace_mail' => env('GOOGLE_WORKSPACE_MAIL')
    ],

    'compliance_dashboard' => [
        'enabled' => env('COMPLIANCE_DASHBOARD', 0)
    ],

    'project_management' => [
        'enabled' => env('PROJECT_MANAGEMENT', 0)
    ],

    'powerbi_report' => [
        'enabled' => env('POWERBI_REPORT', 0)
    ],

    'mailroom_management' => [
        'enabled' => env('MAILROOM_MANAGEMENT', 0)
    ],

    'gatepass' => [
        'enabled' => env('GATEPASS', 0),
    ],

    'software_tracking' => [
        'enabled' => env('SOFTWARE_TRACKING', 0),
    ],

    'multiple_company' =>[
        'enabled'=> env('MULTIPLE_COMPANY',0),
    ],

];
