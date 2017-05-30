<?php
namespace Wandu\Validator\Contracts;

interface RuleDefinition
{
    /**
     * @param string $target
     * @param string[]|\Wandu\Validator\Contracts\Rule[]|\Closure[] ...$rules
     */
    public function prop(string $target, ...$rules);
}
