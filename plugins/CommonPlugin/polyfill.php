<?php

if (!function_exists('array_column')) {
    function array_column($input, $columnKey, $indexKey = null)
    {
        $result = array();

        if (null === $indexKey) {
            if (null === $columnKey) {
                // trigger_error('What are you doing? Use array_values() instead!', E_USER_NOTICE);
                $result = array_values($input);
            } else {
                foreach ($input as $row) {
                    $result[] = $row[$columnKey];
                }
            }
        } else {
            if (null === $columnKey) {
                foreach ($input as $row) {
                    $result[$row[$indexKey]] = $row;
                }
            } else {
                foreach ($input as $row) {
                    $result[$row[$indexKey]] = $row[$columnKey];
                }
            }
        }

        return $result;
    }
}
