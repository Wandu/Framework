<?php
namespace Wandu\Restifier;

use PHPUnit\Framework\TestCase;
use Wandu\Restifier\Contracts\TransformResource;

class RestifierTest extends TestCase
{
    /** @var \Wandu\Restifier\Restifier */
    protected $restifier;

    public function setUp()
    {
        $this->restifier = new Restifier();
    }
    
    public function testSingle()
    {
        static::assertEquals([
            "username" => "wan2land",
        ], $this->restifier->transform($this->createUser("wan2land", "admin")));
    }

    public function testSingleWithIncludes()
    {
        static::assertEquals([
            "username" => "wan2land",
            "group" => [
                "name" => "admin",
            ],
        ], $this->restifier->transform($this->createUser("wan2land", "admin"), ['group']));

        static::assertEquals([
            "username" => "wan2land",
        ], $this->restifier->transform($this->createUser("wan2land", "admin"), ['group' => false]));

        static::assertEquals([
            "username" => "wan2land",
            "group" => [
                "name" => "admin",
            ],
        ], $this->restifier->transform($this->createUser("wan2land", "admin"), ['group' => true]));

        static::assertEquals([
            "username" => "wan2land",
        ], $this->restifier->transform($this->createUser("wan2land", "admin"), ['group' => function () {
            return false;
        }]));

        static::assertEquals([
            "username" => "wan2land",
            "group" => [
                "name" => "admin",
            ],
        ], $this->restifier->transform($this->createUser("wan2land", "admin"), ['group' => function () {
            return true;
        }]));

        static::assertEquals([
            "username" => "wan2land",
        ], $this->restifier->transform($this->createUser("wan2land", "admin"), ['group' => function (RestifierTestUser $entity) {
            return $entity->getUsername() !== "wan2land";
        }]));

        static::assertEquals([
            "username" => "wan2land",
            "group" => [
                "name" => "admin",
            ],
        ], $this->restifier->transform($this->createUser("wan2land", "admin"), ['group' => function (RestifierTestUser $entity) {
            return $entity->getUsername() === "wan2land";
        }]));
    }

    public function provideResources()
    {
        return [
            [
                [
                    $this->createUser("wan2land", "admin"),
                    $this->createUser("foo", "normal"),
                    $this->createUser("bar", "normal"),
                ]
            ],
        ];
    }

    /**
     * @dataProvider provideResources
     */
    public function testCollection($resources)
    {
        static::assertEquals([
            ["username" => "wan2land",],
            ["username" => "foo",],
            ["username" => "bar",],
        ], $this->restifier->transform($resources));
    }

    /**
     * @dataProvider provideResources
     */
    public function testCollectionWithIncludes($resources)
    {
        static::assertEquals([
            ["username" => "wan2land", "group" => ["name" => "admin"],],
            ["username" => "foo", "group" => ["name" => "normal"],],
            ["username" => "bar", "group" => ["name" => "normal"],],
        ], $this->restifier->transform($resources, ['group']));

        static::assertEquals([
            ["username" => "wan2land",],
            ["username" => "foo",],
            ["username" => "bar",],
        ], $this->restifier->transform($resources, ['group' => false]));

        static::assertEquals([
            ["username" => "wan2land", "group" => ["name" => "admin"],],
            ["username" => "foo", "group" => ["name" => "normal"],],
            ["username" => "bar", "group" => ["name" => "normal"],],
        ], $this->restifier->transform($resources, ['group' => true]));

        static::assertEquals([
            ["username" => "wan2land",],
            ["username" => "foo",],
            ["username" => "bar",],
        ], $this->restifier->transform($resources, ['group' => function () { return false; }]));

        static::assertEquals([
            ["username" => "wan2land", "group" => ["name" => "admin"],],
            ["username" => "foo", "group" => ["name" => "normal"],],
            ["username" => "bar", "group" => ["name" => "normal"],],
        ], $this->restifier->transform($resources, ['group' => function () { return true; }]));

        static::assertEquals([
            ["username" => "wan2land", "group" => ["name" => "admin"],],
            ["username" => "foo",],
            ["username" => "bar",],
        ], $this->restifier->transform($resources, ['group' => function (RestifierTestUser $entity) {
            return $entity->getGroup()->getName() !== 'normal';
        }]));
    }

    protected function createUser($username, $groupName)
    {
        return new RestifierTestUser($username, new RestifierTestGroup($groupName));
    }
}

class RestifierTestGroup implements TransformResource
{
    protected $name;
    protected $createdAt;
    protected $updatedAt;

    public function __construct($name)
    {
        $this->name = $name;
        $this->createdAt = $this->createdAt = time();
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    public function transform()
    {
        return [
            'name' => $this->name,
        ];
    }

    public function includeAttribute(string $name)
    {
    }
}

class RestifierTestUser implements TransformResource
{
    protected $group;
    protected $username;
    protected $createdAt;
    protected $updatedAt;

    public function __construct($username, RestifierTestGroup $group)
    {
        $this->group = $group;
        $this->username = $username;
        $this->createdAt = $this->createdAt = time();
    }

    /**
     * @return \Wandu\Restifier\RestifierTestGroup
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }
    
    public function transform()
    {
        return [
            'username' => $this->username,
        ];
    }

    public function includeAttribute(string $name)
    {
        switch ($name) {
            case "group": return [
                "group" => $this->group,
            ];
        }
    }
}
