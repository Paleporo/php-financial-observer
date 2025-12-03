<?php

declare(strict_types=1);

namespace FinancialObserver\Tests;

use FinancialObserver\Controllers\MarketController;
use FinancialObserver\Services\MarketDataService;
use RuntimeException;

final class MarketControllerTest
{
    public static function run(): void
    {
        self::testHealthResponse();
        self::testEtfMoversSuccess();
        self::testEtfMoversError();
        self::testMarketOverviewError();
        self::testQuoteSuccess();
        self::testQuoteError();
    }

    private static function testHealthResponse(): void
    {
        $controller = new MarketController(new FakeMarketDataService());
        $response = $controller->health();

        Assert::equals('ok', $response['status']);
        Assert::true(isset($response['timestamp']) && is_string($response['timestamp']), 'Timestamp missing');
        Assert::true((bool) preg_match('/^\d{4}-\d{2}-\d{2}T/', $response['timestamp']), 'Timestamp not ISO-8601');
    }

    private static function testEtfMoversSuccess(): void
    {
        $controller = new MarketController(new FakeMarketDataService([
            'etfMovers' => [
                ['symbol' => 'ETF1'],
                ['symbol' => 'ETF2'],
            ],
        ]));

        $response = $controller->etfMovers(2);

        Assert::equals('ETF1', $response[0]['symbol']);
        Assert::equals('ETF2', $response[1]['symbol']);
    }

    private static function testEtfMoversError(): void
    {
        $controller = new MarketController(new FakeMarketDataService(errors: [
            'etfMovers' => 'Upstream ETF error',
        ]));

        $response = $controller->etfMovers(1);

        Assert::equals('Upstream ETF error', $response['error']);
    }

    private static function testMarketOverviewError(): void
    {
        $controller = new MarketController(new FakeMarketDataService(errors: [
            'marketOverview' => 'Overview error',
        ]));

        $response = $controller->marketOverview();

        Assert::equals('Overview error', $response['error']);
    }

    private static function testQuoteSuccess(): void
    {
        $controller = new MarketController(new FakeMarketDataService([
            'quote' => ['symbol' => 'QQQ'],
        ]));

        $response = $controller->quote('QQQ');

        Assert::equals('QQQ', $response['symbol']);
    }

    private static function testQuoteError(): void
    {
        $controller = new MarketController(new FakeMarketDataService(errors: [
            'quote' => 'Quote unavailable',
        ]));

        $response = $controller->quote('MISSING');

        Assert::equals('Quote unavailable', $response['error']);
    }
}

final class FakeMarketDataService extends MarketDataService
{
    /** @param array<string, mixed> $data */
    public function __construct(private array $data = [], private array $errors = [])
    {
    }

    public function getEtfMovers(int $limit = 10): array
    {
        if (isset($this->errors['etfMovers'])) {
            throw new RuntimeException($this->errors['etfMovers']);
        }

        return array_slice($this->data['etfMovers'] ?? [], 0, $limit);
    }

    public function getMarketOverview(): array
    {
        if (isset($this->errors['marketOverview'])) {
            throw new RuntimeException($this->errors['marketOverview']);
        }

        return $this->data['marketOverview'] ?? [];
    }

    public function getQuote(string $symbol): array
    {
        if (isset($this->errors['quote'])) {
            throw new RuntimeException($this->errors['quote']);
        }

        return $this->data['quote'] ?? [];
    }
}
