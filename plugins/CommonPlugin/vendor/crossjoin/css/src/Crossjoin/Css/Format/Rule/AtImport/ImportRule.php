<?php
namespace Crossjoin\Css\Format\Rule\AtImport;

use Crossjoin\Css\Format\Rule\AtMedia\MediaQuery;
use Crossjoin\Css\Format\Rule\AtMedia\MediaRule;
use Crossjoin\Css\Format\Rule\AtRuleAbstract;
use Crossjoin\Css\Format\StyleSheet\StyleSheet;
use Crossjoin\Css\Helper\Placeholder;
use Crossjoin\Css\Helper\Url;

class ImportRule
extends AtRuleAbstract
{
    // TODO: Implement CSS4 supports (not final, see: http://dev.w3.org/csswg/css-cascade-4/#at-ruledef-import)

    /**
     * @var string|null Import rule URL
     */
    protected $url;

    /**
     * @var MediaQuery[] Import rule media queries
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
     * Sets the import rule URL.
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Gets the import rule URL.
     *
     * @return string|null
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Sets the import rule media queries.
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
     * Adds an import rule media query.
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
     * Gets the import rule media queries.
     *
     * @return MediaQuery[] array
     */
    public function getQueries()
    {
        return $this->queries;
    }

    /**
     * Parses the import rule.
     *
     * @param string $ruleString
     */
    protected function parseRuleString($ruleString)
    {
        $charset = $this->getCharset();

        // Remove at-rule and unnecessary white-spaces
        $ruleString = preg_replace('/^[ \r\n\t\f]*@import[ \r\n\t\f]+/i', '', $ruleString);
        $ruleString = trim($ruleString, " \r\n\t\f");

        // Remove trailing semicolon
        $ruleString = rtrim($ruleString, ";");

        $isEscaped  = false;
        $inFunction = false;

        $url = "";
        $mediaQuery = "";
        $currentPart  = "";
        for ($i = 0, $j = mb_strlen($ruleString, $charset); $i < $j; $i++) {
            $char = mb_substr($ruleString, $i, 1, $charset);
            if ($char === "\\") {
                if ($isEscaped === false) {
                    $isEscaped = true;
                } else {
                    $isEscaped = false;
                }
            } else {
                if ($char === " ") {
                    if ($isEscaped === false) {
                        if ($inFunction == false) {
                            $currentPart = trim($currentPart, " \r\n\t\f");
                            if ($currentPart !== "") {
                                if ($url === "") {
                                    $url = trim($currentPart, " \r\n\t\f");
                                } else {
                                    $mediaQuery .= trim($currentPart, " \r\n\t\f");
                                    $mediaQuery .= $char;
                                }
                                $currentPart = "";
                            }
                        }
                    } else {
                        $currentPart .= $char;
                    }
                } elseif ($isEscaped === false && $char === "(") {
                    $inFunction = true;
                    $currentPart .= $char;
                } elseif ($isEscaped === false && $char === ")") {
                    $inFunction = false;
                    $currentPart .= $char;
                } else {
                    $currentPart .= $char;
                }
            }

            // Reset escaped flag
            if ($isEscaped === true && $char !== "\\") {
                $isEscaped = false;
            }
        }
        if ($currentPart !== "") {
            $currentPart = trim($currentPart, " \r\n\t\f");
            if ($currentPart !== "") {
                if ($url === "") {
                    $url = trim($currentPart, " \r\n\t\f");
                } else {
                    $mediaQuery .= trim($currentPart, " \r\n\t\f");
                }
            }
        }

        // Get URL value
        $url = Url::extractUrl($url);
        $this->setUrl($url);

        // Process media query
        $mediaRule = new MediaRule($mediaQuery);
        $this->setQueries($mediaRule->getQueries());
    }
}