<?php
namespace Crossjoin\Css\Helper;

class Optimizer
{
    public static function optimizeDeclarationValue($value)
    {
        // Optimize color values: "#FFFFFF -> #FFF"
        if (strpos($value, '#')) {
            $value = preg_replace('/#([0-9a-f])\g{1}{5}/', '#\\1\\1\\1', $value);
        }

        if (strpos($value, '0')) {
            // Optimize zero values: "0px -> 0", "0.0px -> 0", ".000 -> 0"
            $value = preg_replace('/(?<=^|[ \r\n\t\f])(?:(?:0)?\.)?[0]+(?:in|pc|pt|px|cm|mm|%|em|ex|ch|rem|vw|vh|vmin|vmax|deg|grad|rad|turn|s|ms)/', '0', $value);

            // Optimize zero values: ".000 -> 0"
            $value = preg_replace('/(?<=^|[ \r\n\t\f])(?:(?:0)?\.)?[0]+(?=$|[ \r\n\t\f])/', '0', $value);

            // Optimize floats that can be written as integer: "50.0% -> 50%"
            $value = preg_replace('/\.[0]+(?=in|pc|pt|px|cm|mm|%|em|ex|ch|rem|vw|vh|vmin|vmax|deg|grad|rad|turn|s|ms|$|[ \r\n\t\f])/', '', $value);
        }

        return $value;
    }
}