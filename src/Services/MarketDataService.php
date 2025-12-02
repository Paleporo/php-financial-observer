<?php

declare(strict_types=1);

namespace FinancialObserver\Services;

use FinancialObserver\Config\ApiConfig;
use FinancialObserver\Http\HttpClient;
use RuntimeException;

class MarketDataService
{
    public function __construct(private readonly HttpClient $httpClient = new HttpClient())
    {
    }

    public function getEtfMovers(int $limit = 10): array
    {
        $url = sprintf('%s/quotes/etf?apikey=%s&limit=%d', ApiConfig::BASE_URL, ApiConfig::apiKey(), $limit);
        $data = $this->httpClient->getJson($url);

        return array_slice($data, 0, $limit);
    }

    public function getMarketOverview(): array
    {
        $indices = ['^GSPC', '^DJI', '^IXIC', '^RUT', '^FTSE', '^GDAXI', '^N225'];
        $symbols = implode(',', $indices);
        $url = sprintf('%s/quote/%s?apikey=%s', ApiConfig::BASE_URL, urlencode($symbols), ApiConfig::apiKey());
        $quotes = $this->httpClient->getJson($url);

        if (!is_array($quotes)) {
            throw new RuntimeException('Unexpected response for index overview.');
        }

        return $quotes;
    }

    public function getQuote(string $symbol): array
    {
        $url = sprintf('%s/quote/%s?apikey=%s', ApiConfig::BASE_URL, urlencode($symbol), ApiConfig::apiKey());
        $quotes = $this->httpClient->getJson($url);

        if ($quotes === [] || !isset($quotes[0])) {
            throw new RuntimeException('Symbol not found or no data available.');
        }

        return $quotes[0];
    }
}
