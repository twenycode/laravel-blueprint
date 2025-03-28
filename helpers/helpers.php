<?php

/**
 * Global helper functions
 *
 * These functions provide convenient shortcuts to commonly used functionality.
 * They delegate to the appropriate helper classes.
 */

use TwenyCode\LaravelCore\Helpers\DateHelper;
use TwenyCode\LaravelCore\Helpers\NumberHelper;
use TwenyCode\LaravelCore\Helpers\TextHelper;

// Date helpers
if (!function_exists('dateTimeConversion')) {
    /**
     * Convert a date and time string from one format to another.
     *
     * @param string|null $date1 The date to convert
     * @param string $date2 The target format
     * @return string|null
     */
    function dateTimeConversion($date1, $date2 = 'd M Y H:i:s')
    {
        return DateHelper::dateTimeConversion($date1, $date2);
    }
}

if (!function_exists('numberOfDays')) {
    /**
     * Calculate number of days between two dates.
     *
     * @param string $date1 Start date
     * @param string $date2 End date
     * @return float
     */
    function numberOfDays($date1, $date2): float
    {
        return DateHelper::numberOfDays($date1, $date2);
    }
}

if (!function_exists('calculateAge')) {
    /**
     * Calculate the age of a record in days.
     *
     * @param string|null $date The date to calculate from
     * @return int|null
     */
    function calculateAge($date)
    {
        return DateHelper::calculateAge($date);
    }
}

if (!function_exists('dateDifference')) {
    /**
     * Get difference between two dates in human-readable format.
     *
     * @param string $start_date Start date
     * @param string $end_date End date
     * @return string
     */
    function dateDifference($start_date, $end_date): string
    {
        return DateHelper::dateDifference($start_date, $end_date);
    }
}

if (!function_exists('calculateRemainingDays')) {
    /**
     * Calculate number of days remaining from now to a date.
     *
     * @param string $date The target date
     * @return float
     */
    function calculateRemainingDays($date): float
    {
        return DateHelper::calculateRemainingDays($date);
    }
}

if (!function_exists('formatDateDuration')) {
    /**
     * Format date range in a readable format.
     *
     * @param string $startDate Start date
     * @param string $endDate End date
     * @return string
     */
    function formatDateDuration($startDate, $endDate)
    {
        return DateHelper::formatDateDuration($startDate, $endDate);
    }
}

// Number helpers
if (!function_exists('formatFileSize')) {
    /**
     * Convert file size to human-readable format.
     *
     * @param float $bytes The size in bytes
     * @param int $decimals The number of decimals to use
     * @return string
     */
    function formatFileSize(float $bytes, int $decimals = 2): string
    {
        return NumberHelper::formatFileSize($bytes, $decimals);
    }
}

if (!function_exists('formatTimeAgo')) {
    /**
     * Format time as a human-readable "time ago" string.
     *
     * @param string $timestamp The timestamp to format
     * @return string
     */
    function formatTimeAgo($timestamp)
    {
        return DateHelper::formatTimeAgo($timestamp);
    }
}

if (!function_exists('formatCurrencyDecimal')) {
    /**
     * Format number with decimal places.
     *
     * @param float $number The number to format
     * @param int $int Number of decimal places
     * @return string
     */
    function formatCurrencyDecimal($number, $int = 2): string
    {
        return NumberHelper::formatCurrencyDecimal($number, $int);
    }
}

if (!function_exists('formatCurrency')) {
    /**
     * Format number without decimal places.
     *
     * @param float $number The number to format
     * @return string
     */
    function formatCurrency($number): string
    {
        return NumberHelper::formatCurrency($number);
    }
}

if (!function_exists('formatMoney')) {
    /**
     * Format money with currency symbol.
     *
     * @param float $amount The amount to format
     * @param object|null $currency Currency object with symbol property
     * @return string
     */
    function formatMoney($amount, $currency = null)
    {
        return NumberHelper::formatMoney($amount, $currency);
    }
}

if (!function_exists('calculatePercentNumber')) {
    /**
     * Calculate percentage of an amount.
     *
     * @param float $percent The percentage
     * @param float|null $amount The amount to calculate percentage from
     * @return float
     */
    function calculatePercentNumber($percent, $amount = null)
    {
        return NumberHelper::calculatePercentNumber($percent, $amount);
    }
}

// Text helpers
if (!function_exists('removeUnderscore')) {
    /**
     * Remove underscores from a word.
     *
     * @param string $word The word to process
     * @return string
     */
    function removeUnderscore($word)
    {
        return TextHelper::removeUnderscore($word);
    }
}

if (!function_exists('addUnderscore')) {
    /**
     * Add underscores to a word.
     *
     * @param string $word The word to process
     * @return string
     */
    function addUnderscore($word)
    {
        return TextHelper::addUnderscore($word);
    }
}

if (!function_exists('plural')) {
    /**
     * Return singular or plural suffix based on value.
     *
     * @param int $value The count
     * @param string $singular Singular suffix
     * @param string $plural Plural suffix
     * @return string
     */
    function plural($value, $singular = '', $plural = 's')
    {
        return TextHelper::plural($value, $singular, $plural);
    }
}

if (!function_exists('snake')) {
    /**
     * Convert string to snake case.
     *
     * @param string $string The string to convert
     * @return string
     */
    function snake($string)
    {
        return TextHelper::snake($string);
    }
}

if (!function_exists('headline')) {
    /**
     * Convert string to headline case.
     *
     * @param string $string The string to convert
     * @return string
     */
    function headline($string)
    {
        return TextHelper::headline($string);
    }
}

if (!function_exists('trimWords')) {
    /**
     * Trim text to a specific number of words.
     *
     * @param string $text The text to trim
     * @param int $wordCount Number of words to keep
     * @param string $ellipsis Ellipsis to append
     * @return string
     */
    function trimWords($text, $wordCount, $ellipsis = '...')
    {
        return TextHelper::trimWords($text, $wordCount, $ellipsis);
    }
}

if (!function_exists('trimHtmlWords')) {
    /**
     * Trim HTML text to a specific number of words while preserving HTML structure.
     *
     * @param string $html The HTML to trim
     * @param int $wordCount Number of words to keep
     * @param string $ellipsis Ellipsis to append
     * @return string
     */
    function trimHtmlWords($html, $wordCount, $ellipsis = '...')
    {
        return TextHelper::trimHtmlWords($html, $wordCount, $ellipsis);
    }
}

if (!function_exists('pluralize')) {
    /**
     * Converts a variable name to its plural form.
     *
     * @param string $singular The singular variable name
     * @return string The pluralized variable name
     */
    function pluralize($singular)
    {
        return TextHelper::pluralize($singular);
    }
}

if (!function_exists('pluralizeVariableName')) {
    /**
     * Function to pluralize a camelCase or snake_case variable name.
     *
     * @param string $variableName The singular variable name
     * @return string The pluralized variable name
     */
    function pluralizeVariableName($variableName)
    {
        return TextHelper::pluralizeVariableName($variableName);
    }
}