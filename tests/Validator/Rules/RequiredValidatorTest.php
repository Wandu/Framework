<?php
namespace Wandu\Validator\Rules;

use Wandu\Validator\ValidatorTestCase;
use function Wandu\Validator\validator;

class RequiredValidatorTest extends ValidatorTestCase
{
    public function testRequired()
    {
        $validator = validator()->required();

        static::assertTrue($validator->validate(''));
        static::assertFalse($validator->validate(null));

        $validator->assert('');
        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert(null);
        }, ['required']);
    }
    
    public function testWithPipeline()
    {
        $validator1 = validator()->from('string');

        $validator1->assert(null); // safe
        $validator1->assert('hello world'); // safe
        
        $validator2 = validator()->from('required|string');
        $validator1->assert('hello world'); // safe
        $this->assertInvalidValueException(function () use ($validator2) {
            $validator2->assert(null);
        }, ['required']); // not safe
    }
    
    public function testWithArray()
    {
        $validator = validator()->array([
            'name' => 'required|string',
            'company' => [
                'name' => 'string',
                'age' => 'integer',
            ],
        ]);

        $validator->assert(['name' => 'george']);
        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert([]);
        }, ['required@name']);

        $validator = validator()->array([
            'name' => 'string',
            'company' => [
                'name' => 'required|string',
                'age' => 'integer',
            ],
        ]);

        $validator->assert(['company' => ['name' => 'gogle']]);
        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert([]);
        }, ['required@company.name']);
    }

    public function testWithObject()
    {
        $validator = validator()->object([
            'name' => 'required|string',
            'company' => (object)[
                'name' => 'string',
                'age' => 'integer',
            ],
        ]);

        $validator->assert((object)['name' => 'george']);
        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert((object)[]);
        }, ['required@name']);

        $validator = validator()->object([
            'name' => 'string',
            'company' => (object)[
                'name' => 'required|string',
                'age' => 'integer',
            ],
        ]);

        $validator->assert((object)['company' => (object)['name' => 'gogle']]);

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert((object)['company' => ['name' => 'gogle']]);
        }, ['object@company', 'required@company.name']);
        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert((object)[]);
        }, ['required@company.name']);
    }
}
