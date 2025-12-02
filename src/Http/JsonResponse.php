<?php

declare(strict_types=1);

namespace FinancialObserver\Http;

class JsonResponse
{
    public static function send(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
