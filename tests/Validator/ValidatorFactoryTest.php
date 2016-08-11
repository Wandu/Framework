<?php
namespace Wandu\Validator;

use PHPUnit_Framework_TestCase;

class ValidatorFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $factory = new ValidatorFactory();
        
        // singleton
        $this->assertSame($factory->integer(), $factory->integer());
        $this->assertSame($factory->string(), $factory->string());
        
        // new creation : this is not singleton because it has parameter
        $this->assertNotSame($factory->min(5), $factory->min(5));
        $this->assertNotSame($factory->pipeline(), $factory->pipeline());
    }
    
    public function testFactoryViaHelper()
    {
        $this->assertInstanceOf(ValidatorFactory::class, validator());

        // singleton
        $this->assertSame(validator(), validator());
        $this->assertSame(validator()->integer(), validator()->integer());
        $this->assertSame(validator()->string(), validator()->string());
    }

    public function testCreateSimple()
    {
        $this->assertEquals(validator()->string(), validator()->from('string'));
        $this->assertEquals(validator()->string(), validator()->from('string:'));

        $this->assertNotEquals(validator()->min(6), validator()->from('min:5'));

        $this->assertEquals(validator()->lengthMin(20), validator()->from('length_min:20'));

        $this->assertEquals(validator()->min(5), validator()->from('min:5'));
        $this->assertEquals(validator()->min(5), validator()->from('min:5,,,'));
        $this->assertEquals(validator()->min(5), validator()->from('min:   5, , ,  '));
        $this->assertEquals(validator()->min(5), validator()->from('min:,,,5,,,'));
    }

    public function testCreatePipeline()
    {
        $this->assertEquals(
            validator()->pipeline()->string()->lengthMin(20),
            validator()->from('string|length_min:20')
        );
        $this->assertEquals(
            validator()->pipeline()->string()->lengthMin(20),
            validator()->from('string | length_min:20')
        );
    }

    public function testCreateArray()
    {
        $this->assertEquals(validator()->array([
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
        $this->assertEquals(validator()->array([
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
}
