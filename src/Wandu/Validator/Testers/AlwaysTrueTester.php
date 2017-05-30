<?php
namespace Wandu\Validator\Testers;

use Wandu\Validator\Contracts\Tester;

/**
 * for test
 */
class AlwaysTrueTester implements Tester
{
    /**
     * {@inheritdoc}
     */
    public function test($data, $origin = null, array $keys = []): bool
    {
        return true;
    }
}
