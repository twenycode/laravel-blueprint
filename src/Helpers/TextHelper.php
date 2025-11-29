<?php

namespace TwenyCode\LaravelBlueprint\Helpers;

use Illuminate\Support\Str;

class TextHelper
{
    // Remove underscores from a word, replacing with spaces
    public static function removeUnderscore($word)
    {
        return str_replace("_", " ", $word);
    }

    // Add underscores to a word, replacing spaces and lowercasing
    public static function addUnderscore($word)
    {
        return str_replace(" ", "_", strtolower($word));
    }

    // Remove a specific character and capitalize the word
    public static function removeCharAndCapitalize($char, $word)
    {
        return str_replace($char, " ", ucwords($word));
    }

    // Replace a character in a string with space and capitalize
    public static function replaceString($char, $word)
    {
        return str_replace($char, " ", ucwords($word));
    }

    // Return singular or plural suffix based on a value
    public static function plural($value, $singular = '', $plural = 's')
    {
        if ($value === 1) {
            return $singular;
        }
        return $plural;
    }

    // Convert string to snake_case
    public static function snake($string)
    {
        return Str::snake($string);
    }

    // Convert string to Headline Case
    public static function headline($string)
    {
        return Str::headline($string);
    }

    // Trim text to a specific number of words
    public static function trimWords($text, $wordCount, $ellipsis = '...')
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
    public static function trimHtmlWords($html, $wordCount, $ellipsis = '...')
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
    public static function pluralize($singular)
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
    public static function pluralizeVariableName($variableName)
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
}