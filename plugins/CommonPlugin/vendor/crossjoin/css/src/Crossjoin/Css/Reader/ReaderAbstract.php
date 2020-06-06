<?php
namespace Crossjoin\Css\Reader;

use Crossjoin\Css\Format\Rule\AtCharset\CharsetRule;
use Crossjoin\Css\Format\Rule\AtDocument\DocumentRule;
use Crossjoin\Css\Format\Rule\AtFontFace\FontFaceDeclaration;
use Crossjoin\Css\Format\Rule\AtFontFace\FontFaceRule;
use Crossjoin\Css\Format\Rule\AtImport\ImportRule;
use Crossjoin\Css\Format\Rule\AtKeyframes\KeyframesRule;
use Crossjoin\Css\Format\Rule\AtKeyframes\KeyframesRuleSet;
use Crossjoin\Css\Format\Rule\AtMedia\MediaRule;
use Crossjoin\Css\Format\Rule\AtNamespace\NamespaceRule;
use Crossjoin\Css\Format\Rule\AtPage\PageRule;
use Crossjoin\Css\Format\Rule\AtRuleAbstract;
use Crossjoin\Css\Format\Rule\AtRuleConditionalAbstract;
use Crossjoin\Css\Format\Rule\AtSupports\SupportsRule;
use Crossjoin\Css\Format\Rule\HasRulesInterface;
use Crossjoin\Css\Format\Rule\AtKeyframes\KeyframesDeclaration;
use Crossjoin\Css\Format\Rule\AtPage\PageDeclaration;
use Crossjoin\Css\Format\Rule\RuleAbstract;
use Crossjoin\Css\Format\Rule\Style\StyleRuleSet;
use Crossjoin\Css\Format\Rule\Style\StyleDeclaration;
use Crossjoin\Css\Format\StyleSheet\StyleSheet;
use Crossjoin\Css\Helper\Placeholder;

abstract class ReaderAbstract
{
    protected $preparedContent;
    protected $charset;
    protected $protocolEncoding;
    protected $environmentEncoding;
    protected $styleSheet;

    /**
     * Gets the parsed style sheet.
     *
     * @return StyleSheet
     */
    public function getStyleSheet()
    {
        if ($this->styleSheet === null) {
            $this->parseCss();
        }
        return $this->styleSheet;
    }

    /**
     * Gets ths charset for the parsed style sheet.
     *
     * @return string
     */
    public function getCharset()
    {
        if ($this->charset === null) {
            $this->parseCss();
        }
        return $this->charset;
    }

    /**
     * Gets the CSS source content.
     *
     * @return string
     */
    abstract protected function getCssContent();

    /**
     * Gets a prepared version of the CSS content for parsing.
     *
     * @return string
     */
    protected function getPreparedCssContent()
    {
        if ($this->preparedContent === null) {
            $this->preparedContent = Placeholder::replaceStringsAndComments($this->getCssContent());

            // Move comments from the end of the line to the beginning to make it easier to
            // detect, to which rule they belong to
            $this->preparedContent = preg_replace(
                '/^([^\r\n]+)(_COMMENT_[a-f0-9]{32}_)([\r\n\t\f]+)/m',
                "\\2\n\\1\\3",
                $this->preparedContent
            );

            // Remove white space characters before comments
            $this->preparedContent = preg_replace(
                '/^([ \t\f]*)(_COMMENT_[a-f0-9]{32}_)/m',
                "\\2\n",
                $this->preparedContent
            );

            // Very important: Split long lines, because parsing long lines (char by char) costs a lot performance
            // (several thousand percent...).
            $this->preparedContent = str_replace(["{", "}", ";"], ["{\n", "}\n", ";\n"], $this->preparedContent);
        }

        return $this->preparedContent;
    }

    /**
     * Gets the CSS content as resource.
     *
     * @return resource
     */
    protected function getCssResource()
    {
        $handle = fopen("php://memory", "rw");
        fputs($handle, $this->getPreparedCssContent());
        rewind($handle);

        return $handle;
    }

    /**
     * @param string $charset
     * @return $this
     */
    protected function setCharset($charset)
    {
        if (is_string($charset)) {
            if (in_array($charset, mb_list_encodings())) {
                $this->charset = $charset;
            } else {
                throw new \InvalidArgumentException(
                    "Invalid value '" . $charset . "' for argument 'charset' given. Charset not supported."
                );
            }
        } else {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($charset). "' for argument 'charset' given."
            );
        }

        return $this;
    }

    /**
     * Set the encoding of the CSS file, as defined in HTTP (Content-Type header) or equivalent protocol,
     * when the CSS was loaded from an external source. This is used as a fall back value to determine
     * the charset of the CSS file.
     *
     * @param string $encoding
     * @return $this
     */
    protected function setProtocolEncoding($encoding)
    {
        if (is_string($encoding)) {
            if (in_array($encoding, mb_list_encodings())) {
                $this->protocolEncoding = $encoding;
            } else {
                throw new \InvalidArgumentException(
                    "Invalid value '" . $encoding . "' for argument 'encoding' given. Encoding not supported."
                );
            }
        } else {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($encoding). "' for argument 'encoding' given."
            );
        }

        return $this;
    }

    /**
     * Gets the encoding of the CSS file, as defined in HTTP (Content-Type header) or equivalent protocol,
     * when the CSS was loaded from an external source. This is used as a fall back value to determine
     * the charset of the CSS file.
     *
     * @return string|null
     */
    public function getProtocolEncoding()
    {
        return $this->protocolEncoding;
    }

    /**
     * Sets the environment encoding of the referencing (!) document, e.g. if defined in a link tag of an HTML page.
     * This is used as a fall back value to determine the charset of the CSS file.
     *
     * @param string $encoding
     * @return $this
     */
    public function setEnvironmentEncoding($encoding)
    {
        if (is_string($encoding)) {
            if (in_array($encoding, mb_list_encodings())) {
                $this->environmentEncoding = $encoding;
            } else {
                throw new \InvalidArgumentException(
                    "Invalid value '" . $encoding . "' for argument 'encoding' given. Encoding not supported."
                );
            }
        } else {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($encoding). "' for argument 'encoding' given."
            );
        }

        return $this;
    }

    /**
     * Gets the environment encoding of the referencing (!) document, e.g. if defined in a link tag of an HTML page.
     * This is used as a fall back value to determine the charset of the CSS file.
     *
     * @return string|null
     */
    public function getEnvironmentEncoding()
    {
        return $this->environmentEncoding;
    }

    /**
     * Parses the CSS source content.
     */
    protected function parseCss()
    {
        // Init variables
        $this->styleSheet = new StyleSheet();
        $cssContent = "";

        // Prepare CSS content to allow easy parsing;
        // temporarily replace all strings.
        $blockCount = 0;
        $ruleCount = 0;
        $ruleBlock = 0;
        $inBrackets = false;
        $charsetIgnored = true;
        $charsetReplaced = false;

        if (($handle = $this->getCssResource()) !== false) {
            // Determine charset in the correct order, defined in
            // http://www.w3.org/TR/css-syntax-3/#input-byte-stream
            $charset = null;
            $fileContainsBom = false;
            if (($firstLine = fgets($handle)) === false) {
                $firstLine = "";
            }

            // Check for a BOM and use it, if it exists. "The decode algorithm gives precedence to a byte order mark
            // (BOM), and only uses the fallback when none is found."
            $bom = pack("CCC", 0xef, 0xbb, 0xbf);
            if (strlen($firstLine) >= 3 && strncmp($firstLine, $bom, 3) === 0) {
                $charset = "UTF-8";
                $fileContainsBom = true;
            } else {
                // Fallback 1: The encoding defined in HTTP or equivalent protocol
                $charset = $this->getProtocolEncoding();

                // Fallback 2: The charset as defined in the CSS file
                if ($charset === null) {
                    if (preg_match('/^@charset\s+(["\'])([-a-zA-Z0-9_]+)\g{1}/i', $firstLine, $matches)) {
                        $charset = $matches[2];
                        $charsetIgnored = false;

                        // Auto-correction of the defined charset.
                        //"If the return value was utf-16be or utf-16le, use utf-8 as the fallback encoding".
                        if (in_array(strtoupper($charset), ["UTF-16BE", "UTF-16LE"])) {
                            $charset = "UTF-8";
                            $charsetReplaced = true;
                        }
                    }
                }

                // Fallback 3: The environment encoding of the referencing document
                if ($charset === null) {
                    $charset = $this->getEnvironmentEncoding();
                }

                // Fallback 4: Default to UTF-8
                if ($charset === null) {
                    $charset = "UTF-8";
                }
            }
            $this->setCharset($charset);

            // Set position back to the beginning (but skip BOMs)
            fseek($handle, ($fileContainsBom?3:0));

            while (($css = fgets($handle)) !== false) {
                // Required check to avoid errors when the encoding of the
                // file doesn't match the set/detected charset.
                if (mb_check_encoding($css, $charset) === false) {
                    throw new \RuntimeException("Invalid '$charset' encoding in CSS file.");
                }

                if (preg_match('/[^\x00-\x7f]/', $css)) {
                    $isAscii = false;
                    $strLen  = mb_strlen($css, $charset);
                } else {
                    $isAscii = true;
                    $strLen = strlen($css);
                }

                for ($i = 0, $j = $strLen; $i < $j; $i++) {
                    if ($isAscii === true) {
                        $char = $css[$i];
                    } else {
                        $char = mb_substr($css, $i, 1, $charset);
                    }

                    if ($char === "{") {
                        $blockCount++;
                        $cssContent .= "\n_BLOCKSTART_" . $blockCount . "_\n";

                        if ($ruleCount > $ruleBlock) {
                            $ruleBlock++;
                        }
                    } else if ($char === "}") {
                        $cssContent .= "\n_BLOCKEND_" . $blockCount . "_\n";
                        $blockCount--;


                        if ($blockCount < $ruleCount) {
                            if ($ruleCount > 0) {
                                $cssContent .= "\n_RULEEND_" . $ruleCount . "_\n";
                                $ruleCount--;
                            }
                        }
                        if ($ruleCount > 0) {
                            $ruleBlock--;
                        }
                    } elseif ($char === ";") {
                        $cssContent .= $char;
                        if ($ruleCount > 0 && $ruleBlock === 0) {
                            $cssContent .= "\n_RULEEND_1_\n";
                            $ruleCount--;
                        }
                    } else {
                        // Start new at-rule, but only if we are not in brackets, which still can occur, although we
                        // replaced all strings, e.g. in this case: "background: url(/images/myimage-@1x.png)".
                        if ($char === "@" && $inBrackets === false) {
                            if ($ruleCount > 0 && $blockCount === 0) {
                                $errorCss = Placeholder::replaceCommentPlaceholders(
                                    Placeholder::replaceStringPlaceholders($css)
                                );
                                throw new \RuntimeException("Parse error near '$errorCss'.");
                            }
                            $ruleCount++;
                            $cssContent .= "\n_RULESTART_" . $ruleCount . "_\n";
                        // Replace all white-space characters within rule definitions by normal space to get
                        // one line only
                        } elseif ($ruleCount >= $blockCount && in_array($char, ["\r", "\n", "\t", "\f"])) {
                            $char = " ";
                        } elseif ($char === "(") {
                            $inBrackets = true;
                        } elseif ($char === ")") {
                            $inBrackets = false;
                        }
                        $cssContent .= $char;
                    }
                }
            }

            // Auto-correction as required by CSS specs
            while ($blockCount > 0) {
                $cssContent .= "\n_BLOCKEND_" . $blockCount . "_\n";
                $blockCount--;
            }
            while ($ruleCount > 0) {
                $cssContent .= "\n_RULEEND_" . $ruleCount . "_\n";
                $ruleCount--;
            }
        }

        // Prettify...
        $cssContent = preg_replace('/;/', ";\n", $cssContent);
        $cssContent = preg_replace('/[\t\f]+/', "", $cssContent);
        $cssContent = preg_replace('/[ ]+/', " ", $cssContent);
        $cssContent = preg_replace('/(\n)[ ]|[ ](\n)/', "\\1\\2", $cssContent);
        $cssContent = preg_replace('/(?<!_)[ \t\n\r\f]*(:)[ \t\n\r\f]*/', "\\1", $cssContent);
        $cssContent = preg_replace('/([\r\n])+/', "\\1", $cssContent);
        $cssContent = preg_replace('/^\n|\n$/', "", $cssContent);
        $cssContent = preg_replace('/^(_COMMENT_[a-f0-9]{32}_)([^\r\n]+)/m', "\\1\n\\2", $cssContent);

        // Parse
        $lines = explode("\n", $cssContent);

        $ruleCount = 0;
        $blockCount = 0;
        $lastRuleContainers = [$this->styleSheet];
        $lastRuleSet = null;

        // Prepare vendor prefix regular expression
        $vendorPrefixRegExp = RuleAbstract::getVendorPrefixRegExp("/");

        $comment = null;
        $atRuleCharsetAllowed = true;
        $atRuleImportAllowed = true;
        $atRuleNamespaceAllowed = true;
        foreach ($lines as $line) {
            if (preg_match(
                '/^(?J)(?:_(?P<type>RULESTART|RULEEND|BLOCKSTART|BLOCKEND)_\d+_|_(?P<type>COMMENT)_[a-f0-9]{32}_)/',
                $line,
                $matches
            )) {
                if ($matches['type'] === 'RULESTART') {
                    $ruleCount++;
                } elseif ($matches['type'] === 'RULEEND') {
                    $ruleCount--;
                    if ($ruleCount === $blockCount) {
                        // Current rule finished
                    }
                } elseif ($matches['type'] === 'BLOCKSTART') {
                    $blockCount++;
                } elseif ($matches['type'] === 'BLOCKEND') {
                    $blockCount--;
                    if ($blockCount === $ruleCount) {
                        if ($comment !== null) {
                            /** @var AtRuleAbstract $lastRuleSet */
                            $lastRuleSet->addComment($comment);
                            $comment = null;
                        }

                        // Current rule set finished
                        $lastRuleSet = null;
                    } else {
                        if ($comment !== null) {
                            /** @var AtRuleAbstract[] $lastRuleContainers */
                            $lastRuleContainers[$ruleCount]->addComment($comment);
                            $comment = null;
                        }
                    }
                } elseif ($matches['type'] === 'COMMENT') {
                    $comment = rtrim($line);
                }
            } else {
                if ($blockCount < $ruleCount) {
                    // New rule opened
                    if (preg_match(
                        '/^@(' . $vendorPrefixRegExp . ')?([a-zA-Z_]{1}(?:[-a-zA-Z0-9_]*|[^[:ascii:]*]))/i',
                        trim($line),
                        $matches
                    )) {
                        $identifier   = mb_strtolower($matches[2], $this->getCharset());
                        switch($identifier) {
                            case "charset":
                                $atRule = new CharsetRule($line, $this->styleSheet);
                                break;
                            case "import":
                                $atRule = new ImportRule($line, $this->styleSheet);
                                break;
                            case "namespace":
                                $atRule = new NamespaceRule($line, $this->styleSheet);
                                break;
                            case "media":
                                $atRule = new MediaRule($line, $this->styleSheet);
                                break;
                            case "supports":
                                $atRule = new SupportsRule($line, $this->styleSheet);
                                break;
                            case "document":
                                $atRule = new DocumentRule($line, $this->styleSheet);
                                break;
                            case "font-face":
                                $atRule = new FontFaceRule($line, $this->styleSheet);
                                break;
                            case "page":
                                $atRule = new PageRule($line, $this->styleSheet);
                                break;
                            case "keyframes":
                                $atRule = new KeyframesRule($line, $this->styleSheet);
                                break;
                            default:
                                throw new \InvalidArgumentException("Unknown at rule identifier '$identifier'.");
                        }

                        // Add vendor prefix
                        if ($matches[1] !== "") {
                            $vendorPrefix = mb_strtolower($matches[1], $this->getCharset());
                            $atRule->setVendorPrefix($vendorPrefix);
                        }
                    } else {
                        throw new \InvalidArgumentException("Invalid rule format in '$line'.");
                    }

                    // IMPORTANT:
                    // - The @charset rule must be the first element in the style sheet and not be preceded by any
                    //   character.
                    // - Any @import rules must precede all other types of rules, except @charset rules (and other
                    //   @import rules).
                    // - Any @namespace rules must follow all @charset and @import rules (and other @namespace rules)
                    //   and precede all other non-ignored at-rules and style rules in a style-sheet.
                    if ($atRule instanceof CharsetRule) {
                        if ($atRuleCharsetAllowed === false) {
                            // As defined by CSS specs, the rule has been ignored, du to an invalid position in the
                            // style sheet. E.g. @charset must be the first content of the file, @import must be first
                            // or follow @charset or @import, and @namespace can only follow to @charset, @import or
                            // @namespace.
                            $atRule->setIsValid(false);
                            $atRule->addValidationError(
                                "Ignored @charset rule, because at wrong position in style sheet."
                            );
                        } elseif ($charsetIgnored === true) {
                            // As defined by CSS specs, the charset rule has been ignored, due to charset information
                            // from other sources (e.g. BOMs in the file or defined protocol encoding).
                            $atRule->setIsValid(false);
                            $atRule->addValidationError(
                                "Ignored @charset rule, because charset got from other source with higher priority."
                            );
                            $atRuleCharsetAllowed = false;
                        } elseif ($charsetReplaced === true) {
                            // As defined by CSS specs, the charset defined by the charset rule has been replaced with
                            // "UTF-8", because an UTF-16* charset has been used.
                            $atRule->setIsValid(false);
                            $atRule->addValidationError(
                                "Replaced charset in @charset rule with 'UTF-8', because defined charset is invalid."
                            );
                            $atRuleCharsetAllowed = false;
                        } else {
                            $atRuleCharsetAllowed = false;
                        }
                    } elseif ($atRule instanceof ImportRule) {
                        // As defined by CSS specs, the rule has been ignored, du to an invalid position in the style
                        // sheet. E.g. @charset must be the first content of the file, @import must be first or follow
                        // @charset or @import, and @namespace can only follow to @charset, @import or @namespace.
                        if ($atRuleImportAllowed === false) {
                            $atRule->setIsValid(false);
                            $atRule->addValidationError(
                                "Ignored @import rule, because at wrong position in style sheet."
                            );
                        }
                        $atRuleCharsetAllowed = false;
                    } elseif ($atRule instanceof NamespaceRule) {
                        // As defined by CSS specs, the rule has been ignored, du to an invalid position in the style
                        // sheet. E.g. @charset must be the first content of the file, @import must be first or follow
                        // @charset or @import, and @namespace can only follow to @charset, @import or @namespace.
                        if ($atRuleNamespaceAllowed === false) {
                            $atRule->setIsValid(false);
                            $atRule->addValidationError(
                                "Ignored @namespace rule, because at wrong position in style sheet."
                            );
                        }
                        $atRuleCharsetAllowed = false;
                        $atRuleImportAllowed = false;
                    } else {
                        $atRuleCharsetAllowed = false;
                        $atRuleImportAllowed = false;
                        $atRuleNamespaceAllowed = false;
                    }

                    $lastRuleContainers[$ruleCount-1]->addRule($atRule);
                    if ($atRule instanceof AtRuleConditionalAbstract) {
                        $lastRuleContainers[$ruleCount] = $atRule;
                    } elseif ($atRule instanceof KeyframesRule) {
                        $lastRuleContainers[$ruleCount] = $atRule;
                    } elseif ($atRule instanceof FontFaceRule) {
                        $lastRuleContainers[$ruleCount] = $atRule;
                        $lastRuleSet = $atRule;
                    } elseif ($atRule instanceof PageRule) {
                        $lastRuleContainers[$ruleCount] = $atRule;
                        $lastRuleSet = $atRule;
                    }
                // Not all at-rules contain other rule, e.g. in @page rules the rules are mixed with the
                // at-rule, so they directly contain declarations - this is filtered by checking for the
                // HasRulesInterface here.
                } elseif ($blockCount === $ruleCount && $lastRuleContainers[$ruleCount] instanceof HasRulesInterface) {
                    // New rule set opened
                    if ($lastRuleContainers[$ruleCount] instanceof KeyframesRule) {
                        $ruleSet = new KeyframesRuleSet($line, $this->styleSheet);
                    } else {
                        $ruleSet = new StyleRuleSet($line, $this->styleSheet);
                    }
                    if ($comment !== null) {
                        $ruleSet->addComment($comment);
                        $comment = null;
                    }
                    $lastRuleContainers[$ruleCount]->addRule($ruleSet);
                    $lastRuleSet = $ruleSet;
                    $atRuleCharsetAllowed = false;
                } elseif ($blockCount >= $ruleCount) {
                    // New declaration
                    if ($lastRuleSet !== null) {
                        $line = preg_replace('/[\s;]+$/', '', $line);

                        $invalidDeclaration = false;
                        if (strpos($line, ":") === false) {
                            $property = $line;
                            $value = "";
                            $invalidDeclaration = true;
                        } else {
                            list($property, $value) = explode(":", $line, 2);
                        }

                        $declaration = null;
                        if ($lastRuleContainers[$ruleCount] instanceof StyleSheet) {
                            $declaration = new StyleDeclaration($property, $value, $this->styleSheet);
                        } elseif ($lastRuleContainers[$ruleCount] instanceof AtRuleConditionalAbstract) {
                            $declaration = new StyleDeclaration($property, $value, $this->styleSheet);
                        } elseif ($lastRuleContainers[$ruleCount] instanceof KeyframesRule) {
                            $declaration = new KeyframesDeclaration($property, $value, $this->styleSheet);
                        } elseif ($lastRuleContainers[$ruleCount] instanceof FontFaceRule) {
                            $declaration = new FontFaceDeclaration($property, $value, $this->styleSheet);
                        } elseif ($lastRuleContainers[$ruleCount] instanceof PageRule) {
                            $declaration = new PageDeclaration($property, $value, $this->styleSheet);
                        }

                        if ($declaration !== null) {
                            if ($comment !== null) {
                                $declaration->addComment($comment);
                                $comment = null;
                            }
                            if ($invalidDeclaration === true) {
                                $declaration->setIsValid(false);
                                $declaration->addValidationError("Parse error. Invalid declaration at '$line'.");
                            }
                            $lastRuleSet->addDeclaration($declaration);
                        }
                    }
                    $atRuleCharsetAllowed = false;
                }
            }
        }
    }
}