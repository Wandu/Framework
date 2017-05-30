<?php
namespace Wandu\Validator\Testers;

use Wandu\Validator\Contracts\Tester;

class RegExpTester implements Tester
{
    /** @var string */
    protected $pattern;

    /**
     * @param string $pattern
     */
    public function __construct($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * {@inheritdoc}
     */
    public function test($data, $origin = null, array $keys = []): bool
    {
        return @preg_match($this->pattern, $data) > 0;
    }
}
