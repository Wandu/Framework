<?php
namespace Wandu\Validator\Testers;

use Wandu\Validator\Contracts\TesterInterface;

class IntegerableTester implements TesterInterface
{
    /**
     * {@inheritdoc}
     */
    public function test($data): bool
    {
        return is_numeric($data) && is_int($data + 0);
    }
}
