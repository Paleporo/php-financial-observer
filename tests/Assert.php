<?php

declare(strict_types=1);

namespace FinancialObserver\Tests;

use Exception;

final class Assert
{
    public static function equals(mixed $expected, mixed $actual, string $message = ''): void
    {
        if ($expected !== $actual) {
            $prefix = $message !== '' ? $message . ' - ' : '';
            throw new Exception($prefix . 'Expected ' . var_export($expected, true) . ', got ' . var_export($actual, true));
        }
    }

    public static function true(bool $condition, string $message = ''): void
    {
        if ($condition !== true) {
            $prefix = $message !== '' ? $message . ' - ' : '';
            throw new Exception($prefix . 'Expected condition to be true.');
        }
    }

    public static function throws(callable $callback, string $expectedMessage): void
    {
        try {
            $callback();
        } catch (Exception $exception) {
            self::equals($expectedMessage, $exception->getMessage(), 'Exception message mismatch');
            return;
        }

        throw new Exception('Expected exception with message: ' . $expectedMessage);
    }
}
