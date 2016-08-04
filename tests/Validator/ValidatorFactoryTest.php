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

        $this->assertSame(validator()->integer(), $factory->integer()); // use static
    }
    
    public function testSingletonViaHelper()
    {
        $this->assertInstanceOf(ValidatorFactory::class, validator());

        // singleton
        $this->assertSame(validator(), validator());
        $this->assertSame(validator()->integer(), validator()->integer());
    }
    
    public function testFactoryViaHelper()
    {
        $this->assertSame(validator()->array(), validator()->array());

        // this is not singleton because it has parameter
        $this->assertNotSame(validator()->array([]), validator()->array());
    }
}
