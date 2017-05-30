<?php
namespace Wandu\Validator\Contracts;

interface SanitizedRule extends Rule
{
    public function map($T);
}
