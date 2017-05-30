<?php
namespace Wandu\Validator\Testers;

use Wandu\Validator\Contracts\Tester;

class PrintableTester implements Tester
{
    /**
     * {@inheritdoc}
     */
    public function test($data, $origin = null, array $keys = []): bool
    {
        if (is_object($data)) {
            return method_exists($data, '__toString');
        }
        return is_scalar($data);
    }
}
