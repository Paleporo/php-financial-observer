<?php

declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

use FinancialObserver\Controllers\MarketController;
use FinancialObserver\Routing\Router;

$controller = new MarketController();
$router = new Router();

$router->get('/health', fn () => $controller->health());
$router->get('/api/markets/overview', fn () => $controller->marketOverview());
$router->get('/api/etf/top-movers', function () use ($controller) {
    $limit = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT, [
        'options' => [
            'min_range' => 1,
            'max_range' => 100,
        ],
    ]);

    return $controller->etfMovers($limit ?: 10);
});
$router->get('/api/quotes/{symbol}', fn (string $symbol) => $controller->quote($symbol));

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $path);
