<?php
namespace Crossjoin\Css\Format\Rule;

trait TraitConditions
{
    /**
     * @var array Condition array
     */
    protected $conditions = [];

    /**
     * Sets the conditions.
     *
     * @param ConditionAbstract[]|ConditionAbstract $conditions
     * @return $this
     */
    public function setConditions($conditions)
    {
        $this->conditions = [];
        if (!is_array($conditions)) {
            $conditions = [$conditions];
        }
        foreach ($conditions as $condition) {
            $this->addCondition($condition);
        }

        return $this;
    }

    /**
     * Adds a condition.
     *
     * @param ConditionAbstract $condition
     * @return $this
     */
    public function addCondition(ConditionAbstract $condition)
    {
        $this->conditions[] = $condition;

        return $this;
    }

    /**
     * Gets an array of conditions.
     *
     * @return ConditionAbstract[] array
     */
    public function getConditions()
    {
        return $this->conditions;
    }
}