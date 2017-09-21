<?php
namespace Wandu\Validator\Contracts;

interface RuleNormalizable
{
    /**
     * @param string|array|callable|\Wandu\Validator\Contracts\Rule $rule
     * @return array
     */
    public function normalize($rule): array;
}
