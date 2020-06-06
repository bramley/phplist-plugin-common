<?php
namespace Crossjoin\Css\Format\Rule\AtDocument;

use Crossjoin\Css\Format\Rule\AtRuleConditionalAbstract;
use Crossjoin\Css\Format\StyleSheet\StyleSheet;
use Crossjoin\Css\Helper\Placeholder;

class DocumentRule
extends AtRuleConditionalAbstract
{
    /**
     * @var string|null Document URL filter
     */
    protected $url;

    /**
     * @var string|null Document URL prefix filter
     */
    protected $urlPrefix;

    /**
     * @var string|null Document domain filter
     */
    protected $domain;

    /**
     * @var string|null Document regular expression filter
     */
    protected $regexp;

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
     * Sets the document URL filter.
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        if (is_string($url)) {
            $this->url = $url;
        } else {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($url) . "' for argument 'url' given. String expected."
            );
        }
    }

    /**
     * Gets the document URL filter.
     *
     * @return string|null
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Sets the document URL prefix filter.
     *
     * @param string $urlPrefix
     */
    public function setUrlPrefix($urlPrefix)
    {
        if (is_string($urlPrefix)) {
            $this->urlPrefix = $urlPrefix;
        } else {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($urlPrefix) . "' for argument 'urlPrefix' given. String expected."
            );
        }
    }

    /**
     * Gets the document URL prefix filter.
     *
     * @return string|null
     */
    public function getUrlPrefix()
    {
        return $this->urlPrefix;
    }

    /**
     * Sets the document domain filter.
     *
     * @param string $domain
     */
    public function setDomain($domain)
    {
        if (is_string($domain)) {
            $this->domain = $domain;
        } else {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($domain) . "' for argument 'domain' given. String expected."
            );
        }
    }

    /**
     * Gets the document domain filter.
     *
     * @return string|null
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Sets the document regular expression filter.
     *
     * @param string $regexp
     */
    public function setRegexp($regexp)
    {
        if (is_string($regexp)) {
            $this->regexp = $regexp;
        } else {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($regexp) . "' for argument 'regexp' given. String expected."
            );
        }
    }

    /**
     * Gets the document regular expression filter.
     *
     * @return string|null
     */
    public function getRegexp()
    {
        return $this->regexp;
    }

    /**
     * Parses the charset rule.
     *
     * @param string $ruleString
     */
    protected function parseRuleString($ruleString)
    {
        if (is_string($ruleString)) {
            // Check for valid rule format
            // (with vendor prefix check to match e.g. "@-moz-document")
            if (preg_match(
                '/^[ \r\n\t\f]*@(' . self::getVendorPrefixRegExp("/") . ')?document[ \r\n\t\f]+(.*)$/i',
                $ruleString,
                $matches
            )) {
                $vendorPrefix = $matches[1];
                $ruleString = trim($matches[2], " \r\n\t\f");
                $charset = $this->getCharset();

                $inFunction = false;
                $isEscaped = false;

                $conditions = [];
                $currentCondition = "";
                $currentValue  = "";
                for ($i = 0, $j = mb_strlen($ruleString, $charset); $i < $j; $i++) {
                    $char = mb_substr($ruleString, $i, 1, $charset);
                    if ($char === "\\") {
                        if ($isEscaped === false) {
                            $isEscaped = true;
                        } else {
                            $isEscaped = false;
                        }
                    } else {
                        if ($char === "(") {
                            if ($isEscaped === false) {
                                $inFunction = true;
                                continue;
                            } else {
                                $currentValue .= $char;
                            }
                        } else if ($char === ")") {
                            if ($isEscaped === false) {
                                $conditions[$currentCondition] = trim($currentValue, " \r\n\t\f");
                                $currentCondition = "";
                                $currentValue = "";
                                $inFunction = false;
                                continue;
                            } else {
                                $currentValue .= $char;
                            }
                        } else if ($char === "," || $char === " ") {
                            if ($currentCondition === "" && $currentValue === "") {
                                continue;
                            } elseif ($currentValue !== "") {
                                $currentValue .= $char;
                            } else {
                                // something wrong here...
                            }
                        } else {
                            if ($inFunction === false) {
                                $currentCondition .= $char;
                            } else {
                                $currentValue .= $char;
                            }
                        }
                    }

                    // Reset escaped flag
                    if ($isEscaped === true && $char !== "\\") {
                        $isEscaped = false;
                    }
                }

                foreach ($conditions as $key => $value) {
                    $conditions[$key] = Placeholder::replaceStringPlaceholders($value, true);
                }

                if (isset($conditions["url"])) {
                    $this->setUrl($conditions["url"]);
                }
                if (isset($conditions["url-prefix"])) {
                    $this->setUrlPrefix($conditions["url-prefix"]);
                }
                if (isset($conditions["domain"])) {
                    $this->setDomain($conditions["domain"]);
                }
                if (isset($conditions["regexp"])) {
                    $this->setRegexp($conditions["regexp"]);
                }
                if ($vendorPrefix !== "") {
                    $this->setVendorPrefix($vendorPrefix);
                }
            } else {
                throw new \InvalidArgumentException("Invalid format for @document rule.");
            }
        } else {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($ruleString) . "' for argument 'ruleString' given. String expected."
            );
        }
    }
}