<?php

namespace phpList\plugin\Common;

/**
 * Get a config value then split it into lines.
 *
 * @param string $item
 *
 * @return array
 */
function getConfigLines($item)
{
    return splitIntoLines(getConfig($item));
}

/**
 * Split a string into lines allowing for any line-ending.
 *
 * @param string $string
 *
 * @return array
 */
function splitIntoLines($value)
{
    $lines = [];

    if ($value !== '') {
        foreach (preg_split('|\R+|', $value) as $line) {
            $line = trim($line);

            if ($line !== '') {
                $lines[] = $line;
            }
        }
    }

    return $lines;
}

/*
 * shortenText
 *
 * Shorten text for use by shortenTextDisplay() but also stand-alone.
 *
 * Define multibyte-string aware/unaware function depending on whether the mbstring extension is available
 * see https://github.com/phpList/phplist3/pull/10
 */
if (!function_exists('mb_strlen')) {
    // mbstring unavailable
    function shortenText($text, $max = 30)
    {
        if (strlen($text) > $max) {
            if ($max < 30) {
                $shortened = substr($text, 0, $max - 4).' ... ';
            } else {
                $shortened = substr($text, 0, 20).' ... '.substr($text, -10);
            }
        } else {
            $shortened = $text;
        }

        return $shortened;
    }
} else {
    // mbstring available
    function shortenText($text, $max = 30)
    {
        if (mb_strlen($text) > $max) {
            if ($max < 30) {
                $shortened = mb_substr($text, 0, $max - 4).' ... ';
            } else {
                $shortened = mb_substr($text, 0, 20).' ... '.mb_substr($text, -10);
            }
        } else {
            $shortened = $text;
        }

        return $shortened;
    }
}

/*
 * shortenTextDisplay
 *
 * mostly used for columns in listings to retrict the width, particularly on mobile devices
 * it will show the full text as the title tip but restrict the size of the output
 *
 * will also place a space after / and @ to facilitate wrapping in the browser
 *
 */
function shortenTextDisplay($text, $max = 30)
{
    $display = preg_replace('!^https?://!i', '', $text);
    $display = shortenText($display, $max);
    $display = str_replace('/', '/&#x200b;', $display);
    $display = str_replace('@', '@&#x200b;', $display);

    return sprintf('<span title="%s">%s</span>', htmlspecialchars($text), $display);
}
