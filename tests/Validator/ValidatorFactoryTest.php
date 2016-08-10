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
}
