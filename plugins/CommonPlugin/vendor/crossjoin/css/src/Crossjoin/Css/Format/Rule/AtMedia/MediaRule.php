<?php
namespace Crossjoin\Css\Format\Rule\AtMedia;

use Crossjoin\Css\Format\Rule\AtRuleConditionalAbstract;
use Crossjoin\Css\Format\StyleSheet\StyleSheet;
use Crossjoin\Css\Helper\Condition;
use Crossjoin\Css\Helper\Placeholder;

class MediaRule
extends AtRuleConditionalAbstract
{
    /**
     * @var MediaQuery[] Media queries for the rule
     */
    protected $queries = [];

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
     * Sets the media queries for the rule.
     *
     * @param MediaQuery[]|MediaQuery $queries
     * @return $this
     */
    public function setQueries($queries)
    {
        $this->queries = [];
        if (!is_array($queries)) {
            $queries = [$queries];
        }
        foreach ($queries as $query) {
            $this->addQuery($query);
        }

        return $this;
    }

    /**
     * Adds a media query for the rule.
     *
     * @param MediaQuery $query
     * @return $this
     */
    public function addQuery(MediaQuery $query)
    {
        $this->queries[] = $query;

        return $this;
    }

    /**
     * Gets the media queries for the rule.
     *
     * @return MediaQuery[] array
     */
    public function getQueries()
    {
        return $this->queries;
    }

    /**
     * Parses the media rule.
     *
     * @param string $ruleString
     */
    protected function parseRuleString($ruleString)
    {
        if (is_string($ruleString)) {
            $charset = $this->getCharset();

            // Remove at-rule and unnecessary white-spaces
            $ruleString = preg_replace('/^[ \r\n\t\f]*@media[ \r\n\t\f]*/i', '', $ruleString);
            $ruleString = trim($ruleString, " \r\n\t\f");

            $groupsOpened = 0;
            $enclosedChar = null;

            $queries = [];
            $conditionList = "";
            $currentQueries = [];
            $currentConditions = [];

            if (preg_match('/[^\x00-\x7f]/', $ruleString)) {
                $isAscii = false;
                $strLen  = mb_strlen($ruleString, $charset);
            } else {
                $isAscii = true;
                $strLen = strlen($ruleString);
            }

            for ($i = 0, $j = $strLen; $i < $j; $i++) {
                if ($isAscii === true) {
                    $char = $ruleString[$i];
                } else {
                    $char = mb_substr($ruleString, $i, 1, $charset);
                }

                if (count($currentQueries) === 0) {
                    $currentQueries[] = "";
                }
                if ($char === "(") {
                    if ($groupsOpened > 0) {
                        $conditionList .= $char;
                    }
                    $groupsOpened++;
                } else if ($char === ")") {
                    $groupsOpened--;
                    if ($groupsOpened > 0) {
                        $conditionList .= $char;
                    } else {
                        if ($conditionList != "") {
                            $conditions = $this->parseConditionList($conditionList);
                            foreach ($conditions as $condition) {
                                $currentConditions[] = $condition;
                            }
                        }
                        $conditionList = "";
                    }
                } else if ($char === ",") {
                    if ($groupsOpened > 0) {
                        $conditionList .= $char;
                    } else {
                        foreach ($currentQueries as $currentQuery) {
                            $query = $this->getQueryInstanceFromQueryString($currentQuery);
                            if ($query !== null) {
                                if (count($currentConditions) > 0) {
                                    $query->setConditions($currentConditions);
                                }

                                $queries[] = $query;
                            }
                        }
                        $currentQueries = [];
                        $currentConditions = [];
                    }
                } else if ($char === " ") {
                    if ($groupsOpened > 0) {
                        $conditionList .= $char;
                    } else {
                        foreach (array_keys($currentQueries) as $index) {
                            $currentQueries[$index] .= $char;
                        }
                    }
                } else {
                    if ($groupsOpened > 0) {
                        $conditionList .= $char;
                    } else {
                        foreach (array_keys($currentQueries) as $index) {
                            $currentQueries[$index] .= $char;
                        }
                    }
                }
            }
            if ($groupsOpened > 0) {
                // Handle unclosed parenthesis following the spec:
                // "Because the parenthesized block is unclosed, it will contain the entire rest of the stylesheet
                // from that point (unless it happens to encounter an unmatched ')' character somewhere in the
                // stylesheet), and turn the entire thing into a not all media query."
                $this->setIsValid(false);
                $this->addValidationError("Parse error. Unclosed parenthesis at '$ruleString'.");

                $query = new MediaQuery(MediaQuery::TYPE_ALL);
                $query->setIsNot(true);
                $queries[] = $query;
            } else {
                foreach ($currentQueries as $currentQuery) {
                    $query = $this->getQueryInstanceFromQueryString($currentQuery);
                    if ($query !== null) {
                        if (count($currentConditions) > 0) {
                            $query->setConditions($currentConditions);
                        }
                        $queries[] = $query;
                    }
                }
            }

            // If not query set, default to type "all"
            if (count($queries) === 0) {
                $queries[] = new MediaQuery(MediaQuery::TYPE_ALL);
            }

            $this->setQueries($queries);
        } else {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($ruleString). "' for argument 'ruleString' given."
            );
        }
    }

    /**
     * Extracts the type from the query part of the media rule and returns a MediaQuery instance for it.
     *
     * @param $queryString
     * @return MediaQuery|null
     */
    protected function getQueryInstanceFromQueryString($queryString)
    {
        if (preg_match('/^[ \r\n\t\f]*(?:(only[ \r\n\t\f]+)|(not[ \r\n\t\f]+))?([^ \r\n\t\f]*)[ \r\n\t\f]*(?:(?:and)?[ \r\n\t\f]*)*$/iD', $queryString, $matches)) {
            $type = $matches[3] === "" ? MediaQuery::TYPE_ALL : $matches[3];
            $query = new MediaQuery($type);
            if (!empty($matches[1])) {
                $query->setIsOnly(true);
            }
            if (!empty($matches[2])) {
                $query->setIsNot(true);
            }
            return $query;
        }
        return null;
    }

    /**
     * Parses the condition part of the media rule.
     *
     * @param string $conditionList
     * @return MediaCondition[]
     */
    protected function parseConditionList($conditionList)
    {
        $charset = $this->getCharset();

        $conditions = [];
        foreach (Condition::splitNestedConditions($conditionList) as $normalizedConditionList) {
            $normalizedConditions = [];
            $currentCondition = "";

            if (preg_match('/[^\x00-\x7f]/', $normalizedConditionList)) {
                $isAscii = false;
                $strLen  = mb_strlen($normalizedConditionList, $charset);
                $getAnd  = function($i) use ($normalizedConditionList){return strtolower(substr($normalizedConditionList, $i, 5));};
            } else {
                $isAscii = true;
                $strLen = strlen($normalizedConditionList);
                $getAnd  = function($i) use ($charset, $normalizedConditionList){return mb_strtolower(mb_substr($normalizedConditionList, $i, 5, $charset), $charset);};
            }

            for ($i = 0, $j = $strLen; $i < $j; $i++) {
                if ($isAscii === true) {
                    //$char = substr($normalizedConditionList, $i, 1);
                    $char = $normalizedConditionList[$i];
                } else {
                    $char = mb_substr($normalizedConditionList, $i, 1, $charset);
                }

                if ($char === " " && $getAnd($i) === " and ") {
                    $normalizedConditions[] = new MediaCondition(trim($currentCondition, " \r\n\t\f"));
                    $currentCondition = "";
                    $i += (5 - 1);
                } else {
                    $currentCondition .= $char;
                }
            }
            $currentCondition = trim($currentCondition, " \r\n\t\f");
            if ($currentCondition !== "") {
                $normalizedConditions[] = new MediaCondition(trim($currentCondition, " \r\n\t\f"));
            }

            foreach ($normalizedConditions as $normalizedCondition) {
                $conditions[] = $normalizedCondition;
            }
        }

        return $conditions;
    }
}