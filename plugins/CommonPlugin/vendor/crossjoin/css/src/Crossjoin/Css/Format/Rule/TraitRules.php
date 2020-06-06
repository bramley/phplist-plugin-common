<?php
namespace Crossjoin\Css\Format\Rule;

trait TraitRules
{
    /**
     * @var array Array to save CSS rules
     */
    protected $rules = [];

    /**
     * Sets CSS rules.
     *
     * @param RuleAbstract[]|RuleAbstract $rules
     * @return $this
     */
    public function setRules($rules)
    {
        $this->rules = [];
        if (!is_array($rules)) {
            $rules = [$rules];
        }
        foreach ($rules as $rule) {
            $this->addRule($rule);
        }

        return $this;
    }

    /**
     * Adds a CSS rule.
     *
     * @param RuleAbstract $rule
     * @return $this
     */
    public function addRule(RuleAbstract $rule)
    {
        $this->rules[] = $rule;

        return $this;
    }

    /**
     * Gets CSS rules.
     *
     * @return RuleAbstract[]
     */
    public function getRules()
    {
        return $this->rules;
    }
}