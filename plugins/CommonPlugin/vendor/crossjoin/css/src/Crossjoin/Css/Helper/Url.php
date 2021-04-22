<?php
namespace Crossjoin\Css\Helper;

class Url
{
    /**
     * Extracts the URL from a CSS URL value.
     *
     * @param string $url
     * @return string
     */
    public static function extractUrl($url)
    {
        if (is_string($url)) {
            // Extract the URL from a given CSS URL value|string
            //
            // Examples:
            // - "http://www.example.com"
            // - 'http://www.example.com'
            // - url("http://www.example.com")
            // - url( 'http://www.example.com' )
            // - url(http://www.example.com)
            // - url( http://www.example.com )
            // - url("http://www.example.com?escape=\"escape\"")
            //
            // "Parentheses, whitespace characters, single quotes (') and double quotes (") appearing in a URL must be
            // escaped with a backslash so that the resulting value is a valid URL token"
            $url = Placeholder::replaceStringPlaceholders($url);
            $url = preg_replace('/^url\(|\)$/', '', $url);
            $url = str_replace(["\\(", "\\)", "\\ ", "\\'", "\\\""], ["(", ")", " ", "'", "\""], $url);
            $url = preg_replace('/^(["\'])(.*)\g{1}$/', '\\2', $url);
            $url = urldecode($url);

            return $url;
        } else {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($url). "' for argument 'url' given."
            );
        }
    }

    /**
     * Escapes a URL for the use in CSS.
     *
     * @param string $url
     * @return string
     */
    public static function escapeUrl($url)
    {
        if (is_string($url)) {
            // "Parentheses, whitespace characters, single quotes (') and double quotes (") appearing in a URL must be
            // escaped with a backslash so that the resulting value is a valid URL token"
            return str_replace(["(", ")", " ", "'", "\""], ["\\(", "\\)", "\\ ", "\\'", "\\\""], $url);
        } else {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($url). "' for argument 'url' given."
            );
        }
    }
}