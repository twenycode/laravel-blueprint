<?php

namespace TwenyCode\LaravelCore\Helpers;

class NumberHelper
{
    /**
     * Convert file size to human-readable format.
     *
     * @param float $bytes The size in bytes
     * @param int $decimals The number of decimals to use
     * @return string
     */
    public static function formatFileSize(float $bytes, int $decimals = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $factor = floor((strlen((string) $bytes) - 1) / 3);

        if ($factor > count($units) - 1) {
            $factor = count($units) - 1;
        }

        return sprintf("%.{$decimals}f %s", $bytes / pow(1024, $factor), $units[$factor]);
    }

    /**
     * Format number with decimal places.
     *
     * @param float $number The number to format
     * @param int $int Number of decimal places
     * @return string
     */
    public static function formatCurrencyDecimal($number, $int = 2): string
    {
        return number_format($number, $int, '.', ',');
    }

    /**
     * Format number without decimal places.
     *
     * @param float $number The number to format
     * @return string
     */
    public static function formatCurrency($number): string
    {
        return number_format($number, 0, '.', ',');
    }

    /**
     * Format money with currency symbol.
     *
     * @param float $amount The amount to format
     * @param object|null $currency Currency object with symbol property
     * @return string
     */
    public static function formatMoney($amount, $currency = null)
    {
        $symbol = !is_null($currency) ? $currency->symbol : '$';
        return $symbol . ' ' . self::formatCurrencyDecimal($amount);
    }

    /**
     * Calculate percentage of an amount.
     *
     * @param float $percent The percentage
     * @param float|null $amount The amount to calculate percentage from
     * @return float
     */
    public static function calculatePercentNumber($percent, $amount = null)
    {
        return $percent / 100 * $amount;
    }
}