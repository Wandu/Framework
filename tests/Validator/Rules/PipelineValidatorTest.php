<?php
namespace Wandu\Validator\Rules;

use Wandu\Validator\ValidatorTestCase;
use function Wandu\Validator\validator;

class PipelineValidatorTest extends ValidatorTestCase
{
    public function testMinAndMax()
    {
        $validator = validator()->pipeline()->min(10)->max(100);

        $validator->assert(10);
        $validator->assert(11);
        $validator->assert(50);
        $validator->assert(99);
        $validator->assert(100);

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert(9);
        }, [
            'min' => ['it must be greater or equal than 10'],
        ]);

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert(101);
        }, [
            'max' => ['it must be less or equal than 100'],
        ]);
    }

    public function testMinAndMin()
    {
        $validator = validator()->pipeline()->min(10)->min(30);

        $validator->assert(30);
        $validator->assert(31);

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert(9);
        }, [
            'min' => [
                'it must be greater or equal than 10',
                'it must be greater or equal than 30',
            ],
        ]);
    }
}
