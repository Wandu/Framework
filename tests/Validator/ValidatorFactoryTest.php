<?php
namespace Wandu\Validator;

use PHPUnit_Framework_TestCase;

class ValidatorFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $factory = new ValidatorFactory();
        
        // singleton
        static::assertSame($factory->integer(), $factory->integer());
        static::assertSame($factory->string(), $factory->string());
        
        // new creation : this is not singleton because it has parameter
        static::assertNotSame($factory->min(5), $factory->min(5));
        static::assertNotSame($factory->pipeline(), $factory->pipeline());
    }
    
    public function testFactoryViaHelper()
    {
        static::assertInstanceOf(ValidatorFactory::class, validator());

        // singleton
        static::assertSame(validator(), validator());
        static::assertSame(validator()->integer(), validator()->integer());
        static::assertSame(validator()->string(), validator()->string());
    }

    public function testCreateSimple()
    {
        static::assertEquals(validator()->string(), validator()->from('string'));
        static::assertEquals(validator()->string(), validator()->from('string:'));

        static::assertNotEquals(validator()->min(6), validator()->from('min:5'));

        static::assertEquals(validator()->lengthMin(20), validator()->from('length_min:20'));

        static::assertEquals(validator()->min(5), validator()->from('min:5'));
        static::assertEquals(validator()->min(5), validator()->from('min:5,,,'));
        static::assertEquals(validator()->min(5), validator()->from('min:   5, , ,  '));
        static::assertEquals(validator()->min(5), validator()->from('min:,,,5,,,'));
    }

    public function testCreatePipeline()
    {
        static::assertEquals(
            validator()->pipeline()->string()->lengthMin(20),
            validator()->from('string|length_min:20')
        );
        static::assertEquals(
            validator()->pipeline()->string()->lengthMin(20),
            validator()->from('string | length_min:20')
        );
    }

    public function testCreateArray()
    {
        static::assertEquals(validator()->array([
            'username' => validator()->string(),
            'license' => validator()->array([
                'expired_at' => validator()->integer(),
            ]),
        ]), validator()->from([
            'username' => 'string',
            'license' => [
                'expired_at' => 'integer',
            ]
        ]));

        // with pipeline
        static::assertEquals(validator()->array([
            'username' => validator()->pipeline()->string()->lengthMax(30),
            'license' => validator()->array([
                'expired_at' => validator()->pipeline()->integer()->min(20),
            ]),
        ]), validator()->from([
            'username' => 'string|length_max:30',
            'license' => [
                'expired_at' => 'integer|min:20',
            ]
        ]));
    }
    
    public function testCreateNot()
    {
        static::assertEquals(
            validator()->not(validator()->min(20)),
            validator()->from('!min:20')
        );
        
        static::assertEquals(validator()->array([
            'username' => validator()->pipeline()->string()->lengthMax(30),
            'license' => validator()->array([
                'expired_at' => validator()->pipeline()->integer()->not(validator()->min(20)),
            ]),
        ]), validator()->from([
            'username' => 'string|length_max:30',
            'license' => [
                'expired_at' => 'integer|!min:20',
            ]
        ]));
    }

    public function testCreateOptional()
    {
        static::assertEquals(
            validator()->not(validator()->optional(validator()->min(20))),
            validator()->from('!min?:20')
        );

        static::assertEquals(validator()->array([
            'username' => validator()->pipeline()->not(
                validator()->optional(
                    validator()->string()
                )
            )->lengthMax(30),
            'license' => validator()->array([
                'expired_at' => validator()->pipeline()->optional(validator()->integer())->not(validator()->min(20)),
            ]),
        ]), validator()->from([
            'username' => '!string?|length_max:30',
            'license' => [
                'expired_at' => 'integer?|!min:20',
            ]
        ]));
    }
}
