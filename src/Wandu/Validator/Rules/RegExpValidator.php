<?php
namespace Wandu\Validator\Rules;

class RegExpValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'reg_exp:{{pattern}}';

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
    public function test($item)
    {
        return @preg_match($this->pattern, $item) > 0;
    }
}
