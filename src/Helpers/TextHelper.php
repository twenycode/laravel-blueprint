<?php

namespace TwenyCode\LaravelBlueprint\Helpers;

use Illuminate\Support\Str;

class TextHelper
{
    /**
     * Remove underscores from a word.
     *
     * @param string $word The word to process
     * @return string
     */
    public static function removeUnderscore($word)
    {
        return str_replace("_", " ", $word);
    }

    /**
     * Add underscores to a word.
     *
     * @param string $word The word to process
     * @return string
     */
    public static function addUnderscore($word)
    {
        return str_replace(" ", "_", strtolower($word));
    }

    /**
     * Remove a character and capitalize the word.
     *
     * @param string $char The character to remove
     * @param string $word The word to process
     * @return string
     */
    public static function removeCharAndCapitalize($char, $word)
    {
        return str_replace($char, " ", ucwords($word));
    }

    /**
     * Replace a character in a string.
     *
     * @param string $char The character to replace
     * @param string $word The word to process
     * @return string
     */
    public static function replaceString($char, $word)
    {
        return str_replace($char, " ", ucwords($word));
    }

    /**
     * Return singular or plural suffix based on value.
     *
     * @param int $value The count
     * @param string $singular Singular suffix
     * @param string $plural Plural suffix
     * @return string
     */
    public static function plural($value, $singular = '', $plural = 's')
    {
        if ($value === 1) {
            return $singular;
        }
        return $plural;
    }

    /**
     * Convert string to snake case.
     *
     * @param string $string The string to convert
     * @return string
     */
    public static function snake($string)
    {
        return Str::snake($string);
    }

    /**
     * Convert string to headline case.
     *
     * @param string $string The string to convert
     * @return string
     */
    public static function headline($string)
    {
        return Str::headline($string);
    }

    /**
     * Trim text to a specific number of words.
     *
     * @param string $text The text to trim
     * @param int $wordCount Number of words to keep
     * @param string $ellipsis Ellipsis to append
     * @return string
     */
    public static function trimWords($text, $wordCount, $ellipsis = '...')
    {
        // Remove extra whitespace
        $text = trim($text);

        // If text is empty or word count is 0 or less, return empty string
        if (empty($text) || $wordCount <= 0) {
            return '';
        }

        // Split the text into words
        $words = preg_split('/\s+/', $text);

        // If the text has fewer words than the limit, return the original text
        if (count($words) <= $wordCount) {
            return $text;
        }

        // Keep only the specified number of words
        $trimmedWords = array_slice($words, 0, $wordCount);

        // Join the words back together
        $trimmedText = implode(' ', $trimmedWords);

        // Add ellipsis if the text was trimmed
        return $trimmedText . $ellipsis;
    }

    /**
     * Trim HTML text to a specific number of words while preserving HTML structure.
     *
     * @param string $html The HTML to trim
     * @param int $wordCount Number of words to keep
     * @param string $ellipsis Ellipsis to append
     * @return string
     */
    public static function trimHtmlWords($html, $wordCount, $ellipsis = '...')
    {
        // Strip tags to count actual words
        $stripped = strip_tags($html);
        $words = preg_split('/\s+/', trim($stripped));

        // If we don't need to trim
        if (count($words) <= $wordCount) {
            return $html;
        }

        // Get the first N words
        $limitedWords = array_slice($words, 0, $wordCount);
        $limitedText = implode(' ', $limitedWords);

        // Handle tags and preserve proper HTML structure
        $position = 0;
        $tags = array();

        // Find position of the last word in original HTML
        for ($i = 0; $i < $wordCount; $i++) {
            $word = $words[$i];
            // Find position of current word
            $wordPos = strpos($stripped, $word, $position);
            // If we somehow can't find the word, break
            if ($wordPos === false) break;

            // Update the position to character after current word
            $position = $wordPos + strlen($word);
        }

        // Extract HTML portion up to last word
        $htmlPart = substr($html, 0, strpos($html, $words[$wordCount - 1]) + strlen($words[$wordCount - 1]));

        // Find all opened tags
        preg_match_all('/<([a-z]+)[^>]*>/i', $htmlPart, $openedTags);

        // Find all closed tags
        preg_match_all('/<\/([a-z]+)>/i', $htmlPart, $closedTags);

        // Count opened tags
        $openedTagsCount = array();
        foreach ($openedTags[1] as $tag) {
            $tag = strtolower($tag);
            if (isset($openedTagsCount[$tag])) {
                $openedTagsCount[$tag]++;
            } else {
                $openedTagsCount[$tag] = 1;
            }
        }

        // Count closed tags
        foreach ($closedTags[1] as $tag) {
            $tag = strtolower($tag);
            if (isset($openedTagsCount[$tag])) {
                $openedTagsCount[$tag]--;
            }
        }

        // Close any unclosed tags
        $closingTags = '';
        foreach ($openedTagsCount as $tag => $count) {
            for ($i = 0; $i < $count; $i++) {
                $closingTags .= '</' . $tag . '>';
            }
        }

        return $htmlPart . $ellipsis . $closingTags;
    }

    /**
     * Converts a variable name to its plural form
     *
     * @param string $singular The singular variable name
     * @return string The pluralized variable name
     */
    public static function pluralize($singular)
    {
        // Special cases (irregular plurals)
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

        // Words that are uncountable (same in singular and plural)
        $uncountable = [
            'equipment', 'information', 'rice', 'money', 'species', 'series',
            'fish', 'sheep', 'deer', 'aircraft', 'feedback', 'metadata',
            'traffic', 'furniture', 'software', 'hardware', 'history'
        ];

        // Check for exact matches in irregular plurals
        if (array_key_exists($singular, $irregulars)) {
            return $irregulars[$singular];
        }

        // Check if word is uncountable
        if (in_array($singular, $uncountable)) {
            return $singular;
        }

        // Rules for words ending with 'y' preceded by a consonant
        if (preg_match('/[^aeiou]y$/i', $singular)) {
            return preg_replace('/y$/i', 'ies', $singular);
        }

        // Rules for words ending with 's', 'ss', 'sh', 'ch', 'x', 'z'
        if (preg_match('/(s|ss|sh|ch|x|z)$/i', $singular)) {
            return $singular . 'es';
        }

        // Rules for words ending with 'f' or 'fe'
        if (preg_match('/[^f]f$/i', $singular)) {
            return preg_replace('/f$/i', 'ves', $singular);
        }

        if (preg_match('/fe$/i', $singular)) {
            return preg_replace('/fe$/i', 'ves', $singular);
        }

        // Rules for words ending with 'o' preceded by a consonant
        if (preg_match('/[^aeiou]o$/i', $singular)) {
            return $singular . 'es';
        }

        // Default case: add 's'
        return $singular . 's';
    }

    /**
     * Function to pluralize a camelCase or snake_case variable name
     *
     * @param string $variableName The singular variable name
     * @return string The pluralized variable name
     */
    public static function pluralizeVariableName($variableName)
    {
        // Handle camelCase
        if (strpos($variableName, '_') === false && preg_match('/[A-Z]/', $variableName)) {
            // For camelCase, get the last part of the variable name
            preg_match('/([A-Z][a-z0-9]*)$/', $variableName, $matches);

            if (!empty($matches[1])) {
                // Last word exists and begins with a capital letter
                $lastWord = $matches[1];
                $pluralLastWord = self::pluralize(strtolower($lastWord));

                // Ensure first letter remains capitalized if the original was
                if (ctype_upper($lastWord[0])) {
                    $pluralLastWord = ucfirst($pluralLastWord);
                }

                return preg_replace('/' . $lastWord . '$/', $pluralLastWord, $variableName);
            }

            // For simpler camelCase like 'documentType'
            $lastWord = lcfirst($variableName);
            return self::pluralize($lastWord);
        }

        // Handle snake_case
        if (strpos($variableName, '_') !== false) {
            $parts = explode('_', $variableName);
            $lastPart = array_pop($parts);
            $parts[] = self::pluralize($lastPart);
            return implode('_', $parts);
        }

        // Simple variable name (no camelCase or snake_case)
        return self::pluralize($variableName);
    }
}