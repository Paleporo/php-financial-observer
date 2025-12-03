<?php

declare(strict_types=1);

namespace FinancialObserver\Tests;

use FinancialObserver\Http\HttpClient;
use RuntimeException;

final class FakeHttpClient extends HttpClient
{
    /** @var array<string, array> */
    private array $responses;

    private string $lastUrl = '';

    /**
     * @param array<string, array> $responses
     */
    public function __construct(array $responses)
    {
        $this->responses = $responses;
    }

    public function getJson(string $url): array
    {
        $this->lastUrl = $url;

        if (!array_key_exists($url, $this->responses)) {
            throw new RuntimeException('No stubbed response for URL: ' . $url);
        }

        return $this->responses[$url];
    }

    public function lastUrl(): string
    {
        return $this->lastUrl;
    }
}
