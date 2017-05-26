<?php
namespace Wandu\Validator\Contracts;

interface RuleDefinitionInterface
{
    /**
     * @param string $target
     * @param string|string[]|\Wandu\Validator\Contracts\RuleInterface|\Wandu\Validator\Contracts\RuleInterface[] $rules
     */
    public function prop(string $target, $rules = null);
}
