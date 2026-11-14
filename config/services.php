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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // Optional paid remove.bg — if it fails, free local GD remover is used automatically
    'removebg' => [
        'key' => env('REMOVEBG_API_KEY'),
    ],

    // Optional free self-hosted remover (rembg / withoutBG Docker). Example:
    // BG_REMOVAL_URL=http://127.0.0.1:8000/v1/remove-background
    'bg_removal' => [
        'url' => env('BG_REMOVAL_URL'),
    ],

    /*
    | GSTIN auto-fill (Add Vendor / Customer). Same providers as erp-acshop.
    | ClearTax free lookup by default; optional Clear API token / Appyflow.
    */
    'gst_lookup' => [
        'clear_api_url' => env('GST_CLEAR_API_URL', 'https://api.clear.in/gst/api/v0.2/taxable_entities/123456/gstin_verification'),
        'clear_api_token' => env('GST_CLEAR_API_TOKEN', '3.ebf2623c-44a2-49c8-8e11-aefa342f60f3_a1cfc03847ba734d20b6f9c1f80018474999aec0809348357afe263a98a99267'),
        'cleartax_url' => env('GST_CLEARTAX_URL', 'https://cleartax.in/f/compliance-report'),
        'use_appyflow' => filter_var(env('GST_USE_APPYFLOW', false), FILTER_VALIDATE_BOOLEAN),
        'url' => env('GST_LOOKUP_URL', 'https://appyflow.in/api/verifyGST'),
        'appyflow_key' => env('GST_APPYFLOW_KEY_SECRET', ''),
    ],

];
