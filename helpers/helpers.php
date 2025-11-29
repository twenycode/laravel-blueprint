<?php

/*
|--------------------------------------------------------------------------
| Global Helper Functions - Date & Time
|--------------------------------------------------------------------------
*/

use TwenyCode\LaravelBlueprint\Helpers\DateHelper;
use TwenyCode\LaravelBlueprint\Helpers\NumberHelper;
use TwenyCode\LaravelBlueprint\Helpers\TextHelper;

// Format date range in a readable format
function formatDateDuration(string $startDate, string $endDate): string
{
    return DateHelper::formatDateDuration($startDate, $endDate);
}

// Format time as a human-readable "time ago" string
function formatTimeAgo(string $timestamp): string
{
    return DateHelper::formatTimeAgo($timestamp);
}

// Convert a date and time string from one format to another
function dateTimeConversion($date, string $format = 'd M Y H:i:s')
{
    return DateHelper::dateTimeConversion($date, $format);
}

// Calculate number of days between two dates
function numberOfDays(string $date1, string $date2): float
{
    return DateHelper::numberOfDays($date1, $date2);
}

// Calculate the age of a record in days
function calculateAge($date)
{
    return DateHelper::calculateAge($date);
}

// Calculate number of days remaining from now to a date
function calculateRemainingDays($date): float
{
    return DateHelper::calculateRemainingDays($date);
}

// Get difference between two dates in human-readable format
function dateDifference(string $startDate, string $endDate): string
{
    return DateHelper::dateDifference($startDate, $endDate);
}

/*
|--------------------------------------------------------------------------
| Global Helper Functions - Numbers & Currency
|--------------------------------------------------------------------------
*/

// Convert bytes to human-readable file size
function formatFileSize(float $bytes, int $decimals = 2): string
{
    return NumberHelper::formatFileSize($bytes, $decimals);
}

// Format amount with currency symbol
function formatMoney(float $amount, ?object $currency = null): string
{
    return NumberHelper::formatMoney($amount, $currency);
}

// Format number with thousands separator, no decimals
function formatCurrency(float $number): string
{
    return NumberHelper::formatCurrency($number);
}

// Format number with thousands separator and decimals
function formatCurrencyDecimal(float $number, int $decimals = 2): string
{
    return NumberHelper::formatCurrencyDecimal($number, $decimals);
}

// Calculate percentage of an amount
function calculatePercentNumber(float $percent, ?float $amount = null)
{
    return NumberHelper::calculatePercentNumber($percent, $amount);
}


/*
|--------------------------------------------------------------------------
| Global Helper Functions - Text
|--------------------------------------------------------------------------
*/


// Trim text to specified word count
function trimWords(string $text, int $wordCount, string $ellipsis = '...'): string
{
    return TextHelper::trimWords($text, $wordCount, $ellipsis);
}

// Trim HTML text while preserving structure
function trimHtmlWords(string $html, int $wordCount, string $ellipsis = '...'): string
{
    return TextHelper::trimHtmlWords($html, $wordCount, $ellipsis);
}

// Remove underscores from a word
function removeUnderscore(string $word): string
{
    return TextHelper::removeUnderscore($word);
}

// Add underscores to a word
function addUnderscore(string $word): string
{
    return TextHelper::addUnderscore($word);
}

// Remove a character and capitalize the word
function removeCharAndCapitalize(string $char, string $word): string
{
    return TextHelper::removeCharAndCapitalize($char, $word);
}

// Replace a character in a string with space and capitalize
function replaceString(string $char, string $word): string
{
    return TextHelper::replaceString($char, $word);
}

// Return singular or plural suffix based on value
function textPlural(int $value, string $singular = '', string $plural = 's'): string
{
    return TextHelper::plural($value, $singular, $plural);
}

// Convert string to snake_case
function textSnake(string $string): string
{
    return TextHelper::snake($string);
}

// Convert string to Headline Case
function textHeadline(string $string): string
{
    return TextHelper::headline($string);
}

// Convert a word to its plural form
function pluralize(string $singular): string
{
    return TextHelper::pluralize($singular);
}

// Pluralize a camelCase or snake_case variable name
function pluralizeVariableName(string $variableName): string
{
    return TextHelper::pluralizeVariableName($variableName);
}
