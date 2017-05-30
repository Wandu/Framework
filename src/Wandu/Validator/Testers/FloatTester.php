<?php
namespace Wandu\Validator\Testers;

use Wandu\Validator\Contracts\Tester;

class FloatTester implements Tester
{
    /**
     * {@inheritdoc}
     */
    public function test($data, $origin = null, array $keys = []): bool
    {
        return is_float($data);
    }
}
