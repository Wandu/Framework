<?php
namespace Wandu\Validator\Rules;

use ArrayObject;
use Illuminate\Support\Collection;
use Wandu\Validator\ValidatorTestCase;
use Illuminate\Database\Eloquent\Model;
use function Wandu\Validator\validator;

class CollectionValidatorTest extends ValidatorTestCase
{
    public function testNullCollectionValidate()
    {
        static::assertTrue(validator()->collection()->validate(null));
        static::assertTrue(validator()->collection()->validate([]));
        static::assertTrue(validator()->collection()->validate(new ArrayObject([])));
        static::assertTrue(validator()->collection()->validate(new Collection([1, 2, 3]))); // also use Laravel's Collection.
        static::assertTrue(validator()->collection()->validate(['3', 0, 1, 2, 'something']));

        static::assertFalse(validator()->collection()->validate((object)[]));
        static::assertFalse(validator()->collection()->validate("30"));
        static::assertFalse(validator()->collection()->validate([
            'hello' => 'world',
        ]));
        static::assertFalse(validator()->collection()->validate(new Collection([
            'id' => 1,
        ]))); // but cannot use Laravel's Assoc Collection.
        static::assertFalse(validator()->collection()->validate(new CollectionTestUser([]))); // cannot use Laravel's Model

    }

    public function testValidate()
    {
        static::assertTrue(validator()->collection('integer')->validate([30, 40, 50, 60]));
        static::assertFalse(validator()->collection('integer')->validate([30, '40', 50, 60]));

        // ignore other key 
        static::assertTrue(validator()->collection([
            'age' => 'integer',
        ])->validate([
            ['age' => 30, 'other' => 'other...'],
        ]));

        static::assertFalse(validator()->collection([
            'age' => 'integer',
        ])->validate(['age' => 30, 'other' => 'other...']));
    }

    public function testAssertMethod()
    {
        $validator = validator()->collection(['name' => 'string', 'age' => 'integer',]);

        // valid
        $validator->assert([
            [
                'name' => 'wandu',
                'age' => 30,
            ],
        ]);

        static::assertInvalidValueException(function () use ($validator) {
            $validator->assert('string');
        }, [
            'collection',
        ]);

        $validator->assert([]);
        $validator->assert([
            [
                'age' => 30
            ],
        ]);
    }

    public function testAssertCollectionOfArray()
    {
        $validator = validator()->collection([
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
            'collection',
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
            'children' => validator()->collection([
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
            'children' => validator()->collection(validator()->arrayable([
                'name' => 'required|string',
            ])),
        ]);

        $user = new CollectionTestUser();
        $children = new Collection([
            new CollectionTestUser(['name' => 'alex']),
            new CollectionTestUser(['name' => 'lily'])
        ]);
        $user->setRelation('children', $children);
        
        $validator->assert($user);

        static::assertInvalidValueException(function () use ($validator) {
            $user = new CollectionTestUser();
            $children = new Collection([
                new CollectionTestUser(['name' => 3030]),
                new CollectionTestUser([])
            ]);
            $user->setRelation('children', $children);

            $validator->assert($user);
        }, [
            'string@children.0.name',
            'required@children.1.name',
        ]);
    }
}

class CollectionTestUser extends Model
{
    protected $fillable = [
        'name',
    ];
}
