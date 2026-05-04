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

    'yandex' => [
        'client_id' => env('YANDEX_CLIENT_ID'),
        'client_secret' => env('YANDEX_CLIENT_SECRET'),
        'redirect' => env('YANDEX_REDIRECT_URI'),
    ],

    'vkontakte' => [
        'client_id' => env('VK_CLIENT_ID'),
        'client_secret' => env('VK_CLIENT_SECRET'),
        'redirect' => env('VK_REDIRECT_URI'),
    ],

    'plusofon' => [
        'api_url' => env('PLUSOFON_API_URL'),
        'token' => env('PLUSOFON_API_TOKEN'),
        'sender' => env('PLUSOFON_SENDER'),
    ],

    'plusofon_flash_call' => [
        'enabled' => (bool) env('PLUSOFON_FLASH_CALL_ENABLED', false),
        'base_url' => env('PLUSOFON_FLASH_CALL_BASE_URL', 'https://restapi.plusofon.ru/api/v1'),
        'token' => env('PLUSOFON_FLASH_CALL_TOKEN', env('PLUSOFON_API_TOKEN')),
        'client_id' => env('PLUSOFON_FLASH_CALL_CLIENT', '10553'),
    ],

];
