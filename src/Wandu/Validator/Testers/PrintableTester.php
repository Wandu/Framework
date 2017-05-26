<?php
namespace Wandu\Validator\Testers;

use Wandu\Validator\Contracts\TesterInterface;

class PrintableTester implements TesterInterface
{
    /**
     * {@inheritdoc}
     */
    public function test($data): bool
    {
        if (is_object($data)) {
            return method_exists($data, '__toString');
        }
        return is_scalar($data);
    }
}
