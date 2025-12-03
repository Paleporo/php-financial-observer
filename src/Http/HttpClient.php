<?php

declare(strict_types=1);

namespace FinancialObserver\Http;

use RuntimeException;

class HttpClient
{
    public function getJson(string $url): array
    {
        $response = @file_get_contents($url);

        if ($response === false) {
            $error = error_get_last();
            throw new RuntimeException('HTTP request failed: ' . ($error['message'] ?? 'unknown error'));
        }

        $decoded = json_decode($response, true);

        if (!is_array($decoded)) {
            throw new RuntimeException('Invalid JSON response received from upstream API.');
        }

        return $decoded;
    }
}
