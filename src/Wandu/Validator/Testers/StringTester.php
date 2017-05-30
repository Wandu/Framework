<?php
namespace Wandu\Validator\Testers;

use Wandu\Validator\Contracts\Tester;

class StringTester implements Tester
{
    /**
     * {@inheritdoc}
     */
    public function test($data, $origin = null, array $keys = []): bool
    {
        return is_string($data);
    }
}
