<?php

declare(strict_types=1);

namespace FinancialObserver\Tests;

use FinancialObserver\Config\ApiConfig;
use FinancialObserver\Services\MarketDataService;
use RuntimeException;

final class MarketDataServiceTest
{
    public static function run(): void
    {
        self::testEtfMoversRespectsLimitAndUrl();
        self::testMarketOverviewReturnsQuotes();
        self::testQuoteReturnsFirstResult();
        self::testQuoteThrowsWhenMissing();
    }

    private static function testEtfMoversRespectsLimitAndUrl(): void
    {
        $limit = 2;
        $expectedUrl = sprintf('%s/quotes/etf?apikey=%s&limit=%d', ApiConfig::BASE_URL, ApiConfig::apiKey(), $limit);
        $httpClient = new FakeHttpClient([
            $expectedUrl => [
                ['symbol' => 'AAA'],
                ['symbol' => 'BBB'],
                ['symbol' => 'CCC'],
            ],
        ]);

        $service = new MarketDataService($httpClient);
        $result = $service->getEtfMovers($limit);

        Assert::equals($expectedUrl, $httpClient->lastUrl(), 'ETF movers URL mismatch');
        Assert::equals(2, count($result), 'ETF movers limit not respected');
        Assert::equals('AAA', $result[0]['symbol']);
        Assert::equals('BBB', $result[1]['symbol']);
    }

    private static function testMarketOverviewReturnsQuotes(): void
    {
        $indices = ['^GSPC', '^DJI', '^IXIC', '^RUT', '^FTSE', '^GDAXI', '^N225'];
        $symbols = implode(',', $indices);
        $expectedUrl = sprintf('%s/quote/%s?apikey=%s', ApiConfig::BASE_URL, urlencode($symbols), ApiConfig::apiKey());

        $httpClient = new FakeHttpClient([
            $expectedUrl => [
                ['symbol' => '^GSPC', 'price' => 100],
            ],
        ]);

        $service = new MarketDataService($httpClient);
        $overview = $service->getMarketOverview();

        Assert::equals($expectedUrl, $httpClient->lastUrl(), 'Market overview URL mismatch');
        Assert::equals('^GSPC', $overview[0]['symbol']);
        Assert::equals(100, $overview[0]['price']);
    }

    private static function testQuoteReturnsFirstResult(): void
    {
        $expectedUrl = sprintf('%s/quote/%s?apikey=%s', ApiConfig::BASE_URL, urlencode('AAPL'), ApiConfig::apiKey());
        $httpClient = new FakeHttpClient([
            $expectedUrl => [
                ['symbol' => 'AAPL', 'price' => 150],
                ['symbol' => 'AAPL', 'price' => 149],
            ],
        ]);

        $service = new MarketDataService($httpClient);
        $quote = $service->getQuote('AAPL');

        Assert::equals($expectedUrl, $httpClient->lastUrl(), 'Quote URL mismatch');
        Assert::equals('AAPL', $quote['symbol']);
        Assert::equals(150, $quote['price']);
    }

    private static function testQuoteThrowsWhenMissing(): void
    {
        $expectedUrl = sprintf('%s/quote/%s?apikey=%s', ApiConfig::BASE_URL, urlencode('MISSING'), ApiConfig::apiKey());
        $httpClient = new FakeHttpClient([
            $expectedUrl => [],
        ]);

        $service = new MarketDataService($httpClient);

        Assert::throws(
            fn () => $service->getQuote('MISSING'),
            'Symbol not found or no data available.'
        );
    }
}
