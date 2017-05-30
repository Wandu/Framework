<?php
namespace Wandu\Validator\Contracts;

interface Rule
{
    /**
     * @param \Wandu\Validator\Contracts\RuleDefinition $rule
     * @return void
     */
    public function define(RuleDefinition $rule);
}
