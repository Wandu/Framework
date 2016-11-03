<?php
namespace Wandu\Validator\Rules;

use ArrayObject;
use Illuminate\Support\Collection;
use Wandu\Validator\ValidatorTestCase;
use Illuminate\Database\Eloquent\Model;
use function Wandu\Validator\validator;

class IterableValidatorTest extends ValidatorTestCase
{
    public function testNullCollectionValidate()
    {
        static::assertTrue(validator()->iterable()->validate(null));
        static::assertTrue(validator()->iterable()->validate([]));
        static::assertTrue(validator()->iterable()->validate(new ArrayObject([])));
        static::assertTrue(validator()->iterable()->validate(new Collection([1, 2, 3]))); // also use Laravel's Collection.
        static::assertTrue(validator()->iterable()->validate(['3', 0, 1, 2, 'something']));

        static::assertFalse(validator()->iterable()->validate((object)[]));
        static::assertFalse(validator()->iterable()->validate("30"));
        static::assertFalse(validator()->iterable()->validate([
            'hello' => 'world',
        ]));
        static::assertFalse(validator()->iterable()->validate(new Collection([
            'id' => 1,
        ]))); // but cannot use Laravel's Assoc Collection.
        static::assertFalse(validator()->iterable()->validate(new IterableTestUser([]))); // cannot use Laravel's Model

    }

    public function testValidate()
    {
        static::assertTrue(validator()->iterable('integer')->validate([30, 40, 50, 60]));
        static::assertFalse(validator()->iterable('integer')->validate([30, '40', 50, 60]));

        // ignore other key 
        static::assertTrue(validator()->iterable([
            'age' => 'integer',
        ])->validate([
            ['age' => 30, 'other' => 'other...'],
        ]));

        static::assertFalse(validator()->iterable([
            'age' => 'integer',
        ])->validate(['age' => 30, 'other' => 'other...']));
    }

    public function testAssertMethod()
    {
        $validator = validator()->iterable(['name' => 'string', 'age' => 'integer',]);

        // valid
        $validator->assert([]);
        $validator->assert([
            [
                'age' => 30
            ],
        ]);
        $validator->assert([
            [
                'name' => 'wandu',
                'age' => 30,
            ],
        ]);
        $validator->assert([
            [
                'name' => 'wandu',
                'age' => 30,
            ],
            null,
            '',
        ]);

        static::assertInvalidValueException(function () use ($validator) {
            $validator->assert('string');
        }, [
            'iterable',
        ]);
        static::assertInvalidValueException(function () use ($validator) {
            $validator->assert([
                [
                    'name' => 3030,
                    'age' => 'string',
                ],
                null,
                '',
            ]);
        }, [
            'string@0.name',
            'integer@0.age',
        ]);
    }

    public function testAssertCollectionOfArray()
    {
        $validator = validator()->iterable([
            'name' => 'string',
            'company' => [
                'name' => 'string',
                'age' => 'integer',
            ],
        ]);

        $validator->assert([]);
        $validator->assert([
            [
                'company' => [],
            ],
        ]);
        $validator->assert([
            [
                'name' => 'name string',
                'company' => [
                    'name' => 'string',
                    'age' => 38
                ],
            ],
            [
                'name' => 'name string',
                'company' => [
                    'name' => 'string',
                    'age' => 38
                ],
            ]
        ]);

        static::assertInvalidValueException(function () use ($validator) {
            $validator->assert('string');
        }, [
            'iterable',
        ]);
        static::assertInvalidValueException(function () use ($validator) {
            $validator->assert([
                'what?',
                [
                    'name' => 30,
                    'company' => [
                        'name' => 'string',
                        'age' => 38
                    ],
                ],
                [
                    'name' => 'name string',
                    'company' => [
                        'name' => 3030,
                    ],
                ]
            ]);
        }, [
            'array@0',
            'string@1.name',
            'string@2.company.name',
        ]);
    }

    public function testAssertArrayOfCollection()
    {
        $validator = validator()->from([
            'name' => 'string',
            'children' => validator()->iterable([
                'name' => 'string',
                'company' => [
                    'name' => 'string',
                    'age' => 'integer',
                ],
            ]),
        ]);
        $validator->assert([
            'name' => 'Alex',
        ]);
        $validator->assert([
            'name' => 'Alex',
            'children' => null,
        ]);
        $validator->assert([
            'name' => 'Alex',
            'children' => [],
        ]);
        $validator->assert([
            'name' => 'Alex',
            'children' => [
                [
                    'name' => 'name string',
                    'company' => [
                        'name' => 'string',
                        'age' => 38
                    ],
                ],
                [
                    'name' => 'name string',
                    'company' => [
                        'name' => 'string',
                        'age' => 38
                    ],
                ]
            ],
        ]);

        static::assertInvalidValueException(function () use ($validator) {
            $validator->assert([
                'name' => 'Alex',
                'children' => [
                    [
                        'name' => 30303030,
                    ],
                    [
                        'name' => 'name string',
                        'company' => [
                            'age' => 'string age'
                        ],
                    ]
                ],
            ]);
        }, [
            'string@children.0.name',
            'integer@children.1.company.age',
        ]);
    }

    public function testEloquentCollection()
    {
        $validator = validator()->arrayable([
            'name' => 'string',
            'children' => validator()->iterable(validator()->arrayable([
                'name' => 'required|string',
            ])),
        ]);

        $user = new IterableTestUser();
        $children = new Collection([
            new IterableTestUser(['name' => 'alex']),
            new IterableTestUser(['name' => 'lily'])
        ]);
        $user->setRelation('children', $children);

        $validator->assert($user);

        static::assertInvalidValueException(function () use ($validator) {
            $user = new IterableTestUser();
            $children = new Collection([
                new IterableTestUser(['name' => 3030]),
                new IterableTestUser([])
            ]);
            $user->setRelation('children', $children);

            $validator->assert($user);
        }, [
            'string@children.0.name',
            'required@children.1.name',
        ]);
    }
}

class IterableTestUser extends Model
{
    protected $fillable = [
        'name',
    ];
}
