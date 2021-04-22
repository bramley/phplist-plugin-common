<?php
namespace Crossjoin\Css\Helper;

class Placeholder
{
    /**
     * @var int Counter used for generating unique place holder names
     */
    private static $counter = 1;

    /**
     * @var array Array to save the place holders
     */
    protected static $replacements = [];

    /**
     * Replaces all CSS strings and comments in the given text.
     *
     * @param string $text
     * @return string
     */
    public static function replaceStringsAndComments($text)
    {
        if (is_string($text)) {
            // Fast pre-check, to optimize performance
            if (strpos($text, "'") !== false || strpos($text, '"') !== false) {
                // Replace all strings in quotes (also in comments, but that's okay - removed in next step)
                $replaceStringCallback = function ($matches) {
                    return self::addStringReplacement($matches[1] . $matches[2] . $matches[1]);
                };
                $text = preg_replace('/\r/', '', $text);
                $text = preg_replace('/\\\\\n/', '_STRING_CSSLINEBREAK_', $text);
                $text = preg_replace('/\\\\"/', '_STRING_ESCAPEDDOUBLEQUOTE_', $text);
                $text = preg_replace('/\\\\\'/', '_STRING_ESCAPEDSINGLEQUOTE_', $text);
                $text = preg_replace_callback('/("|\')(.*?)\g{1}/', $replaceStringCallback, $text);
            }

            if (strpos($text, "data:") !== false) {
                // Replace all data URIs
                // (that are not in quotes and therefor were not replaced by the previous check)
                $replaceDataUriCallback = function ($matches) {
                    return self::addStringReplacement($matches[1]);
                };
                $text = preg_replace_callback(
                    '/(data:(?:[^;,]+)?(?:;charset=[^;,]+)?' .
                    '(?:;base64,(?:[A-Za-z0-9+\/]{4})*(?:[A-Za-z0-9+\/]{2}==|[A-Za-z0-9+\/]{3}=)?|,[-.a-zA-Z0-9_%]+))/',
                    $replaceDataUriCallback,
                    $text
                );
            }

            // Fast pre-check, to optimize performance
            if (strpos($text, "*") !== false) {
                // Strip multi-line comments (after string replace to keep comments in strings)
                $replaceCommentCallback = function ($matches) {
                    return self::addCommentReplacement($matches[1]);
                };
                $text = preg_replace_callback('/(\/\*.*?\*\/)/', $replaceCommentCallback, $text);
            }

            return $text;
        } else {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($text). "' for argument 'text' given."
            );
        }
    }

    /**
     * Replaces the string place holders wih the saved strings in a given text.
     *
     * @param string $text
     * @param bool $deletePlaceholders
     * @return string
     */
    public static function replaceStringPlaceholders($text, $deletePlaceholders = false)
    {
        if (is_string($text)) {
            // Fast pre-check, to optimize performance
            if (strpos($text, "_") !== false) {
                $replaceStringPlaceholders = function ($matches) use ($deletePlaceholders) {
                    $result = "";
                    if (isset(self::$replacements[$matches[1]])) {
                        $result = self::$replacements[$matches[1]];
                        $result = str_replace(
                            ['_STRING_CSSLINEBREAK_', '_STRING_ESCAPEDDOUBLEQUOTE_', '_STRING_ESCAPEDSINGLEQUOTE_'],
                            ["\\\n", '\\"', "\\'"],
                            $result
                        );
                        if ($deletePlaceholders === true) {
                            unset(self::$replacements[$matches[1]]);
                        }
                    }
                    return $result;
                };
                $text = preg_replace_callback('/(?:_STRING_([a-f0-9]{32})_)/', $replaceStringPlaceholders, $text);
            }
            return $text;
        } else {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($text). "' for argument 'text' given."
            );
        }
    }

    /**
     * Replaces the comment place holders wih the saved comments in a given text.
     *
     * @param string $text
     * @param bool $deletePlaceholders
     * @return string
     */
    public static function replaceCommentPlaceholders($text, $deletePlaceholders = false)
    {
        if (is_string($text)) {
            // Fast pre-check, to optimize performance
            if (strpos($text, "_") !== false) {
                $replaceStringPlaceholders = function ($matches) use ($deletePlaceholders) {
                    $result = "";
                    if (isset(self::$replacements[$matches[1]])) {
                        // Also so replace the string placeholders within the comments
                        $result = self::replaceStringPlaceholders(self::$replacements[$matches[1]], $deletePlaceholders);
                        if ($deletePlaceholders === true) {
                            unset(self::$replacements[$matches[1]]);
                        }
                    }
                    return $result;
                };
                $text = preg_replace_callback('/(?:_COMMENT_([a-f0-9]{32})_)/', $replaceStringPlaceholders, $text);
            }
            return $text;
        } else {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($text). "' for argument 'text' given."
            );
        }
    }

    /**
     * Removes the comment place holders from a given text.
     *
     * @param string $text
     * @param bool $deletePlaceholders
     * @return string
     */
    public static function removeCommentPlaceholders($text, $deletePlaceholders = false)
    {
        if (is_string($text)) {
            // Fast pre-check, to optimize performance
            if (strpos($text, "_") !== false) {
                $removeStringPlaceholders = function ($matches) use ($deletePlaceholders) {
                    if ($deletePlaceholders === true) {
                        if (isset(self::$replacements[$matches[1]])) {
                            unset(self::$replacements[$matches[1]]);
                        }
                    }
                    return "";
                };
                $text = preg_replace_callback('/(?:_COMMENT_([a-f0-9]{32})_)/', $removeStringPlaceholders, $text);
            }
            return $text;
        } else {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($text). "' for argument 'text' given."
            );
        }
    }

    /**
     * Adds a string replacement to the internal list.
     *
     * @param string $string
     * @return string
     */
    protected static function addStringReplacement($string)
    {
        return self::addReplacement($string, "STRING");
    }

    /**
     * Adds a comment replacement to the internal list.
     *
     * @param string $string
     * @return string
     */
    protected static function addCommentReplacement($string)
    {
        return self::addReplacement($string, "COMMENT");
    }

    /**
     * Adds a replacement to the internal list.
     *
     * @param string $string
     * @param string $prefix
     * @return string
     */
    protected static function addReplacement($string, $prefix)
    {
        if (is_string($string)) {
            if (is_string($prefix)) {
                $hash = md5(self::$counter);
                $placeholder = "_" . $prefix . "_" . $hash . "_";
                self::$replacements[$hash] = $string;
                self::$counter++;

                return $placeholder;
            } else {
                throw new \InvalidArgumentException(
                    "Invalid type '" . gettype($prefix). "' for argument 'prefix' given."
                );
            }
        } else {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($string). "' for argument 'string' given."
            );
        }
    }
}