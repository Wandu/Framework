<?php
namespace Wandu\Validator\Testers;

use Wandu\Validator\Contracts\TesterInterface;

class FloatTester implements TesterInterface
{
    /**
     * {@inheritdoc}
     */
    public function test($data): bool
    {
        return is_float($data);
    }
}
