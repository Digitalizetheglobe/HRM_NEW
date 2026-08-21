<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Auto present emails (daily)
    |--------------------------------------------------------------------------
    |
    | Comma-separated list via env:
    | AUTO_PRESENT_EMAILS="a@x.com,b@x.com"
    |
    */
    'auto_present_emails' => array_values(array_filter(array_map('trim', explode(',', (string) env('AUTO_PRESENT_EMAILS', env('MONTHLY_FULL_PRESENT_EMAILS', '')))))),

    // Backward compatibility (old key name)
    'monthly_full_present_emails' => array_values(array_filter(array_map('trim', explode(',', (string) env('MONTHLY_FULL_PRESENT_EMAILS', ''))))),
];


