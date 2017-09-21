<?php
namespace Wandu\Validator\Sample;

use Wandu\Validator\Contracts\Tester;

class SampleOverTenTester implements Tester
{
    /**
     * {@inheritdoc}
     */
    public function test($data, $origin = null, array $keys = []): bool
    {
        return $data > 10;
    }
}
