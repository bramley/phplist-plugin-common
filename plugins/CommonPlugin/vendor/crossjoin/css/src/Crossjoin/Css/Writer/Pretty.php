<?php
namespace Crossjoin\Css\Writer;

class Pretty
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
        $lineBreak = "\r\n";
        $intendChar = "\t";
        $intendNum  = 1;
        $baseIntend = str_repeat($intendChar, $intendNum * $level);
        $nextIntend = str_repeat($intendChar, $intendNum * ($level + 1));
        $extraLineBreak = ($level === 0 ? $lineBreak : '');

        return [
            "BaseAddComments"               => true,
            "BaseLastDeclarationSemicolon"  => true,
            "BaseIntend"                    => $baseIntend,
            "CommentIntend"                 => $baseIntend,
            "CommentLineBreak"              => $lineBreak,
            "CharsetLineBreak"              => $lineBreak,
            "DocumentFilterSeparator"       => ", ",
            "DocumentRuleSetOpen"           => " {" . $lineBreak,
            "DocumentRuleSetClose"          => $baseIntend . "}" . $lineBreak . $extraLineBreak,
            "FontFaceCommentLineBreak"      => $lineBreak,
            "FontFaceRuleSetOpen"           => " {" . $lineBreak,
            "FontFaceRuleSetIntend"         => str_repeat($intendChar, $intendNum),
            "FontFaceRuleSetClose"          => $baseIntend . "}" . $lineBreak . $extraLineBreak,
            "FontFaceDeclarationIntend"     => $nextIntend,
            "FontFaceDeclarationSeparator"  => ": ",
            "FontFaceDeclarationLineBreak"  => $lineBreak,
            "ImportLineBreak"               => $lineBreak,
            "NamespaceLineBreak"            => $lineBreak,
            "MediaQuerySeparator"           => ", " . $lineBreak,
            "MediaRuleSetOpen"              => " {" . $lineBreak,
            "MediaRuleSetClose"             => $baseIntend . "}" . $lineBreak . $extraLineBreak,
            "PageCommentLineBreak"          => $lineBreak,
            "PageRuleSetOpen"               => " {" . $lineBreak,
            "PageRuleSetClose"              => $baseIntend . "}" . $lineBreak . $extraLineBreak,
            "PageDeclarationIntend"         => $nextIntend,
            "PageDeclarationSeparator"      => ": ",
            "PageDeclarationLineBreak"      => $lineBreak,
            "SupportsRuleSetOpen"           => " {" . $lineBreak,
            "SupportsRuleSetClose"          => $baseIntend . "}" . $lineBreak . $extraLineBreak,
            "StyleCommentLineBreak"         => $lineBreak,
            "StyleDeclarationsOpen"         => " {" . $lineBreak,
            "StyleDeclarationsClose"        => $baseIntend . "}" . $lineBreak . $extraLineBreak,
            "StyleSelectorSeparator"        => "," . $lineBreak . $baseIntend,
            "StyleDeclarationIntend"        => $nextIntend,
            "StyleDeclarationSeparator"     => ": ",
            "StyleDeclarationLineBreak"     => $lineBreak,
            "KeyframesRuleSetOpen"          => " {" . $lineBreak,
            "KeyframesRuleSetClose"         => $baseIntend . "}" . $lineBreak . $extraLineBreak,
            "KeyframesSelectorSeparator"    => "," . $lineBreak . $baseIntend,
            "KeyframesDeclarationsOpen"     => " {" . $lineBreak,
            "KeyframesDeclarationsClose"    => $baseIntend . "}" . $lineBreak,
            "KeyframesDeclarationIntend"    => $nextIntend,
            "KeyframesDeclarationSeparator" => ": ",
            "KeyframesDeclarationLineBreak" => $lineBreak,
        ];
    }
}