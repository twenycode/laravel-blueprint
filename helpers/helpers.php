<?php

/**
 * Laravel Blueprint - Global Helper Functions
 *
 * Provides convenient shortcuts for commonly used functionality.
 * Only includes helpers that add real value beyond Laravel's built-in methods.
 */

use TwenyCode\LaravelBlueprint\Helpers\DateHelper;
use TwenyCode\LaravelBlueprint\Helpers\NumberHelper;
use TwenyCode\LaravelBlueprint\Helpers\TextHelper;

/*
|--------------------------------------------------------------------------
| Date & Time Helpers
|--------------------------------------------------------------------------
*/

if (!function_exists('formatDateDuration')) {
    /**
     * Format date range in a readable format.
     * Example: "15 Jan - 20 Feb, 2024"
     */
    function formatDateDuration(string $startDate, string $endDate): string
    {
        return DateHelper::formatDateDuration($startDate, $endDate);
    }
}

/*
|--------------------------------------------------------------------------
| Number & Currency Helpers
|--------------------------------------------------------------------------
*/

if (!function_exists('formatFileSize')) {
    /**
     * Convert bytes to human-readable file size.
     * Example: 1024000 → "1.00 MB"
     */
    function formatFileSize(float $bytes, int $decimals = 2): string
    {
        return NumberHelper::formatFileSize($bytes, $decimals);
    }
}

if (!function_exists('formatMoney')) {
    /**
     * Format amount with currency symbol.
     * Example: 1234.56 → "$ 1,234.56"
     */
    function formatMoney(float $amount, ?object $currency = null): string
    {
        return NumberHelper::formatMoney($amount, $currency);
    }
}

if (!function_exists('formatCurrency')) {
    /**
     * Format number with thousands separator, no decimals.
     * Example: 1234.56 → "1,235"
     */
    function formatCurrency(float $number): string
    {
        return NumberHelper::formatCurrency($number);
    }
}

if (!function_exists('formatCurrencyDecimal')) {
    /**
     * Format number with thousands separator and decimals.
     * Example: 1234.56 → "1,234.56"
     */
    function formatCurrencyDecimal(float $number, int $decimals = 2): string
    {
        return NumberHelper::formatCurrencyDecimal($number, $decimals);
    }
}

/*
|--------------------------------------------------------------------------
| Text Helpers
|--------------------------------------------------------------------------
*/

if (!function_exists('trimWords')) {
    /**
     * Trim text to specified word count.
     * Example: trimWords("Hello world foo bar", 2) → "Hello world..."
     */
    function trimWords(string $text, int $wordCount, string $ellipsis = '...'): string
    {
        return TextHelper::trimWords($text, $wordCount, $ellipsis);
    }
}

if (!function_exists('trimHtmlWords')) {
    /**
     * Trim HTML text while preserving structure.
     * Example: trimHtmlWords("<p>Hello <strong>world</strong></p>", 1) → "<p>Hello...</p>"
     */
    function trimHtmlWords(string $html, int $wordCount, string $ellipsis = '...'): string
    {
        return TextHelper::trimHtmlWords($html, $wordCount, $ellipsis);
    }
}