<?php

use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Date Helper Class
|--------------------------------------------------------------------------
*/
// Convert a date and time string from one format to another
function dateTimeConversion($date1, $date2 = 'd M Y H:i:s')
{
    if (!is_null($date1)) {
        return Carbon::parse($date1)->format($date2);
    }
    return null;
}

// Calculate number of days between two dates
function numberOfDays($date1, $date2): float
{
    $date1 = strtotime($date1);
    $date2 = strtotime($date2);
    $difference = $date2 - $date1;
    return round($difference / (60 * 60 * 24));
}

// Calculate the age of a record in days from a given date to today
function calculateAge($date)
{
    if ($date != null) {
        $dob = new DateTime($date);
        $today = new DateTime(date('Y-m-d'));
        $interval = $today->diff($dob);
        return $interval->days;
    }
    return null;
}

// Get difference between two dates in human-readable format
function dateDifference($start_date, $end_date): string
{
    $fromDate = new DateTime($start_date);
    $curDate = new DateTime($end_date);
    $months = $curDate->diff($fromDate);

    if ($months->format('%y') > 0) {
        return $months->format('%y years %m months');
    }

    return $months->format('%m months');
}

// Calculate number of days remaining from now to a future date
function calculateRemainingDays($date): float
{
    $now = Carbon::now();
    return max($now->diffInDays($date), 0);
}

// Format date range in a readable format
function formatDateDuration($startDate, $endDate)
{
    $start = Carbon::parse($startDate);
    $end = Carbon::parse($endDate);

    // Same month and year
    if ($start->month == $end->month && $start->year == $end->year) {
        return $start->format('F d') . '-' . $end->format('d') . ', ' . $start->format(' Y');
    }

    // Different months but same year
    if ($start->year == $end->year) {
        return $start->format('d M') . ' - ' . $end->format('d M, Y');
    }

    // Different years
    return $start->format('d M Y') . ' - ' . $end->format('d M Y');
}

// Format time as a human-readable "time ago" string
function formatTimeAgo($timestamp)
{
    $time_ago = strtotime($timestamp);
    $current_time = time();
    $timeDifference = $current_time - $time_ago;

    $intervals = [
        31553280 => ['year', 'years'],
        2629440  => ['month', 'months'],
        604800   => ['week', 'weeks'],
        86400    => ['day', 'days'],
        3600     => ['hour', 'hours'],
        60       => ['minute', 'minutes'],
        1        => ['second', 'seconds']
    ];

    foreach ($intervals as $seconds => list($singular, $plural)) {
        $count = floor($timeDifference / $seconds);
        if ($count > 0) {
            return $count === 1 ? "1 $singular ago" : "$count $plural ago";
        }
    }

    return "Just now";
}

/*
|--------------------------------------------------------------------------
| Number Helper Class
|--------------------------------------------------------------------------
*/
// Convert file size in bytes to human-readable format
function formatFileSize(float $bytes, int $decimals = 2): string
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    $factor = floor((strlen((string) $bytes) - 1) / 3);

    if ($factor > count($units) - 1) {
        $factor = count($units) - 1;
    }

    return sprintf("%.{$decimals}f %s", $bytes / pow(1024, $factor), $units[$factor]);
}

// Format number with decimal places and thousands separator
function formatCurrencyDecimal($number, $int = 2): string
{
    return number_format($number, $int, '.', ',');
}

// Format number with thousands separator, no decimal places
function formatCurrency($number): string
{
    return number_format($number, 0, '.', ',');
}

// Format money amount with currency symbol
function formatMoney($amount, $currency = null)
{
    $symbol = !is_null($currency) ? $currency->symbol : '$';
    return $symbol . ' ' . self::formatCurrencyDecimal($amount);
}

// Calculate percentage of an amount
function calculatePercentNumber($percent, $amount = null)
{
    return $percent / 100 * $amount;
}


/*
|--------------------------------------------------------------------------
| Text Helper Class
|--------------------------------------------------------------------------
*/
// Remove underscores from a word, replacing with spaces
function removeUnderscore($word)
{
    return str_replace("_", " ", $word);
}

// Add underscores to a word, replacing spaces and lowercasing
function addUnderscore($word)
{
    return str_replace(" ", "_", strtolower($word));
}

// Remove a specific character and capitalize the word
function removeCharAndCapitalize($char, $word)
{
    return str_replace($char, " ", ucwords($word));
}

// Replace a character in a string with space and capitalize
function replaceString($char, $word)
{
    return str_replace($char, " ", ucwords($word));
}

// Return singular or plural suffix based on a value
function plural($value, $singular = '', $plural = 's')
{
    if ($value === 1) {
        return $singular;
    }
    return $plural;
}

// Convert string to snake_case
function snake($string)
{
    return Str::snake($string);
}

// Convert string to Headline Case
function headline($string)
{
    return Str::headline($string);
}

// Trim text to a specific number of words
function trimWords($text, $wordCount, $ellipsis = '...')
{
    $text = trim($text);

    if (empty($text) || $wordCount <= 0) {
        return '';
    }

    $words = preg_split('/\s+/', $text);

    if (count($words) <= $wordCount) {
        return $text;
    }

    $trimmedWords = array_slice($words, 0, $wordCount);
    $trimmedText = implode(' ', $trimmedWords);

    return $trimmedText . $ellipsis;
}

// Trim HTML text to a specific number of words while preserving HTML structure
function trimHtmlWords($html, $wordCount, $ellipsis = '...')
{
    $stripped = strip_tags($html);
    $words = preg_split('/\s+/', trim($stripped));

    if (count($words) <= $wordCount) {
        return $html;
    }

    $limitedWords = array_slice($words, 0, $wordCount);
    $limitedText = implode(' ', $limitedWords);

    $position = 0;
    $tags = array();

    for ($i = 0; $i < $wordCount; $i++) {
        $word = $words[$i];
        $wordPos = strpos($stripped, $word, $position);
        if ($wordPos === false) break;
        $position = $wordPos + strlen($word);
    }

    $htmlPart = substr($html, 0, strpos($html, $words[$wordCount - 1]) + strlen($words[$wordCount - 1]));

    preg_match_all('/<([a-z]+)[^>]*>/i', $htmlPart, $openedTags);
    preg_match_all('/<\/([a-z]+)>/i', $htmlPart, $closedTags);

    $openedTagsCount = array();
    foreach ($openedTags[1] as $tag) {
        $tag = strtolower($tag);
        if (isset($openedTagsCount[$tag])) {
            $openedTagsCount[$tag]++;
        } else {
            $openedTagsCount[$tag] = 1;
        }
    }

    foreach ($closedTags[1] as $tag) {
        $tag = strtolower($tag);
        if (isset($openedTagsCount[$tag])) {
            $openedTagsCount[$tag]--;
        }
    }

    $closingTags = '';
    foreach ($openedTagsCount as $tag => $count) {
        for ($i = 0; $i < $count; $i++) {
            $closingTags .= '</' . $tag . '>';
        }
    }

    return $htmlPart . $ellipsis . $closingTags;
}

// Convert a word to its plural form
function pluralize($singular)
{
    $irregulars = [
        'person' => 'people',
        'man' => 'men',
        'child' => 'children',
        'foot' => 'feet',
        'tooth' => 'teeth',
        'goose' => 'geese',
        'mouse' => 'mice',
        'ox' => 'oxen',
        'leaf' => 'leaves',
        'life' => 'lives',
        'wife' => 'wives',
        'knife' => 'knives',
        'datum' => 'data',
        'analysis' => 'analyses',
        'criterion' => 'criteria',
        'medium' => 'media',
        'phenomenon' => 'phenomena',
        'crisis' => 'crises',
        'index' => 'indices',
        'matrix' => 'matrices',
        'vertex' => 'vertices',
    ];

    $uncountable = [
        'equipment', 'information', 'rice', 'money', 'species', 'series',
        'fish', 'sheep', 'deer', 'aircraft', 'feedback', 'metadata',
        'traffic', 'furniture', 'software', 'hardware', 'history'
    ];

    if (array_key_exists($singular, $irregulars)) {
        return $irregulars[$singular];
    }

    if (in_array($singular, $uncountable)) {
        return $singular;
    }

    if (preg_match('/[^aeiou]y$/i', $singular)) {
        return preg_replace('/y$/i', 'ies', $singular);
    }

    if (preg_match('/(s|ss|sh|ch|x|z)$/i', $singular)) {
        return $singular . 'es';
    }

    if (preg_match('/[^f]f$/i', $singular)) {
        return preg_replace('/f$/i', 'ves', $singular);
    }

    if (preg_match('/fe$/i', $singular)) {
        return preg_replace('/fe$/i', 'ves', $singular);
    }

    if (preg_match('/[^aeiou]o$/i', $singular)) {
        return $singular . 'es';
    }

    return $singular . 's';
}

// Pluralize a camelCase or snake_case variable name
function pluralizeVariableName($variableName)
{
    if (strpos($variableName, '_') === false && preg_match('/[A-Z]/', $variableName)) {
        preg_match('/([A-Z][a-z0-9]*)$/', $variableName, $matches);

        if (!empty($matches[1])) {
            $lastWord = $matches[1];
            $pluralLastWord = self::pluralize(strtolower($lastWord));

            if (ctype_upper($lastWord[0])) {
                $pluralLastWord = ucfirst($pluralLastWord);
            }

            return preg_replace('/' . $lastWord . '$/', $pluralLastWord, $variableName);
        }

        $lastWord = lcfirst($variableName);
        return self::pluralize($lastWord);
    }

    if (strpos($variableName, '_') !== false) {
        $parts = explode('_', $variableName);
        $lastPart = array_pop($parts);
        $parts[] = self::pluralize($lastPart);
        return implode('_', $parts);
    }

    return self::pluralize($variableName);
}
