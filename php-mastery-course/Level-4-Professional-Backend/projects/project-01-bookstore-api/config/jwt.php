<?php

declare(strict_types=1);

return [
    'secret' => $_ENV['JWT_SECRET'] ?? 'change-me-in-production',
    'issuer' => $_ENV['JWT_ISSUER'] ?? 'bookstore-api',
    'expiry' => (int) ($_ENV['JWT_EXPIRY'] ?? 3600),
    'refresh_expiry' => (int) ($_ENV['JWT_REFRESH_EXPIRY'] ?? 86400),
    'algorithm' => 'HS256',
];
