<?php
namespace Wandu\Validator\Contracts;

interface Rule
{
    /**
     * @param \Wandu\Validator\Contracts\RuleDefnition $rule
     * @return void
     */
    public function define(RuleDefnition $rule);
}
