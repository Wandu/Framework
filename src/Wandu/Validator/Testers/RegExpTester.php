<?php
namespace Wandu\Validator\Testers;

use Wandu\Validator\Contracts\TesterInterface;

class RegExpTester implements TesterInterface
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
    public function test($data): bool
    {
        return @preg_match($this->pattern, $data) > 0;
    }
}
