<?php
namespace Wandu\Validator\Contracts;

interface RuleDefnition
{
    /**
     * @param string $target
     * @param string|string[]|\Wandu\Validator\Contracts\Rule|\Wandu\Validator\Contracts\Rule[] $rules
     */
    public function prop(string $target, $rules = null);
}
