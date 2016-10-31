<?php
namespace Wandu\Validator\Rules;

use Wandu\Validator\ValidatorTestCase;
use function Wandu\Validator\validator;

class ExistsValidatorTest extends ValidatorTestCase
{
    public function testExists()
    {
        $validator = validator()->exists();

        static::assertTrue($validator->validate(true));
        static::assertTrue($validator->validate(1));
        static::assertTrue($validator->validate('34'));
        static::assertTrue($validator->validate('0'));
        static::assertTrue($validator->validate(0));
        static::assertTrue($validator->validate(false));
        
        static::assertFalse($validator->validate(''));
        static::assertFalse($validator->validate(null));

        $validator->assert(true);
        $validator->assert(1);
        $validator->assert('34');
        $validator->assert('0');
        $validator->assert(0);
        $validator->assert(false);
        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert('');
        }, ['exists']);
        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert(null);
        }, ['exists']);
    }

    public function testWithPipeline()
    {
        $validator1 = validator()->from('integerable');

        $validator1->assert(null); // safe
        $validator1->assert(''); // safe
        $validator1->assert('3030'); // safe

        $validator2 = validator()->from('exists|integerable');
        $validator1->assert('3030'); // safe
        $this->assertInvalidValueException(function () use ($validator2) {
            $validator2->assert('');
        }, ['exists']); // not safe
        $this->assertInvalidValueException(function () use ($validator2) {
            $validator2->assert(null);
        }, ['exists']); // not safe
    }

    public function testWithArray()
    {
        $validator = validator()->array([
            'name' => 'exists|string',
            'company' => [
                'name' => 'string',
                'age' => 'integer',
            ],
        ]);

        $validator->assert(['name' => 'george']);
        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert([]);
        }, ['exists@name']);

        $validator = validator()->array([
            'name' => 'string',
            'company' => [
                'name' => 'exists|string',
                'age' => 'integer',
            ],
        ]);

        $validator->assert(['company' => ['name' => 'gogle']]);
        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert([]);
        }, ['exists@company.name']);
    }

    public function testWithObject()
    {
        $validator = validator()->object([
            'name' => 'exists|string',
            'company' => (object)[
                'name' => 'string',
                'age' => 'integer',
            ],
        ]);

        $validator->assert((object)['name' => 'george']);
        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert((object)[]);
        }, ['exists@name']);

        $validator = validator()->object([
            'name' => 'string',
            'company' => (object)[
                'name' => 'exists|string',
                'age' => 'integer',
            ],
        ]);

        $validator->assert((object)['company' => (object)['name' => 'gogle']]);

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert((object)['company' => ['name' => 'gogle']]);
        }, ['object@company', 'exists@company.name']);
        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert((object)[]);
        }, ['exists@company.name']);
    }
}
