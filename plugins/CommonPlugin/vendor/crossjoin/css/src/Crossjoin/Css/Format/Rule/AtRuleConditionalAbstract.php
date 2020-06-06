<?php
namespace Crossjoin\Css\Format\Rule;

abstract class AtRuleConditionalAbstract
extends AtRuleAbstract
implements HasRulesInterface, RuleGroupableInterface
{
    use TraitRules;

    /**
     * Adds a rule.
     *
     * @param RuleGroupableInterface $rule
     * @return $this
     * @throws \Exception
     */
    public function addRule(RuleAbstract $rule)
    {
        // Check for allowed instances
        if ($rule instanceof RuleGroupableInterface) {
            $this->rules[] = $rule;
        } else {
            // Invalid rule instance, because only nested statements can be added to conditional group rules.
            $parentClassName = get_class($this);
            $childClassName = get_class($rule);
            $rule->setIsValid(false);
            $rule->addValidationError(
                "Rule instance of type '$childClassName' not allowed " .
                "in conditional group rule of type '$parentClassName'."
            );
            $this->rules[] = $rule;
        }

        return $this;
    }
}