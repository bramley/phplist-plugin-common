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
