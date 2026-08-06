<?php

declare(strict_types=1);

namespace App\Controllers;

class StaticController
{
    public function customerService(): void
    {
        $pageTitle = "Customer Service";
        require __DIR__ . "/../views/customer-service.php";
    }

    public function registry(): void
    {
        $pageTitle = "Registry";
        require __DIR__ . "/../views/registry.php";
    }

    public function sell(): void
    {
        $pageTitle = "Sell on amazone";
        require __DIR__ . "/../views/sell.php";
    }

    public function about(): void
    {
        $pageTitle = "About amazone";
        require __DIR__ . "/../views/about.php";
    }

    public function careers(): void
    {
        $pageTitle = "Careers at amazone";
        require __DIR__ . "/../views/careers.php";
    }

    public function sustainability(): void
    {
        $pageTitle = "Sustainability";
        require __DIR__ . "/../views/sustainability.php";
    }

    public function affiliate(): void
    {
        $pageTitle = "Become an Affiliate";
        require __DIR__ . "/../views/affiliate.php";
    }

    public function advertise(): void
    {
        $pageTitle = "Advertise Your Products";
        require __DIR__ . "/../views/advertise.php";
    }

    public function shippingPolicies(): void
    {
        $pageTitle = "Shipping Rates & Policies";
        require __DIR__ . "/../views/shipping-policies.php";
    }

    public function help(): void
    {
        $pageTitle = "Help & Customer Service";
        require __DIR__ . "/../views/help.php";
    }
}
