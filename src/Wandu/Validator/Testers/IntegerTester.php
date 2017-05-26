<?php
namespace Wandu\Validator\Testers;

use Wandu\Validator\Contracts\TesterInterface;

class IntegerTester implements TesterInterface
{
    /**
     * {@inheritdoc}
     */
    public function test($data): bool
    {
        return is_int($data);
    }
}
