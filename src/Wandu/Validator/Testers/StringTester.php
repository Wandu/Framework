<?php
namespace Wandu\Validator\Testers;

use Wandu\Validator\Contracts\TesterInterface;

class StringTester implements TesterInterface
{
    /**
     * {@inheritdoc}
     */
    public function test($data): bool
    {
        return is_string($data);
    }
}
