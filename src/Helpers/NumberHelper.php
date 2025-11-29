<?php

namespace TwenyCode\LaravelBlueprint\Helpers;

class NumberHelper
{
    // Convert file size in bytes to human-readable format
    public static function formatFileSize(float $bytes, int $decimals = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $factor = floor((strlen((string) $bytes) - 1) / 3);

        if ($factor > count($units) - 1) {
            $factor = count($units) - 1;
        }

        return sprintf("%.{$decimals}f %s", $bytes / pow(1024, $factor), $units[$factor]);
    }

    // Format number with decimal places and thousands separator
    public static function formatCurrencyDecimal($number, $int = 2): string
    {
        return number_format($number, $int, '.', ',');
    }

    // Format number with thousands separator, no decimal places
    public static function formatCurrency($number): string
    {
        return number_format($number, 0, '.', ',');
    }

    // Format money amount with currency symbol
    public static function formatMoney($amount, $currency = null)
    {
        $symbol = !is_null($currency) ? $currency->symbol : '$';
        return $symbol . ' ' . self::formatCurrencyDecimal($amount);
    }

    // Calculate percentage of an amount
    public static function calculatePercentNumber($percent, $amount = null)
    {
        return $percent / 100 * $amount;
    }
}