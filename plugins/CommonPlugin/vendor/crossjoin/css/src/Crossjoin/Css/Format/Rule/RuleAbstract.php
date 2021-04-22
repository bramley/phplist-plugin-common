<?php
namespace Crossjoin\Css\Format\Rule;

use Crossjoin\Css\Format\StyleSheet\TraitStyleSheet;

abstract class RuleAbstract
{
    use TraitStyleSheet;
    use TraitComments;
    use TraitVendorPrefix;
    use TraitIsValid;
}