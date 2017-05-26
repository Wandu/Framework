<?php
namespace Wandu\Validator;

use PHPUnit\Framework\TestCase;
use Wandu\Validator\Contracts\RuleDefinitionInterface;
use Wandu\Validator\Contracts\RuleInterface;

class SanitizerTest extends TestCase
{
    public function testSanitize()
    {
        $sanitizer = sanitizer(new SanitizerTestPointRule(), SanitizerTestPoint::class);

        /** @var \Wandu\Validator\SanitizerTestPoint $point */
        $point = $sanitizer->sanitize(["name" => "hello"]);

        static::assertInstanceOf(SanitizerTestPoint::class, $point);
        static::assertSame("hello", $point->getName());
        static::assertNull($point->getAddress());
        static::assertNull($point->getLat());
        static::assertNull($point->getLng());
    }
}

class SanitizerTestPoint
{
    /** @var string */
    protected $name;

    /** @var ?string */
    protected $address;

    /** @var ?float */
    protected $lat;

    /** @var ?float */
    protected $lng;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return mixed
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @return mixed
     */
    public function getLng()
    {
        return $this->lng;
    }
}

class SanitizerTestPointRule implements RuleInterface
{
    public function define(RuleDefinitionInterface $rule)
    {
        $rule->prop("name", "string");
        $rule->prop("address?", "string");
        $rule->prop("lat?", "float");
        $rule->prop("lng?", "float");
    }
}
