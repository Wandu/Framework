<?php
namespace Wandu\Validator;

use PHPUnit\Framework\TestCase;
use Wandu\Assertions;
use Wandu\Validator\Sample\SampleCharterRule;

class RuleNormalizerTest extends TestCase
{
    use Assertions;

    /** @var \Wandu\Validator\RuleNormalizer */
    protected $normalizer;

    public function setUp()
    {
        $this->normalizer = new RuleNormalizer();
    }
    
    public function testString()
    {
        static::assertEquals([
            '' => ['string'],
        ], $this->normalizer->normalize('string'));
    }

    public function testArray()
    {
        static::assertEquals([
            '' => ['string', 'email'],
        ], $this->normalizer->normalize(['string', 'email']));

        static::assertEquals([
            '' => ['string', 'email'],
        ], $this->normalizer->normalize([
            '' => ['string', 'email'],
        ]));

        static::assertEquals([
            '' => ['string', 'email'],
        ], $this->normalizer->normalize([
            'string',
            '' => ['email'],
        ]));
        static::assertEquals([
            '' => ['string', 'email'],
        ], $this->normalizer->normalize([
            'string',
            null => 'email',
        ]));
    }

    public function testCallable()
    {
        static::assertEquals([
            '' => ['string', 'email'],
        ], $this->normalizer->normalize(function () {
            return ['string', 'email'];
        }));
        
        static::assertEquals([
            'departure' => [
                'name' => ['' => ['string']],
                'address?' => ['' => ['string']],
                'lat?' => ['' => ['float']],
                'lng?' => ['' => ['float']],
            ],
            'arrival' => [
                'name' => ['' => ['string']],
                'address?' => ['' => ['string']],
                'lat?' => ['' => ['float']],
                'lng?' => ['' => ['float']],
            ],
            'waypoints[]' => [
                'name' => ['' => ['string']],
                'address?' => ['' => ['string']],
                'lat?' => ['' => ['float']],
                'lng?' => ['' => ['float']],
            ],
            'timeToGo' => ['' => ['int']],
            'timeToBack?' => ['' => ['int', 'greater_than:timeToGo']],
            'people' => ['' => ['int']],
        ], $this->normalizer->normalize(new SampleCharterRule()));
    }

    public function testRecursive()
    {
        static::assertEquals([
            '' => ['string', 'email'],
            'foo' => [
                '' => ['something'],
                'address' => ['' => ['string']],
                'lat' => ['' => ['float']],
                'lng' => ['' => ['float']],
            ],
        ], $this->normalizer->normalize(function () {
            return [
                'string', 'email',
                'foo' => [
                    'something',
                    'address' => 'string',
                    'lat' => 'float',
                    'lng' => 'float',
                ],
            ];
        }));
    }
}
