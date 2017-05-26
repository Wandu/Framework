<?php
namespace Wandu\Validator\Contracts;

interface RuleInterface
{
    /**
     * @param \Wandu\Validator\Contracts\RuleDefinitionInterface $rule
     * @return void
     */
    public function define(RuleDefinitionInterface $rule);
}
