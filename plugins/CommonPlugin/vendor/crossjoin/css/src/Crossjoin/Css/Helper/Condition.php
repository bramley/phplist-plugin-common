<?php
namespace Crossjoin\Css\Helper;

class Condition
{
    /**
     * Splits a condition list with nested conditions (CSS4) into multiple conditions (CSS3 compatible).
     *
     * @param string $conditionList
     * @param string $charset
     * @return array
     */
    public static function splitNestedConditions($conditionList, $charset = "UTF-8")
    {
        // Check arguments
        if (!is_string($conditionList)) {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($conditionList). "' for argument 'conditionList' given."
            );
        }
        if (!is_string($charset)) {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($charset). "' for argument 'charset' given."
            );
        } elseif (!in_array($charset, mb_list_encodings())) {
            throw new \InvalidArgumentException(
                "Invalid value '" . $charset . "' for argument 'charset' given. Charset not supported."
            );
        }

        // Remove at-rule and unnecessary white-spaces
        $conditionList = trim($conditionList, " \r\n\t\f");

        // Strings and comments should already be replaced, but let's check it again...
        $conditionList = Placeholder::replaceStringsAndComments($conditionList);

        $inFunction = false;
        $groupsOpened = 0;
        $enclosedChar = null;

        $conditions = [];
        $currentConditions = [""];
        $currentConditionKeys = [0];
        $subConditionList = "";

        if (preg_match('/[^\x00-\x7f]/', $conditionList)) {
            $isAscii = false;
            $strLen  = mb_strlen($conditionList, $charset);
            $getOr  = function($i) use ($conditionList){return strtolower(substr($conditionList, $i, 4));};
        } else {
            $isAscii = true;
            $strLen = strlen($conditionList);
            $getOr  = function($i) use ($charset, $conditionList){
                return mb_strtolower(mb_substr($conditionList, $i, 4, $charset), $charset);
            };
        }

        for ($i = 0, $j = $strLen; $i < $j; $i++) {
            if ($isAscii === true) {
                $char = $conditionList[$i];
            } else {
                $char = mb_substr($conditionList, $i, 1, $charset);
            }

            if ($char === "(") {
                if ($groupsOpened > 0) {
                    $subConditionList .= $char;
                } else {
                    if ($i > 0) {
                        $prevChar = mb_substr($conditionList, ($i - 1), 1, $charset);
                        if ($prevChar !== "(" && $prevChar !== " ") {
                            $inFunction = true;
                        }
                    }
                }

                if ($inFunction === false) {
                    $groupsOpened++;
                } else {
                    foreach ($currentConditionKeys as $index) {
                        $currentConditions[$index] .= $char;
                    }
                }
            } elseif ($char === ")") {
                if ($inFunction === false) {
                    $groupsOpened--;
                    if ($groupsOpened > 0) {
                        $subConditionList .= $char;
                    } else {
                        if ($subConditionList != "") {
                            $subConditions = self::splitNestedConditions($subConditionList, $charset);
                            $newConditions = [];
                            foreach ($subConditions as $subCondition) {
                                foreach ($currentConditions as $currentCondition) {
                                    $newConditions[] = trim($currentCondition, " \r\n\t\f") . " " . $subCondition;
                                }
                            }
                            $currentConditions = $newConditions;
                            $currentConditionKeys = array_keys($currentConditions);
                        }
                        $subConditionList = "";
                    }
                } else {
                    if ($groupsOpened > 0) {
                        $subConditionList .= $char;
                    }
                    foreach ($currentConditionKeys as $index) {
                        $currentConditions[$index] .= $char;
                    }
                    $inFunction = false;
                }
            } elseif ($char === " " && $getOr($i) === " or ") {
                if ($groupsOpened > 0) {
                    $subConditionList .= " or ";
                } else {
                    foreach ($currentConditions as $currentCondition) {
                        $conditions[] = trim($currentCondition, " \r\n\t\f");
                    }
                    $currentConditions = [""];
                    $currentConditionKeys = [0];
                }
                $i += (4 - 1);
            } else {
                if ($groupsOpened > 0) {
                    $subConditionList .= $char;
                } else {
                    foreach ($currentConditionKeys as $index) {
                        $currentConditions[$index] .= $char;
                    }
                }
            }
        }
        foreach ($currentConditions as $currentCondition) {
            $conditions[] = trim($currentCondition, " \r\n\t\f");
        }

        return $conditions;
    }
}