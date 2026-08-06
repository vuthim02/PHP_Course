<?php
// config.example.php — copy this file to config.php to customise settings.
// SQLite needs no credentials; the "db" section is just a file path.

return [
    "db" => [
        "driver" => "sqlite",
        "path"   => __DIR__ . "/../database/amazone.sqlite",
    ],

    "site" => [
        "name"             => "amazone",
        "currency"         => "$",
        "items_per_page"   => 24,
        "flat_rate_shipping" => 9.99,
        "free_shipping_over" => 25.00,
        "tax_rate"         => 0.08,
    ],

    // Stripe — TEST MODE keys from https://dashboard.stripe.com/test/apikeys
    // Free account, test cards never move real money.
    "stripe" => [
        "publishable_key" => "pk_test_xxxxxxxxxxxxxxxxxxxxxxxx",
        "secret_key"      => "sk_test_xxxxxxxxxxxxxxxxxxxxxxxx",
    ],

    // Google Maps — Places Autocomplete + reverse geocoding for the
    // "Deliver to" picker. Get a key at https://console.cloud.google.com/
    // Leave empty to fall back to a plain city/ZIP input (no key needed).
    "google_maps" => [
        "api_key" => "",
    ],

    "session" => [
        "name"     => "amazone_session",
        "lifetime" => 3600,
    ],
];
