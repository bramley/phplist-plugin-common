<?php
namespace Crossjoin\Css\Writer;

class Compact
extends WriterAbstract
{
    /**
     * Gets the options for the CSS generation.
     *
     * @param int $level
     * @return array
     */
    protected function getOptions($level)
    {
        return [
            "BaseAddComments"               => false,
            "BaseLastDeclarationSemicolon"  => false,
            "BaseIntend"                    => "",
            "CommentIntend"                 => "",
            "CommentLineBreak"              => "",
            "CharsetLineBreak"              => "",
            "DocumentFilterSeparator"       => ",",
            "DocumentRuleSetOpen"           => "{",
            "DocumentRuleSetClose"          => "}",
            "FontFaceCommentLineBreak"      => "",
            "FontFaceRuleSetOpen"           => "{",
            "FontFaceRuleSetIntend"         => "",
            "FontFaceRuleSetClose"          => "}",
            "FontFaceDeclarationIntend"     => "",
            "FontFaceDeclarationSeparator"  => ":",
            "FontFaceDeclarationLineBreak"  => "",
            "ImportLineBreak"               => "",
            "NamespaceLineBreak"            => "",
            "MediaQuerySeparator"           => ",",
            "MediaRuleSetOpen"              => "{",
            "MediaRuleSetClose"             => "}",
            "PageCommentLineBreak"          => "",
            "PageRuleSetOpen"               => "{",
            "PageRuleSetClose"              => "}",
            "PageDeclarationIntend"         => "",
            "PageDeclarationSeparator"      => ":",
            "PageDeclarationLineBreak"      => "",
            "SupportsRuleSetOpen"           => "{",
            "SupportsRuleSetClose"          => "}",
            "StyleCommentLineBreak"         => "",
            "StyleDeclarationsOpen"         => "{",
            "StyleDeclarationsClose"        => "}",
            "StyleSelectorSeparator"        => ",",
            "StyleDeclarationIntend"        => "",
            "StyleDeclarationSeparator"     => ":",
            "StyleDeclarationLineBreak"     => "",
            "KeyframesRuleSetOpen"          => "{",
            "KeyframesRuleSetClose"         => "}",
            "KeyframesSelectorSeparator"    => ",",
            "KeyframesDeclarationsOpen"     => "{",
            "KeyframesDeclarationsClose"    => "}",
            "KeyframesDeclarationIntend"    => "",
            "KeyframesDeclarationSeparator" => ":",
            "KeyframesDeclarationLineBreak" => "",
        ];
    }
}