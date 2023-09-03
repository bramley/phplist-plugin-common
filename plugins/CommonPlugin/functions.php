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

/*
 * Log a message when the logging threshold is DEBUG.
 *
 * @param message $string
 *
 * @return array
 */
function debug($message)
{
    global $log_options, $tmpdir;

    static $logger;

    if (($log_options['threshold'] ?? '') != 'DEBUG') {
        return;
    }

    if ($logger === null) {
        $dir = $log_options['dir'] ?? $tmpdir;
        $logger = new \Katzgrau\KLogger\Logger($dir, \Psr\Log\LogLevel::DEBUG);
        $logger->setDateFormat('H:i:s');
    }
    $logger->debug($message);
}

/*
 * Construct the base URL for a public page, without trailing /.
 *
 * @return string
 */
function publicBaseUrl()
{
    global $public_scheme, $pageroot;

    static $url = null;

    if ($url === null) {
        if (defined('USER_WWWROOT')) {
            $url = USER_WWWROOT;
        } else {
            $url = sprintf('%s://%s%s', $public_scheme, getConfig('website'), $pageroot);
        }
    }

    return $url;
}

/*
 * Construct a public URL.
 *
 * @param string $page      optional page
 * @param array  $params    query parameters
 *
 * @return string
 */
function publicUrl(...$args)
{
    $page = '';
    $params = [];

    if (count($args) == 2) {
        $page = $args[0];
        $params = $args[1];
    } elseif (count($args) == 1) {
        $params = $args[0];
    }
    $url = sprintf('%s/%s?%s', publicBaseUrl(), $page, http_build_query($params));

    return $url;
}

/*
 * Construct the base URL for an admin page, without trailing /.
 *
 * @return string
 */
function adminBaseUrl()
{
    global $admin_scheme, $pageroot;

    static $url = null;

    if ($url === null) {
        $url = sprintf('%s://%s%s/admin', $admin_scheme, getConfig('website'), $pageroot);
    }

    return $url;
}
