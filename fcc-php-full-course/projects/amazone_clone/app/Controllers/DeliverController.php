<?php

declare(strict_types=1);

namespace App\Controllers;

class DeliverController
{
    public function index(): void
    {
        $pageTitle = "Manage Your Delivery Locations";
        $mapsKey    = (string) config("google_maps.api_key", "");
        $hasMapsKey = $mapsKey !== "";
        require __DIR__ . "/../views/deliver-to.php";
    }
}
