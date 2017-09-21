<?php
namespace Wandu\Validator;

use PHPUnit\Framework\TestCase;
use Wandu\Assertions;
use Wandu\Validator\Sample\SampleCharterRule;

class ValidatorNormalizerTest extends TestCase
{
    use Assertions;

    /** @var \Wandu\Validator\ValidatorNormalizer */
    protected $normalizer;

    public function setUp()
    {
        $this->normalizer = new ValidatorNormalizer();
    }
    
    public function testString()
    {
        static::assertEquals([
            ['string'], [],
        ], $this->normalizer->normalize('string'));
    }

    public function testArray()
    {
        static::assertEquals([
            ['string', 'email'], [],
        ], $this->normalizer->normalize(['string', 'email']));

        static::assertEquals([
            ['string', 'email'], [],
        ], $this->normalizer->normalize([
            '' => ['string', 'email'],
        ]));

        static::assertEquals([
            ['string', 'email'], [],
        ], $this->normalizer->normalize([
            'string',
            '' => ['email'],
        ]));
        static::assertEquals([
            ['string', 'email'], [],
        ], $this->normalizer->normalize([
            'string',
            null => 'email',
        ]));
    }

    public function testCallable()
    {
        static::assertEquals([
            ['string', 'email'], [],
        ], $this->normalizer->normalize(function () {
            return ['string', 'email'];
        }));
        
        static::assertEquals([
            [], [
                [
                    ['departure', [], false],
                    [[], [
                        [['name', [], false], [['string'], []]],
                        [['address', [], true], [['string'], []]],
                        [['lat', [], true], [['float'], []]],
                        [['lng', [], true], [['float'], []]],
                    ]]
                ],
                [
                    ['arrival', [], false],
                    [[], [
                        [['name', [], false], [['string'], []]],
                        [['address', [], true], [['string'], []]],
                        [['lat', [], true], [['float'], []]],
                        [['lng', [], true], [['float'], []]],
                    ]]
                ],
                [
                    ['waypoints', [null], false],
                    [[], [
                        [['name', [], false], [['string'], []]],
                        [['address', [], true], [['string'], []]],
                        [['lat', [], true], [['float'], []]],
                        [['lng', [], true], [['float'], []]],
                    ]]
                ],
                [['timeToGo', [], false], [['int'], []]],
                [['timeToBack', [], true], [['int', 'greater_than:timeToGo'], []]],
                [['people', [], false], [['int'], []]],
            ],
        ], $this->normalizer->normalize(new SampleCharterRule()));
    }

    public function testRecursive()
    {
        static::assertEquals([
            ['string', 'email'], [
                [
                    ['foo', [], false],
                    [['something'], [
                        [['address', [], false], [['string'], []]],
                        [['lat', [], false], [['float'], []]],
                        [['lng', [], false], [['float'], []]],
                    ]],
                ],
            ]
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
