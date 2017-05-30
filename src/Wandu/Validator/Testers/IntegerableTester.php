<?php
namespace Wandu\Validator\Testers;

use Wandu\Validator\Contracts\Tester;

class IntegerableTester implements Tester
{
    /**
     * {@inheritdoc}
     */
    public function test($data, $origin = null, array $keys = []): bool
    {
        return is_numeric($data) && is_int($data + 0);
    }
}
