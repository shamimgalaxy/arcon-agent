<?php

return [

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

  'circle' => [
    'base_url'            => env('CIRCLE_BASE_URL', 'https://api.circle.com'),
    'api_key'             => env('CIRCLE_API_KEY'),
    'entity_secret'       => env('CIRCLE_ENTITY_SECRET'),
    'default_blockchain'  => env('CIRCLE_DEFAULT_BLOCKCHAIN', 'ARC-TESTNET'),
],

];