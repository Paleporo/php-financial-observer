<?php

declare(strict_types=1);

namespace FinancialObserver\Controllers;

use FinancialObserver\Services\MarketDataService;
use RuntimeException;

class MarketController
{
    public function __construct(private readonly MarketDataService $service = new MarketDataService())
    {
    }

    public function health(): array
    {
        return [
            'status' => 'ok',
            'timestamp' => (new \DateTimeImmutable())->format(DATE_ATOM),
        ];
    }

    public function etfMovers(?int $limit = null): array
    {
        $limit = $limit ?? 10;

        try {
            return $this->service->getEtfMovers($limit);
        } catch (RuntimeException $exception) {
            return $this->wrapError($exception);
        }
    }

    public function marketOverview(): array
    {
        try {
            return $this->service->getMarketOverview();
        } catch (RuntimeException $exception) {
            return $this->wrapError($exception);
        }
    }

    public function quote(string $symbol): array
    {
        try {
            return $this->service->getQuote($symbol);
        } catch (RuntimeException $exception) {
            return $this->wrapError($exception);
        }
    }

    private function wrapError(RuntimeException $exception): array
    {
        return [
            'error' => $exception->getMessage(),
        ];
    }
}
