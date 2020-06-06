<?php
namespace Crossjoin\Css\Format\Rule\AtSupports;

use Crossjoin\Css\Format\Rule\AtRuleConditionalAbstract;
use Crossjoin\Css\Format\Rule\ConditionAbstract;
use Crossjoin\Css\Format\Rule\TraitConditions;
use Crossjoin\Css\Format\StyleSheet\StyleSheet;
use Crossjoin\Css\Helper\Condition;
use Crossjoin\Css\Helper\Placeholder;

class SupportsRule
extends AtRuleConditionalAbstract
{
    use TraitConditions;

    /**
     * @param string|null $ruleString
     * @param StyleSheet|null $styleSheet
     */
    public function __construct($ruleString = null, StyleSheet $styleSheet = null)
    {
        if ($styleSheet !== null) {
            $this->setStyleSheet($styleSheet);
        }
        if ($ruleString !== null) {
            $ruleString = Placeholder::replaceStringsAndComments($ruleString);
            $ruleString = Placeholder::removeCommentPlaceholders($ruleString, true);
            $this->parseRuleString($ruleString);
        }
    }

    /**
     * Adds a supports condition.
     *
     * @param SupportsCondition $condition
     * @return $this
     */
    public function addCondition(ConditionAbstract $condition)
    {
        if ($condition instanceof SupportsCondition) {
            $this->conditions[] = $condition;
        } else {
            throw new \InvalidArgumentException(
                "Invalid condition instance. Instance of 'SupportsCondition' expected."
            );
        }

        return $this;
    }

    /**
     * Parses the supports rule.
     *
     * @param $ruleString
     */
    protected function parseRuleString($ruleString)
    {
        // Remove at-rule name and unnecessary white-spaces
        $ruleString = preg_replace('/^[ \r\n\t\f]*@supports[ \r\n\t\f]*/i', '', $ruleString);
        $ruleString = trim($ruleString, " \r\n\t\f");

        $charset = $this->getCharset();

        $conditions = [];

        foreach (Condition::splitNestedConditions($ruleString, $this->getCharset()) as $normalizedConditionList) {
            $normalizedConditions = [];
            $currentCondition = "";

            if (preg_match('/[^\x00-\x7f]/', $normalizedConditionList)) {
                $isAscii = false;
                $strLen  = mb_strlen($normalizedConditionList, $charset);
                $getAnd  = function($i) use ($normalizedConditionList){
                    return strtolower(substr($normalizedConditionList, $i, 5));
                };
            } else {
                $isAscii = true;
                $strLen = strlen($normalizedConditionList);
                $getAnd  = function($i) use ($charset, $normalizedConditionList){
                    return mb_strtolower(mb_substr($normalizedConditionList, $i, 5, $charset), $charset);
                };
            }

            for ($i = 0, $j = $strLen; $i < $j; $i++) {
                if ($isAscii === true) {
                    $char = $normalizedConditionList[$i];
                } else {
                    $char = mb_substr($normalizedConditionList, $i, 1, $charset);
                }

                if ($char === " " && $getAnd($i) === " and ") {
                    $normalizedConditions[] = new SupportsCondition(trim($currentCondition, " \r\n\t\f"));
                    $currentCondition = "";
                    $i += (5 - 1);
                } else {
                    $currentCondition .= $char;
                }
            }
            $currentCondition = trim($currentCondition, " \r\n\t\f");
            if ($currentCondition !== "") {
                $normalizedConditions[] = new SupportsCondition(trim($currentCondition, " \r\n\t\f"));
            }

            foreach ($normalizedConditions as $normalizedCondition) {
                $conditions[] = $normalizedCondition;
            }
        }

        $this->setConditions($conditions);
    }
}