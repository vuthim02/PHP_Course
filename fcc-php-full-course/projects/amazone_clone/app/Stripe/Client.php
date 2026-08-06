<?php

declare(strict_types=1);

namespace App\Stripe;

/**
 * Minimal Stripe client — talks to the REST API with cURL (no SDK / Composer).
 * Uses TEST-MODE keys from config.php, so nothing real is charged.
 */
class Client
{
    private const BASE = "https://api.stripe.com/v1/";

    public static function api(string $method, string $endpoint, array $params = []): array
    {
        $secret = config("stripe.secret_key", "");
        if ($secret === "" || str_starts_with($secret, "sk_test_xxx")) {
            throw new \RuntimeException(
                "Stripe secret key is not set. Add your TEST keys in app/config.php."
            );
        }

        $ch = curl_init(self::BASE . $endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD        => $secret . ":",
            CURLOPT_CUSTOMREQUEST  => strtoupper($method),
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_POSTFIELDS     => http_build_query($params),
        ]);

        $body   = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        $data = json_decode((string) $body, true);

        if ($status >= 400) {
            $msg = is_array($data) ? ($data["error"]["message"] ?? "HTTP " . $status) : "HTTP " . $status;
            throw new \RuntimeException("Stripe error: " . $msg);
        }

        return is_array($data) ? $data : [];
    }

    public static function createPaymentIntent(int $amountCents, int $orderId): array
    {
        return self::api("POST", "payment_intents", [
            "amount"       => $amountCents,
            "currency"     => "usd",
            "description"  => "amazone order #" . $orderId,
            "metadata[order_id]" => $orderId,
            "automatic_payment_methods[enabled]" => "true",
        ]);
    }

    public static function getPaymentIntent(string $id): array
    {
        return self::api("GET", "payment_intents/" . $id);
    }
}
