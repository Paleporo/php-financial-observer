<?php

declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/Assert.php';
require __DIR__ . '/FakeHttpClient.php';
require __DIR__ . '/MarketDataServiceTest.php';
require __DIR__ . '/MarketControllerTest.php';

use FinancialObserver\Tests\MarketControllerTest;
use FinancialObserver\Tests\MarketDataServiceTest;

$tests = [
    'MarketDataServiceTest' => [MarketDataServiceTest::class, 'run'],
    'MarketControllerTest' => [MarketControllerTest::class, 'run'],
];

$failures = 0;

foreach ($tests as $name => $callable) {
    try {
        $callable();
        echo "[PASS] {$name}\n";
    } catch (Throwable $throwable) {
        $failures++;
        echo "[FAIL] {$name}: " . $throwable->getMessage() . "\n";
    }
}

if ($failures > 0) {
    exit(1);
}

echo "All tests passed\n";
