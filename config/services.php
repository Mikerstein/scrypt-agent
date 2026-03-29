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


    'twitter' => [
    'client_id'     => env('TWITTER_CLIENT_ID'),
    'client_secret' => env('TWITTER_CLIENT_SECRET'),
    'bearer_token'  => env('TWITTER_BEARER_TOKEN'),
    'access_token'  => env('TWITTER_ACCESS_TOKEN'),
    'access_secret' => env('TWITTER_ACCESS_SECRET'),
],

'linkedin' => [
    'client_id'     => env('LINKEDIN_CLIENT_ID'),
    'client_secret' => env('LINKEDIN_CLIENT_SECRET'),
    'access_token'  => env('LINKEDIN_ACCESS_TOKEN'),
    'person_id'     => env('LINKEDIN_PERSON_ID'),
],


];
