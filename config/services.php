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

    'midtrans' => [
        'merchant_id' => env('MIDTRANS_MERCHANT_ID'),
        'client_key' => env('MIDTRANS_CLIENT_KEY'),
        'server_key' => env('MIDTRANS_SERVER_KEY'),
        'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    ],

    'google_maps' => [
        'web_api_key' => env('GOOGLE_MAPS_WEB_API_KEY'),
        'server_api_key' => env('GOOGLE_MAPS_SERVER_API_KEY'),
        'android_api_key' => env('GOOGLE_MAPS_ANDROID_API_KEY'),
        'default_lat' => env('GOOGLE_MAPS_DEFAULT_LAT', -0.02633000),
        'default_lng' => env('GOOGLE_MAPS_DEFAULT_LNG', 109.34250000),
        'default_zoom' => env('GOOGLE_MAPS_DEFAULT_ZOOM', 13),
    ],

];
