<?php
namespace Wandu\Restifier;

use PHPUnit\Framework\TestCase;
use Wandu\Assertions;
use Wandu\Restifier\Exception\NotFoundTransformerException;
use Wandu\Restifier\Sample\SampleAuthInterface;
use Wandu\Restifier\Sample\SampleCustomer;
use Wandu\Restifier\Sample\SampleCustomerTransformer;
use Wandu\Restifier\Sample\SampleUser;
use Wandu\Restifier\Sample\SampleUserTransformer;

class RestifierTest extends TestCase
{
    use Assertions;
    
    public function testSimple()
    {
        $restifier = new Restifier();
        $restifier->addTransformer(SampleUser::class, function (SampleUser $user) {
            return [
                'username' => $user->username,
            ];
        });

        $user = new SampleUser([
            'username' => 'wan2land',
        ]);
        
        static::assertEquals([
            "username" => "wan2land",
        ], $restifier->restify($user));
    }

    public function testNull()
    {
        $restifier = new Restifier();

        static::assertNull($restifier->restify(null));
    }

    public function testTransformerDirect()
    {
        $restifier = new Restifier();

        $user = new SampleUser([
            'username' => 'wan2land',
        ]);

        static::assertEquals([
            "username" => "wan2land",
        ], $restifier->restify($user, [], function (SampleUser $user) {
            return [
                'username' => $user->username,
            ];
        }));
    }

    public function testSimpleFail()
    {
        $restifier = new Restifier();

        $user = new SampleUser([
            'username' => 'wan2land',
        ]);

        $exception = static::catchException(function () use ($restifier, $user) {
            $restifier->restify($user);
        });

        static::assertInstanceOf(NotFoundTransformerException::class, $exception);
        static::assertEquals('cannot find the transformer named Wandu\Restifier\Sample\SampleUser.', $exception->getMessage());

        $exception = static::catchException(function () use ($restifier) {
            $restifier->restify([]);
        });

        static::assertInstanceOf(NotFoundTransformerException::class, $exception);
        static::assertEquals('resource is not an object.', $exception->getMessage());
    }

    public function testSimpleIterator()
    {
        $restifier = new Restifier();
        $restifier->addTransformer(SampleUser::class, function (SampleUser $user) {
            return [
                'username' => $user->username,
            ];
        });

        $user1 = new SampleUser([
            'username' => 'wan2land',
        ]);
        $user2 = new SampleUser([
            'username' => 'wan3land',
        ]);
        
        // seq array
        static::assertEquals([
            ["username" => "wan2land", ],
            ["username" => "wan3land", ],
        ], $restifier->restifyMany([$user1, $user2]));

        // assoc array
        static::assertEquals([
            'user1' => ["username" => "wan2land", ],
            'user2' => ["username" => "wan3land", ],
        ], $restifier->restifyMany(['user1' => $user1, 'user2' => $user2]));
    }

    public function testSimpleCascading()
    {
        $restifier = new Restifier();
        $restifier->addTransformer(SampleUser::class, function (SampleUser $user, Restifier $restifier) {
            return [
                'username' => $user->username,
                'customer' => $restifier->restify($user->customer),
            ];
        });
        $restifier->addTransformer(SampleCustomer::class, function (SampleCustomer $customer) {
            return [
                'address' => $customer->address,
            ];
        });

        $user = new SampleUser([
            'username' => 'wan2land',
            'customer' => new SampleCustomer([
                'address' => 'seoul blabla',
                'paymentmethods' => [], // critical data
            ]),
        ]);

        static::assertEquals([
            "username" => "wan2land",
            'customer' => [
                'address' => 'seoul blabla',
            ],
        ], $restifier->restify($user));
    }

    public function testTransformer()
    {
        // section:testTransformer
        $restifier = new Restifier();
        $restifier->addTransformer(SampleUser::class, new SampleUserTransformer());
        $restifier->addTransformer(SampleCustomer::class, new SampleCustomerTransformer());

        $user = new SampleUser([
            'username' => 'wan2land',
            'customer' => new SampleCustomer([
                'address' => 'seoul blabla',
                'paymentmethods' => [], // critical data
            ]),
        ]);

        static::assertEquals([
            "username" => "wan2land",
            'customer' => [
                'address' => 'seoul blabla',
            ],
        ], $restifier->restify($user));
        // endsection
    }
    
    public function testTransformerWithIncludes()
    {
        $restifier = new Restifier();
        $restifier->addTransformer(SampleUser::class, new SampleUserTransformer());
        $restifier->addTransformer(SampleCustomer::class, new SampleCustomerTransformer());

        $user = new SampleUser([
            'username' => 'wan2land',
            'profile' => [
                'source' => '/temp/wan2land/profile.png',
            ],
            'customer' => new SampleCustomer([
                'address' => 'seoul blabla',
                'paymentmethods' => [], // critical data
            ]),
        ]);

        static::assertEquals([
            "username" => "wan2land",
            'profile' => [
                'source' => '/temp/wan2land/profile.png',
            ],
            'customer' => [
                'address' => 'seoul blabla',
            ],
        ], $restifier->restify($user, ['profile']));

        static::assertEquals([
            "username" => "wan2land",
            'customer' => [
                'address' => 'seoul blabla',
            ],
        ], $restifier->restify($user, ['profile' => false]));


        static::assertEquals([
            "username" => "wan2land",
            'profile' => [
                'source' => '/temp/wan2land/profile.png',
            ],
            'customer' => [
                'address' => 'seoul blabla',
            ],
        ], $restifier->restify($user, ['profile' => true]));

        static::assertEquals([
            "username" => "wan2land",
            'customer' => [
                'address' => 'seoul blabla',
            ],
        ], $restifier->restify($user, ['profile' => function (SampleUser $user) {
            return $user->username !== 'wan2land';
        }]));


        static::assertEquals([
            "username" => "wan2land",
            'profile' => [
                'source' => '/temp/wan2land/profile.png',
            ],
            'customer' => [
                'address' => 'seoul blabla',
            ],
        ], $restifier->restify($user, ['profile' => function (SampleUser $user) {
            return $user->username === 'wan2land';
        }]));
    }

    public function testTransformerWithCascadingIncludes()
    {
        $restifier = new Restifier();
        $restifier->addTransformer(SampleUser::class, new SampleUserTransformer());
        $restifier->addTransformer(SampleCustomer::class, new SampleCustomerTransformer());

        $user = new SampleUser([
            'username' => 'wan2land',
            'profile' => [
                'source' => '/temp/wan2land/profile.png',
            ],
            'customer' => new SampleCustomer([
                'address' => 'seoul blabla',
                'paymentmethods' => [], // critical data
            ]),
        ]);

        static::assertEquals([
            "username" => "wan2land",
            'profile' => [
                'source' => '/temp/wan2land/profile.png',
            ],
            'customer' => [
                'address' => 'seoul blabla',
            ],
        ], $restifier->restify($user, ['profile', 'customer']));

        static::assertEquals([
            "username" => "wan2land",
            'profile' => [
                'source' => '/temp/wan2land/profile.png',
            ],
            'customer' => [
                'address' => 'seoul blabla',
                'paymentmethods' => [],
            ],
        ], $restifier->restify($user, ['profile', 'customer.paymentmethods']));
    }

    public function testTransformDetectByInterface()
    {
        $restifier = new Restifier();
        $restifier->addTransformer(SampleAuthInterface::class, new SampleUserTransformer());
        $restifier->addTransformer(SampleCustomer::class, new SampleCustomerTransformer());

        $user = new SampleUser([
            'username' => 'wan2land',
            'customer' => new SampleCustomer([
                'address' => 'seoul blabla',
                'paymentmethods' => [], // critical data
            ]),
        ]);

        static::assertEquals([
            "username" => "wan2land",
            'customer' => [
                'address' => 'seoul blabla',
            ],
        ], $restifier->restify($user));
    }
}
