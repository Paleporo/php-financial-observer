<?php

declare(strict_types=1);

namespace FinancialObserver\Config;

final class ApiConfig
{
    public const string BASE_URL = 'https://financialmodelingprep.com/api/v3';
    public const string DEFAULT_API_KEY = 'demo';

    public static function apiKey(): string
    {
        $key = getenv('FMP_API_KEY');

        return $key !== false && $key !== '' ? $key : self::DEFAULT_API_KEY;
    }
}
