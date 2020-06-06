<?php
namespace Crossjoin\PreMailer;

use Crossjoin\Css\Format\Rule\AtMedia\MediaQuery;
use Crossjoin\Css\Format\Rule\AtMedia\MediaRule;
use Crossjoin\Css\Format\Rule\RuleAbstract;
use Crossjoin\Css\Format\Rule\Style\StyleDeclaration;
use Crossjoin\Css\Format\Rule\Style\StyleRuleSet;
use Crossjoin\Css\Format\Rule\Style\StyleSelector;
use Crossjoin\Css\Reader\CssString;
use Crossjoin\Css\Writer\WriterAbstract;
use Symfony\Component\CssSelector\CssSelectorConverter;

abstract class PreMailerAbstract
{
    const OPTION_STYLE_TAG = 'styleTag';
    const OPTION_STYLE_TAG_BODY = 1;
    const OPTION_STYLE_TAG_HEAD = 2;
    const OPTION_STYLE_TAG_REMOVE = 3;

    const OPTION_HTML_COMMENTS = 'htmlComments';
    const OPTION_HTML_COMMENTS_KEEP = 1;
    const OPTION_HTML_COMMENTS_REMOVE = 2;

    const OPTION_HTML_CLASSES = 'htmlClasses';
    const OPTION_HTML_CLASSES_KEEP = 1;
    const OPTION_HTML_CLASSES_REMOVE = 2;

    const OPTION_TEXT_LINE_WIDTH = 'textLineWidth';

    const OPTION_CSS_WRITER_CLASS = 'cssWriterClass';
    const OPTION_CSS_WRITER_CLASS_COMPACT = '\Crossjoin\Css\Writer\Compact';
    const OPTION_CSS_WRITER_CLASS_PRETTY = '\Crossjoin\Css\Writer\Pretty';

    /**
     * @var array Options for the HTML/text generation
     */
    protected $options = [
        self::OPTION_STYLE_TAG => self::OPTION_STYLE_TAG_BODY,
        self::OPTION_HTML_CLASSES => self::OPTION_HTML_CLASSES_KEEP,
        self::OPTION_HTML_COMMENTS => self::OPTION_HTML_COMMENTS_REMOVE,
        self::OPTION_CSS_WRITER_CLASS => self::OPTION_CSS_WRITER_CLASS_COMPACT,
        self::OPTION_TEXT_LINE_WIDTH => 75,
    ];

    /**
     * @var string Charset for the HTML/text output
     */
    protected $charset = "UTF-8";

    /**
     * @var string Prepared HTML content
     */
    protected $html;

    /**
     * @var string Prepared text content
     */
    protected $text;

    /**
     * Sets the charset used in the HTML document and used for the output.
     *
     * @param string $charset
     * @return $this
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;

        return $this;
    }

    /**
     * Gets the charset used in the HTML document and used for the output.
     *
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * Sets an option for the generation of the mail.
     *
     * @param string $name
     * @param mixed $value
     */
    public function setOption($name, $value)
    {
        if (is_string($name)) {
            if (isset($this->options[$name])) {
                switch ($name) {
                    case self::OPTION_STYLE_TAG:
                        if (is_int($value)) {
                            if (!in_array($value, [
                                self::OPTION_STYLE_TAG_BODY,
                                self::OPTION_STYLE_TAG_HEAD,
                                self::OPTION_STYLE_TAG_REMOVE,
                            ])) {
                                throw new \InvalidArgumentException("Invalid value '$value' for option '$name'.");
                            }
                        } else {
                            throw new \InvalidArgumentException(
                                "Invalid type '" . gettype($value) . "' for value of option '$name'."
                            );
                        }
                        break;
                    case self::OPTION_HTML_CLASSES:
                        if (is_int($value)) {
                            if (!in_array($value, [
                                self::OPTION_HTML_CLASSES_REMOVE,
                                self::OPTION_HTML_CLASSES_KEEP,
                            ])) {
                                throw new \InvalidArgumentException("Invalid value '$value' for option '$name'.");
                            }
                        } else {
                            throw new \InvalidArgumentException(
                                "Invalid type '" . gettype($value) . "' for value of option '$name'."
                            );
                        }
                        break;
                    case self::OPTION_HTML_COMMENTS:
                        if (is_int($value)) {
                            if (!in_array($value, [
                                self::OPTION_HTML_COMMENTS_REMOVE,
                                self::OPTION_HTML_COMMENTS_KEEP,
                            ])) {
                                throw new \InvalidArgumentException("Invalid value '$value' for option '$name'.");
                            }
                        } else {
                            throw new \InvalidArgumentException(
                                "Invalid type '" . gettype($value) . "' for value of option '$name'."
                            );
                        }
                        break;
                    case self::OPTION_TEXT_LINE_WIDTH:
                        if (is_int($value)) {
                            if ($value <= 0) {
                                throw new \LengthException(
                                    "Value '" . gettype($value) . "' for option '$name' is to small."
                                );
                            }
                        } else {
                            throw new \InvalidArgumentException(
                                "Invalid type '" . gettype($value) . "' for value of option '$name'."
                            );
                        }
                        break;
                    case self::OPTION_CSS_WRITER_CLASS:
                        if (is_string($value)) {
                            if (is_subclass_of($value, '\Crossjoin\Css\Writer\WriterAbstract', true) === false) {
                                throw new \InvalidArgumentException(
                                    "Invalid value '$value' for option '$name'. " .
                                    "The given class has to be a subclass of \\Crossjoin\\Css\\Writer\\WriterAbstract."
                                );
                            }
                        } else {
                            throw new \InvalidArgumentException(
                                "Invalid type '" . gettype($value) . "' for value of option '$name'."
                            );
                        }
                }
                $this->options[$name] = $value;
            } else {
                throw new \InvalidArgumentException("An option with the name '$name' doesn't exist.");
            }
        } else {
            throw new \InvalidArgumentException("Invalid type '" . gettype($name) . "' for argument 'name'.");
        }
    }

    /**
     * Gets an option for the generation of the mail.
     *
     * @param string $name
     * @return mixed
     */
    public function getOption($name)
    {
        if (is_string($name)) {
            if (isset($this->options[$name])) {
                return $this->options[$name];
            } else {
                throw new \InvalidArgumentException("An option with the name '$name' doesn't exist.");
            }
        } else {
            throw new \InvalidArgumentException("Invalid type '" . gettype($name) . "' for argument 'name'.");
        }
    }

    /**
     * Gets the prepared HTML version of the mail.
     *
     * @return string
     */
    public function getHtml()
    {
        if ($this->html === null) {
            $this->prepareContent();
        }

        return $this->html;
    }

    /**
     * Gets the prepared text version of the mail.
     *
     * @return string
     */
    public function getText()
    {
        if ($this->text === null) {
            $this->prepareContent();
        }

        return $this->text;
    }

    /**
     * Gets the HTML content from the preferred source.
     *
     * @return string
     */
    abstract protected function getHtmlContent();

    /**
     * Prepares the mail HTML/text content.
     */
    protected function prepareContent()
    {
        $html = $this->getHtmlContent();

        if (class_exists("\\DOMDocument")) {
            $doc = new \DOMDocument();
            $doc->loadHTML($html);
        } else {
            throw new \RuntimeException("Required extension 'dom' seems to be missing.");
        }

        // Extract styles and remove style tags (optionally added again later).
        //
        // IMPORTANT: The style nodes need to be saved in an array first, or
        //            the replacement won't work correctly.
        $styleString = "";
        $styleNodes = [];
        foreach($doc->getElementsByTagName('style') as $styleNode) {
            $styleNodes[] = $styleNode;
        }
        foreach($styleNodes as $styleNode) {
            $skip = false;

            // Check if type is 'text/css' (defaults to it if not)
            $typeAttribute = $styleNode->attributes->getNamedItem("type");
            if ($typeAttribute !== null && (string)$typeAttribute->nodeValue !== "text/css") {
                $skip = true;
            }

            // Check media type is allowed (defaults to 'all')
            if ($skip === false) {
                $mediaAttribute = $styleNode->attributes->getNamedItem("media");
                if ($mediaAttribute !== null) {
                    $mediaAttribute = str_replace(" ", "", (string)$mediaAttribute->nodeValue);
                    $mediaTypes = explode(",", $mediaAttribute);
                    if (!in_array("all", $mediaTypes) && !in_array("screen", $mediaTypes)) {
                        $skip = true;
                    }
                }
            }

            // Add CSS if allowed
            if ($skip === false) {
                $styleString .= $styleNode->nodeValue . "\r\n";
            }

            $styleNode->parentNode->removeChild($styleNode);
        }

        // Prepare some variables
        $xpath = new \DOMXpath($doc);
        $reader = new CssString($styleString);
        $reader->setEnvironmentEncoding($this->getCharset());
        $rules = $reader->getStyleSheet()->getRules();

        // Set pseudo classes that can be set in a style attribute
        // and that are supported by the Symfony CssSelector (doesn't support CSS4 yet).
        $allowedPseudoClasses = [
            StyleSelector::PSEUDO_CLASS_FIRST_CHILD,
            StyleSelector::PSEUDO_CLASS_ROOT,
            StyleSelector::PSEUDO_CLASS_NTH_CHILD,
            StyleSelector::PSEUDO_CLASS_NTH_LAST_CHILD,
            StyleSelector::PSEUDO_CLASS_NTH_OF_TYPE,
            StyleSelector::PSEUDO_CLASS_NTH_LAST_OF_TYPE,
            StyleSelector::PSEUDO_CLASS_LAST_CHILD,
            StyleSelector::PSEUDO_CLASS_FIRST_OF_TYPE,
            StyleSelector::PSEUDO_CLASS_LAST_OF_TYPE,
            StyleSelector::PSEUDO_CLASS_ONLY_CHILD,
            StyleSelector::PSEUDO_CLASS_ONLY_OF_TYPE,
            StyleSelector::PSEUDO_CLASS_EMPTY,
            StyleSelector::PSEUDO_CLASS_NOT,
        ];

        // Extract all relevant style declarations
        $selectors = [];
        foreach ($this->getRelevantStyleRules($rules) as $styleRule) {
            foreach ($styleRule->getSelectors() as $selector) {
                // Check if the selector contains pseudo classes/elements that cannot
                // be mapped to elements
                $skip = false;
                foreach($selector->getPseudoClasses() as $pseudoClass) {
                    if (!in_array($pseudoClass, $allowedPseudoClasses)) {
                        $skip = true;
                        break;
                    }
                }
                if ($skip === false) {
                    $specificity = $selector->getSpecificity();
                    if (!isset($selectors[$specificity])) {
                        $selectors[$specificity] = [];
                    }
                    $selectorString = $selector->getValue();
                    if (!isset($selectors[$specificity][$selectorString])) {
                        $selectors[$specificity][$selectorString] = [];
                    }
                    foreach ($styleRule->getDeclarations() as $declaration) {
                        $selectors[$specificity][$selectorString][] = $declaration;
                    }
                }
            }
        }

        // Get all specificity values (to process the declarations in the correct order,
        // without sorting the array by key, which perhaps could result in a changed
        // order of selectors within the specificity).
        $specificityKeys = array_keys($selectors);
        sort($specificityKeys);

        // Temporary remove all existing style attributes, because they always have the highest priority
        // and are added again after all styles have been applied to the elements
        $elements = $xpath->query("descendant-or-self::*[@style]");
        /** @var \DOMElement $element */
        foreach ($elements as $element) {
            if ($element->attributes !== null) {
                $styleAttribute = $element->attributes->getNamedItem("style");

                $styleValue = "";
                if ($styleAttribute !== null) {
                    $styleValue = (string)$styleAttribute->nodeValue;
                }

                if ($styleValue !== "") {
                    $element->setAttribute('data-pre-mailer-original-style', $styleValue);
                    $element->removeAttribute('style');
                }
            }
        }

        // Process all style declarations in the correct order
        $cssSelectorConverter = new CssSelectorConverter();

        foreach ($specificityKeys as $specificityKey) {
            /** @var StyleDeclaration[] $declarations */
            foreach ($selectors[$specificityKey] as $selectorString => $declarations) {
                $xpathQuery = $cssSelectorConverter->toXPath($selectorString);
                $elements = $xpath->query($xpathQuery);
                /** @var \DOMElement $element */
                foreach ($elements as $element) {
                    if ($element->attributes !== null) {
                        $styleAttribute = $element->attributes->getNamedItem("style");

                        $styleValue = "";
                        if ($styleAttribute !== null) {
                            $styleValue = (string)$styleAttribute->nodeValue;
                        }

                        $concat = ($styleValue === "") ? "" : ";";
                        foreach ($declarations as $declaration) {
                            $styleValue .= $concat . $declaration->getProperty() . ":" . $declaration->getValue();
                            $concat = ";";
                        }

                        $element->setAttribute('style', $styleValue);
                    }
                }
            }
        }

        // Add temporarily removed style attributes again, after all styles have been applied to the elements
        $elements = $xpath->query("descendant-or-self::*[@data-pre-mailer-original-style]");
        /** @var \DOMElement $element */
        foreach ($elements as $element) {
            if ($element->attributes !== null) {
                $styleAttribute = $element->attributes->getNamedItem("style");
                $styleValue = "";
                if ($styleAttribute !== null) {
                    $styleValue = (string)$styleAttribute->nodeValue;
                }

                $originalStyleAttribute = $element->attributes->getNamedItem("data-pre-mailer-original-style");
                $originalStyleValue = "";
                if ($originalStyleAttribute !== null) {
                    $originalStyleValue = (string)$originalStyleAttribute->nodeValue;
                }

                if ($styleValue !== "" || $originalStyleValue !== "") {
                    $styleValue = ($styleValue !== "" ? $styleValue . ";" : "") . $originalStyleValue;
                    $element->setAttribute('style', $styleValue);
                    $element->removeAttribute('data-pre-mailer-original-style');
                }
            }
        }

        // Optionally remove class attributes in HTML tags
        $optionHtmlClasses = $this->getOption(self::OPTION_HTML_CLASSES);
        if ($optionHtmlClasses === self::OPTION_HTML_CLASSES_REMOVE) {
            $nodesWithClass = [];
            foreach ($xpath->query('descendant-or-self::*[@class]') as $nodeWithClass) {
                $nodesWithClass[] = $nodeWithClass;
            }
            /** @var \DOMElement $nodeWithClass */
            foreach ($nodesWithClass as $nodeWithClass) {
                $nodeWithClass->removeAttribute('class');
            }
        }

        // Optionally remove HTML comments
        $optionHtmlComments = $this->getOption(self::OPTION_HTML_COMMENTS);
        if ($optionHtmlComments === self::OPTION_HTML_COMMENTS_REMOVE) {
            $commentNodes = [];
            foreach ($xpath->query('//comment()') as $comment) {
                $commentNodes[] = $comment;
            }
            foreach ($commentNodes as $commentNode) {
                $commentNode->parentNode->removeChild($commentNode);
            }
        }

        // Write XPath document back to DOM document
        $newDoc = $xpath->document;

        // Generate text version (before adding the styles again)
        $this->text = $this->prepareText($newDoc);

        // Optionally add styles tag to the HEAD or the BODY of the document
        $optionStyleTag = $this->getOption(self::OPTION_STYLE_TAG);
        if ($optionStyleTag === self::OPTION_STYLE_TAG_BODY || $optionStyleTag === self::OPTION_STYLE_TAG_HEAD) {
            $cssWriterClass = $this->getOption(self::OPTION_CSS_WRITER_CLASS);
            /** @var WriterAbstract $cssWriter */
            $cssWriter = new $cssWriterClass($reader->getStyleSheet());
            $styleNode = $newDoc->createElement("style");
            $styleNode->nodeValue = $cssWriter->getContent();

            if ($optionStyleTag === self::OPTION_STYLE_TAG_BODY) {
                /** @var \DOMNode $bodyNode */
                foreach($newDoc->getElementsByTagName('body') as $bodyNode) {
                    $bodyNode->insertBefore($styleNode, $bodyNode->firstChild);
                    break;
                }
            } elseif ($optionStyleTag === self::OPTION_STYLE_TAG_HEAD) {
                /** @var \DOMNode $headNode */
                foreach($newDoc->getElementsByTagName('head') as $headNode) {
                    $headNode->appendChild($styleNode);
                    break;
                }
            }
        }

        // Generate HTML version
        $this->html = $newDoc->saveHTML();
    }

    /**
     * Prepares the mail text content.
     *
     * @param \DOMDocument $doc
     * @return string
     */
    protected function prepareText(\DOMDocument $doc)
    {
        $text = $this->convertHtmlToText($doc->childNodes);
        $charset = $this->getCharset();
        $textLineMaxLength = $this->getOption(self::OPTION_TEXT_LINE_WIDTH);

        $text = preg_replace_callback('/^([^\n]+)$/m', function($match) use ($charset, $textLineMaxLength) {
                $break = "\n";
                $parts = preg_split('/((?:\(\t[^\t]+\t\))|[^\p{L}\p{N}])/', $match[0], -1, PREG_SPLIT_DELIM_CAPTURE);

                $return = "";
                $brLength = mb_strlen(trim($break, "\r\n"), $charset);

                $lineLength = $brLength;
                foreach ($parts as $part) {
                    // Replace character before/after links with a zero width space,
                    // and mark links as non-breakable
                    $breakLongLines = true;
                    if (strpos($part, "\t")) {
                        $part = str_replace("\t", mb_convert_encoding("\xE2\x80\x8C", $charset, "UTF-8"), $part);
                        $breakLongLines = false;
                    }

                    // Get part length
                    $partLength = mb_strlen($part, $charset);

                    // Ignore trailing space characters if this would cause the line break
                    if (($lineLength + $partLength) === ($textLineMaxLength + 1)) {
                        $lastChar = mb_substr($part, -1, 1, $charset);
                        if ($lastChar === " ") {
                            $part = mb_substr($part, 0, -1, $charset);
                            $partLength--;
                        }
                    }

                    // Check if enough chars left to add the part
                    if (($lineLength + $partLength) <= $textLineMaxLength) {
                        $return .= $part;
                        $lineLength += $partLength;
                    // Check if the part is longer than the line (so that we need to break it)
                    } elseif ($partLength > ($textLineMaxLength - $brLength)) {
                        if ($breakLongLines === true) {
                            $addPart = mb_substr($part, 0, ($textLineMaxLength - $lineLength), $charset);
                            $return .= $addPart;
                            $lineLength = $brLength;

                            for ($i = mb_strlen($addPart, $charset), $j = $partLength; $i < $j; $i+=($textLineMaxLength - $brLength)) {
                                $addPart = $break . mb_substr($part, $i, ($textLineMaxLength - $brLength), $charset);
                                $return .= $addPart;
                                $lineLength = mb_strlen($addPart, $charset) - 1;
                            }
                        } else {
                            $return .= $break . trim($part) . $break;
                            $lineLength = $brLength;
                        }
                    // Add a break to add the part in the next line
                    } else {
                        $return .= $break . rtrim($part);
                        $lineLength = $brLength + $partLength;
                    }
                }
                return $return;
            }, $text);

        $text = preg_replace('/^\s+|\s+$/', '', $text);

        return $text;
    }

    /**
     * Converts HTML tags to text, to create a text version of an HTML document.
     *
     * @param \DOMNodeList $nodes
     * @return string
     */
    protected function convertHtmlToText(\DOMNodeList $nodes)
    {
        $text = "";

        /** @var \DOMElement $node */
        foreach ($nodes as $node) {
            $lineBreaksBefore = 0;
            $lineBreaksAfter = 0;
            $lineCharBefore = "";
            $lineCharAfter = "";
            $prefix = "";
            $suffix = "";

            if (in_array($node->nodeName, ["h1", "h2", "h3", "h4", "h5", "h6", "h"])) {
                $lineCharAfter = "=";
                $lineBreaksAfter = 2;
            } elseif (in_array($node->nodeName, ["p", "td"])) {
                $lineBreaksAfter = 2;
            } elseif (in_array($node->nodeName, ["div"])) {
                $lineBreaksAfter = 1;
            }

            if ($node->nodeName === "h1") {
                $lineCharBefore = "*";
                $lineCharAfter = "*";
            } elseif ($node->nodeName === "h2") {
                $lineCharBefore = "=";
            }

            if ($node->nodeName === '#text') {
                $textContent = html_entity_decode($node->textContent, ENT_COMPAT | ENT_HTML401, $this->getCharset());

                // Replace tabs (used to mark links below) and other control characters
                $textContent = preg_replace("/[\r\n\f\v\t]+/", "", $textContent);

                if ($textContent !== "") {
                    $text .= $textContent;
                }
            } elseif ($node->nodeName === 'a') {
                $href = "";
                if ($node->attributes !== null) {
                    $hrefAttribute = $node->attributes->getNamedItem("href");
                    if ($hrefAttribute !== null) {
                        $href = (string)$hrefAttribute->nodeValue;
                    }
                }
                if ($href !== "") {
                    $suffix = " (\t" . $href . "\t)";
                }
            } elseif ($node->nodeName === 'b' || $node->nodeName === 'strong') {
                $prefix = "*";
                $suffix = "*";
            } elseif ($node->nodeName === 'hr') {
                $text .= str_repeat('-', 75) . "\n\n";
            }

            if ($node->hasChildNodes()) {
                $text .= str_repeat("\n", $lineBreaksBefore);

                $addText = $this->convertHtmlToText($node->childNodes);

                $text .= $prefix;

                $text .= $lineCharBefore ? str_repeat($lineCharBefore, 75) . "\n" : "";
                $text .= $addText;
                $text .= $lineCharAfter ? "\n" . str_repeat($lineCharAfter, 75) . "\n" : "";

                $text .= $suffix;

                $text .= str_repeat("\n", $lineBreaksAfter);
            }
        }

        // Remove unnecessary white spaces at he beginning/end of lines
        $text = preg_replace("/(?:^[ \t\f]+([^ \t\f])|([^ \t\f])[ \t\f]+$)/m", "\\1\\2", $text);

        // Replace multiple line-breaks
        $text = preg_replace("/[\r\n]{2,}/", "\n\n", $text);

        return $text;
    }

    /**
     * Gets all generally relevant style rules.
     * The selectors/declarations are checked in detail in prepareContent().
     *
     * @param $rules RuleAbstract[]
     * @return StyleRuleSet[]
     */
    protected function getRelevantStyleRules(array $rules)
    {
        $styleRules = [];

        foreach ($rules as $rule) {
            if ($rule instanceof StyleRuleSet) {
                $styleRules[] = $rule;
            } else if ($rule instanceof MediaRule) {
                foreach ($rule->getQueries() as $mediaQuery) {
                    // Only add styles in media rules, if the media rule is valid for "all" and "screen" media types
                    // @note: http://premailer.dialect.ca/ also supports "handheld", but this is really useless
                    $type = $mediaQuery->getType();
                    if ($type === MediaQuery::TYPE_ALL || $type === MediaQuery::TYPE_SCREEN) {
                        // ...and only if there are no additional conditions (like screen width etc.)
                        // which are dynamic and therefore need to be ignored.
                        $conditionCount = count($mediaQuery->getConditions());
                        if ($conditionCount === 0) {
                            foreach ($this->getRelevantStyleRules($rule->getRules()) as $styleRule) {
                                $styleRules[] = $styleRule;
                            }
                            break;
                        }
                    }
                }
            }
        }

        return $styleRules;
    }
}
