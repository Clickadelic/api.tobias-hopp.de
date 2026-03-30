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

'unsplash' => [
        'access_key' => env('UNSPLASH_ACCESS_KEY'),
        'secret' => env('UNSPLASH_SECRET'),
        'callback_url' => env('UNSPLASH_CALLBACK_URL'),
        'access_token' => env('UNSPLASH_ACCESS_TOKEN'),
        'refresh_token' => env('UNSPLASH_REFRESH_TOKEN'),
        'access_token_expires_at' => env('UNSPLASH_ACCESS_TOKEN_EXPIRES_AT'),
        'utm_source' => env('UNSPLASH_UTM_SOURCE', env('APP_NAME', 'api_app')),
        'collection_ids' => array_values(
            array_filter(
                array_map('trim', explode(',', (string) env('UNSPLASH_COLLECTION_IDS', '')))
            )
        ),
        'seasonal' => [
            'spring' => env('UNSPLASH_COLLECTION_SPRING_ID'),
            'summer' => env('UNSPLASH_COLLECTION_SUMMER_ID'),
            'autumn' => env('UNSPLASH_COLLECTION_AUTUMN_ID'),
            'winter' => env('UNSPLASH_COLLECTION_WINTER_ID'),
        ],
    ],

];
