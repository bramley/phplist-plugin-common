<?php
namespace Crossjoin\Css\Writer;

use Crossjoin\Css\Format\Rule\AtCharset\CharsetRule;
use Crossjoin\Css\Format\Rule\AtDocument\DocumentRule;
use Crossjoin\Css\Format\Rule\AtFontFace\FontFaceDeclaration;
use Crossjoin\Css\Format\Rule\AtFontFace\FontFaceRule;
use Crossjoin\Css\Format\Rule\AtImport\ImportRule;
use Crossjoin\Css\Format\Rule\AtKeyframes\KeyframesDeclaration;
use Crossjoin\Css\Format\Rule\AtKeyframes\KeyframesRule;
use Crossjoin\Css\Format\Rule\AtKeyframes\KeyframesRuleSet;
use Crossjoin\Css\Format\Rule\AtMedia\MediaQuery;
use Crossjoin\Css\Format\Rule\AtMedia\MediaRule;
use Crossjoin\Css\Format\Rule\AtNamespace\NamespaceRule;
use Crossjoin\Css\Format\Rule\AtPage\PageDeclaration;
use Crossjoin\Css\Format\Rule\AtPage\PageRule;
use Crossjoin\Css\Format\Rule\AtPage\PageSelector;
use Crossjoin\Css\Format\Rule\AtSupports\SupportsRule;
use Crossjoin\Css\Format\Rule\RuleAbstract;
use Crossjoin\Css\Format\Rule\Style\StyleDeclaration;
use Crossjoin\Css\Format\Rule\Style\StyleRuleSet;
use Crossjoin\Css\Format\StyleSheet\StyleSheet;
use Crossjoin\Css\Format\StyleSheet\TraitStyleSheet;
use Crossjoin\Css\Helper\Url;

abstract class WriterAbstract
{
    use TraitStyleSheet;

    protected $content;
    protected $errorMessages;

    /**
     * @param StyleSheet $styleSheet
     */
    public function __construct(StyleSheet $styleSheet)
    {
        $this->setStyleSheet($styleSheet);
    }

    /**
     * Gets the generated CSS content.
     *
     * @return string
     */
    public function getContent()
    {
        if ($this->content === null) {
            $this->getRulesContent();
        }

        return $this->content;
    }

    /**
     * Gets the error messages for the generated CSS content.
     *
     * @return string[]
     */
    public function getErrors()
    {
        if ($this->errorMessages === null) {
            $this->getRulesContent();
        }

        return $this->errorMessages;
    }

    /**
     * Gets the options for the CSS generation.
     *
     * @param int $level
     * @return array
     */
    abstract protected function getOptions($level);

    /**
     * Generates the content for the given rules, depending on the set format options.
     *
     * @param RuleAbstract[] $rules
     * @param int $level
     * @return string
     */
    protected function getRulesContent(array $rules = null, $level = 0)
    {
        if ($level === 0) {
            $rules = $this->getStyleSheet()->getRules();
            $this->errorMessages = [];
        }

        // Get format options
        $options = $this->getOptions($level);

        $content = "";
        foreach ($rules as $rule) {
            if ($options["BaseAddComments"] === true) {
                if (!($rule instanceof CharsetRule)) {
                    $comments = $rule->getComments();
                    foreach ($comments as $comment) {
                        $content .= $options["CommentIntend"] . $comment . $options["CommentLineBreak"];
                    }
                }
            }

            // Skip invalid rules
            if ($rule->getIsValid() === false) {
                // Copy error to the writer
                foreach ($rule->getValidationErrors() as $validationError) {
                    $this->errorMessages[] = $validationError;
                }
                continue;
            }

            if ($rule instanceof CharsetRule) {
                // @charset must be at the beginning of the content
                if ($content === "") {
                    $content .= "@" . ((string)$rule->getVendorPrefix()) . "charset \"" . $rule->getValue() . "\";" . $options["CharsetLineBreak"];
                }
            } elseif ($rule instanceof ImportRule) {
                $content .= $options["BaseIntend"];
                $content .= "@" . ((string)$rule->getVendorPrefix()) . "import ";
                $content .= "url(\"" . Url::escapeUrl($rule->getUrl()) . "\")";

                $mediaQueryConcat = "";
                foreach ($rule->getQueries() as $mediaQuery) {
                    $content .= $mediaQueryConcat;
                    if ($mediaQuery->getIsOnly() === true) {
                        $content .= " only";
                    } else if ($mediaQuery->getIsNot() === true) {
                        $content .= " not";
                    }
                    // Filter default type "all"
                    if ($mediaQuery->getType() !== MediaQuery::TYPE_ALL) {
                        $content .= " " . $mediaQuery->getType();
                    }
                    $conditions = $mediaQuery->getConditions();
                    if (count($conditions) > 0) {
                        if ($mediaQuery->getType() !== MediaQuery::TYPE_ALL) {
                            $content .= " and";
                        }
                        $content .= " (";
                        $mediaConditionConcat = "";
                        foreach ($conditions as $condition) {
                            if ($condition->getIsValid() === true) {
                                $content .= $mediaConditionConcat . $condition->getValue();
                                $mediaConditionConcat = ") and (";
                            } else {
                                // Copy error to the writer
                                foreach ($condition->getValidationErrors() as $validationError) {
                                    $this->errorMessages[] = $validationError;
                                }
                            }
                        }
                        $content .= ")";
                    }
                    $mediaQueryConcat = ",";
                }
                $content .= ";" . $options["ImportLineBreak"];
            } elseif ($rule instanceof NamespaceRule) {
                $content .= $options["BaseIntend"];
                $content .= "@" . ((string)$rule->getVendorPrefix()) . "namespace";
                $prefix = $rule->getPrefix();
                if (!empty($prefix)) {
                    $content .= " " . $prefix;
                }
                $content .= " url(\"" . Url::escapeUrl($rule->getName()) . "\")";
                $content .= ";" . $options["NamespaceLineBreak"];
            } elseif ($rule instanceof DocumentRule) {
                // Prepare rule start content
                $ruleStartContent = $options["BaseIntend"];
                $ruleStartContent .= "@" . ((string)$rule->getVendorPrefix()) . "document";

                // Prepare rule filter content
                $concat = " ";
                $url = $rule->getUrl();
                if (!empty($url)) {
                    $ruleStartContent .= $concat . "url($url)";
                    $concat = $options["DocumentFilterSeparator"];
                }
                $urlPrefix = $rule->getUrlPrefix();
                if (!empty($urlPrefix)) {
                    $ruleStartContent .= $concat . "url-prefix($urlPrefix)";
                    $concat = $options["DocumentFilterSeparator"];
                }
                $domain = $rule->getDomain();
                if (!empty($domain)) {
                    $ruleStartContent .= $concat . "domain($domain)";
                    $concat = $options["DocumentFilterSeparator"];
                }
                $regexp = $rule->getRegexp();
                if (!empty($regexp)) {
                    $ruleStartContent .= $concat . "regexp($regexp)";
                }

                // Prepare rule content
                $ruleRuleContent = $this->getRulesContent($rule->getRules(), $level + 1);

                // Only add the content if valid rules set
                if ($ruleRuleContent !== "") {
                    $content .= $ruleStartContent;
                    $content .= $options["DocumentRuleSetOpen"];
                    $content .= $ruleRuleContent;
                    $content .= $options["DocumentRuleSetClose"];
                } else {
                    $this->errorMessages[] = "Empty document at-rule '$ruleStartContent' ignored.";
                }
            } elseif ($rule instanceof FontFaceRule) {
                // Prepare rule start content
                $ruleStartContent = $options["BaseIntend"];
                $ruleStartContent .= "@" . ((string)$rule->getVendorPrefix()) . "font-face";
                $ruleStartContent .= $options["FontFaceRuleSetOpen"];

                // Prepare rule declarations content
                /** @var FontFaceDeclaration[] $declarations */
                $declarations = $rule->getDeclarations();
                for ($i = 0, $j = count($declarations); $i < $j; $i++) {
                    if ($declarations[$i]->getIsValid() === true) {
                        if ($ruleStartContent !== "") {
                            $content .= $ruleStartContent;
                            $ruleStartContent = "";
                        }
                        if ($options["BaseAddComments"] === true) {
                            $comments = $declarations[$i]->getComments();
                            foreach ($comments as $comment) {
                                $content .= $options["FontFaceRuleSetIntend"] . $comment . $options["FontFaceCommentLineBreak"];
                            }
                        }
                        $content .= $options["FontFaceDeclarationIntend"] . $declarations[$i]->getProperty();
                        $content .= $options["FontFaceDeclarationSeparator"] . $declarations[$i]->getValue();
                        if ($options["BaseLastDeclarationSemicolon"] === true || $i < ($j - 1)) {
                            $content .= ";";
                        }
                        $content .= $options["FontFaceDeclarationLineBreak"];
                    } else {
                        // Copy error to the writer
                        foreach ($declarations[$i]->getValidationErrors() as $validationError) {
                            $this->errorMessages[] = $validationError;
                        }
                    }
                }

                // Only add the content if valid declarations set
                if ($ruleStartContent === "") {
                    $content .= $options["FontFaceRuleSetClose"];
                }
            } elseif ($rule instanceof KeyframesRule) {
                // Prepare rule content
                $ruleStartContent = $options["BaseIntend"];
                $ruleStartContent .= "@" . ((string)$rule->getVendorPrefix()) . "keyframes " ;
                $ruleStartContent .= $rule->getIdentifier();
                $ruleRulesContent = $this->getRulesContent($rule->getRules(), $level + 1);

                // Only add the content if valid rules set
                if ($ruleRulesContent !== "") {
                    $content .= $ruleStartContent;
                    $content .= $options["KeyframesRuleSetOpen"];
                    $content .= $ruleRulesContent;
                    $content .= $options["KeyframesRuleSetClose"];
                } else {
                    $this->errorMessages[] = "Empty keyframes at-rule '$ruleStartContent' ignored.";
                }
            } elseif ($rule instanceof MediaRule) {
                // Prepare rule content
                $ruleStartContent = $options["BaseIntend"];
                $ruleStartContent .= "@" . ((string)$rule->getVendorPrefix()) . "media";
                $mediaQueryConcat = " ";
                $ignoreAllType = true;
                foreach ($rule->getQueries() as $mediaQuery) {
                    $ruleStartContent .= $mediaQueryConcat;
                    if ($mediaQuery->getIsOnly() === true) {
                        $ruleStartContent .= "only ";
                        $ignoreAllType = false;
                    } elseif ($mediaQuery->getIsNot() === true) {
                        $ruleStartContent .= "not ";
                        $ignoreAllType = false;
                    }

                    if ($ignoreAllType === false || $mediaQuery->getType() !== MediaQuery::TYPE_ALL) {
                        $ruleStartContent .= $mediaQuery->getType();
                    }
                    $conditions = $mediaQuery->getConditions();
                    if (count($conditions) > 0) {
                        if ($ignoreAllType === false || $mediaQuery->getType() !== MediaQuery::TYPE_ALL) {
                            $ruleStartContent .= " and ";
                        }
                        $ruleStartContent .= "(";
                        $mediaConditionConcat = "";
                        foreach ($conditions as $condition) {
                            if ($condition->getIsValid() === true) {
                                $ruleStartContent .= $mediaConditionConcat . $condition->getValue();
                                $mediaConditionConcat = ") and (";
                            } else {
                                // Copy error to the writer
                                foreach ($condition->getValidationErrors() as $validationError) {
                                    $this->errorMessages[] = $validationError;
                                }
                            }
                        }
                        $ruleStartContent .= ")";
                    }
                    $mediaQueryConcat = $options["MediaQuerySeparator"];
                    $ignoreAllType = false;
                }
                $ruleRulesContent = $this->getRulesContent($rule->getRules(), $level + 1);

                // Only add the content if valid rules set
                if ($ruleRulesContent !== "") {
                    $content .= $ruleStartContent;
                    $content .= $options["MediaRuleSetOpen"];
                    $content .= $ruleRulesContent;
                    $content .= $options["MediaRuleSetClose"];
                } else {
                    $this->errorMessages[] = "Empty media at-rule '$ruleStartContent' ignored.";
                }
            } elseif ($rule instanceof PageRule) {
                $content .= $options["BaseIntend"];
                $content .= "@" . ((string)$rule->getVendorPrefix()) . "page";

                $selector = $rule->getSelector()->getValue();
                if ($selector !== PageSelector::SELECTOR_ALL) {
                    $content .= " " . $selector;
                }
                $content .= $options["PageRuleSetOpen"];

                /** @var PageDeclaration[] $declarations */
                $declarations = $rule->getDeclarations();
                for ($i = 0, $j = count($declarations); $i < $j; $i++) {
                    if ($declarations[$i]->getIsValid() === true) {
                        if ($options["BaseAddComments"] === true) {
                            $comments = $declarations[$i]->getComments();
                            foreach ($comments as $comment) {
                                $content .= $options["PageDeclarationIntend"] . $comment . $options["PageCommentLineBreak"];
                            }
                        }
                        $content .= $options["PageDeclarationIntend"] . $declarations[$i]->getProperty();
                        $content .= $options["PageDeclarationSeparator"] . $declarations[$i]->getValue();
                        if ($options["BaseLastDeclarationSemicolon"] === true || $i < ($j - 1)) {
                            $content .= ";";
                        }
                        $content .= $options["PageDeclarationLineBreak"];
                    } else {
                        // Copy error to the writer
                        foreach ($declarations[$i]->getValidationErrors() as $validationError) {
                            $this->errorMessages[] = $validationError;
                        }
                    }
                }

                $content .= $options["PageRuleSetClose"];
            } elseif ($rule instanceof SupportsRule) {
                // Prepare rule content
                $ruleStartContent = $options["BaseIntend"];
                $ruleStartContent .= "@" . ((string)$rule->getVendorPrefix()) . "supports ";
                $conditions = $rule->getConditions();
                if (count($conditions) > 0) {
                    $ruleStartContent .= "(";
                    $supportConditionConcat = "";
                    foreach ($conditions as $condition) {
                        if ($condition->getIsValid() === true) {
                            $ruleStartContent .= $supportConditionConcat . $condition->getValue();
                            $supportConditionConcat = ") and (";
                        } else {
                            // Copy error to the writer
                            foreach ($condition->getValidationErrors() as $validationError) {
                                $this->errorMessages[] = $validationError;
                            }
                        }
                    }
                    $ruleStartContent .= ")";
                }
                $ruleRulesContent = $this->getRulesContent($rule->getRules(), $level + 1);

                // Only add the content if valid rules set
                if ($ruleRulesContent !== "") {
                    $content .= $ruleStartContent;
                    $content .= $options["SupportsRuleSetOpen"];
                    $content .= $ruleRulesContent;
                    $content .= $options["SupportsRuleSetClose"];
                } else {
                    $this->errorMessages[] = "Empty supports at-rule '$ruleStartContent' ignored.";
                }
            } elseif ($rule instanceof StyleRuleSet) {
                // Prepare rule content
                $ruleStartContent = $options["BaseIntend"];
                $ruleSelectorContent = "";
                $concat = "";
                foreach ($rule->getSelectors() as $selector) {
                    if ($selector->getIsValid() === true) {
                        $ruleSelectorContent .= $concat . $selector->getValue();
                        $concat = $options["StyleSelectorSeparator"];
                    } else {
                        // Copy error to the writer
                        foreach ($selector->getValidationErrors() as $validationError) {
                            $this->errorMessages[] = $validationError;
                        }
                    }
                }

                // Only add the content if valid selectors set
                if ($ruleSelectorContent !== "") {
                    $ruleStartContent .= $ruleSelectorContent;
                    $ruleStartContent .= $options["StyleDeclarationsOpen"];

                    // Prepare rule declaration content
                    /** @var StyleDeclaration[] $declarations */
                    $ruleDeclarationContent = "";
                    $declarations = $rule->getDeclarations();
                    for ($i = 0, $j = count($declarations); $i < $j; $i++) {
                        if ($declarations[$i]->getIsValid() === true) {
                            if ($options["BaseAddComments"] === true) {
                                $comments = $declarations[$i]->getComments();
                                foreach ($comments as $comment) {
                                    $ruleDeclarationContent .= $options["StyleDeclarationIntend"] . $comment . $options["StyleCommentLineBreak"];
                                }
                            }
                            $important = $declarations[$i]->getIsImportant() ? " !important" : "";
                            $ruleDeclarationContent .= $options["StyleDeclarationIntend"] . $declarations[$i]->getProperty();
                            $ruleDeclarationContent .= $options["StyleDeclarationSeparator"] . $declarations[$i]->getValue() . $important;
                            if ($options["BaseLastDeclarationSemicolon"] === true || $i < ($j - 1)) {
                                $ruleDeclarationContent .= ";";
                            }
                            $ruleDeclarationContent .= $options["StyleDeclarationLineBreak"];
                        } else {
                            // Copy error to the writer
                            foreach ($declarations[$i]->getValidationErrors() as $validationError) {
                                $this->errorMessages[] = $validationError;
                            }
                        }
                    }

                    // Only add the content if valid declarations set
                    if ($ruleDeclarationContent !== "") {
                        $content .= $ruleStartContent;
                        $content .= $ruleDeclarationContent;
                        $content .= $options["StyleDeclarationsClose"];
                    } else {
                        $this->errorMessages[] = "Empty style rule set '$ruleSelectorContent' ignored.";
                    }
                }
            } elseif ($rule instanceof KeyframesRuleSet) {
                // Prepare rule content
                $ruleStartContent = $options["BaseIntend"];
                $ruleSelectorContent = "";
                $concat = "";
                foreach ($rule->getKeyframes() as $keyframe) {
                    if ($keyframe->getIsValid() === true) {
                        $ruleSelectorContent .= $concat . $keyframe->getValue();
                        $concat = $options["KeyframesSelectorSeparator"];
                    } else {
                        // Copy error to the writer
                        foreach ($keyframe->getValidationErrors() as $validationError) {
                            $this->errorMessages[] = $validationError;
                        }
                    }
                }

                // Only add the content if valid selectors set
                if ($ruleSelectorContent !== "") {
                    $ruleStartContent .= $ruleSelectorContent;
                    $ruleStartContent .= $options["KeyframesDeclarationsOpen"];

                    /** @var KeyframesDeclaration[] $declarations */
                    $ruleDeclarationContent = "";
                    $declarations = $rule->getDeclarations();
                    for ($i = 0, $j = count($declarations); $i < $j; $i++) {
                        if ($declarations[$i]->getIsValid() === true) {
                            $ruleDeclarationContent .= $options["KeyframesDeclarationIntend"] . $declarations[$i]->getProperty();
                            $ruleDeclarationContent .= $options["KeyframesDeclarationSeparator"] . $declarations[$i]->getValue();
                            if ($options["BaseLastDeclarationSemicolon"] === true || $i < ($j - 1)) {
                                $ruleDeclarationContent .= ";";
                            }
                            $ruleDeclarationContent .= $options["KeyframesDeclarationLineBreak"];
                        } else {
                            // Copy error to the writer
                            foreach ($declarations[$i]->getValidationErrors() as $validationError) {
                                $this->errorMessages[] = $validationError;
                            }
                        }
                    }

                    // Only add the content if valid declarations set
                    if ($ruleDeclarationContent !== "") {
                        $content .= $ruleStartContent;
                        $content .= $ruleDeclarationContent;
                        $content .= $options["KeyframesDeclarationsClose"];
                    } else {
                        $this->errorMessages[] = "Empty keyframes rule set '$ruleSelectorContent' ignored.";
                    }
                }
            }
        }

        // Save complete style sheet content in property
        if ($level === 0) {
            $this->content = $content;
        }

        return $content;
    }
}