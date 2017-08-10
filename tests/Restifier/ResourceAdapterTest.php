<?php
namespace Wandu\Restifier;

use PHPUnit\Framework\TestCase;
use Wandu\Restifier\Contracts\TransformResource;
use Wandu\Restifier\Resources\ResourceAdapter;

class ResourceAdapterTest extends TestCase
{
    /** @var \Wandu\Restifier\Restifier */
    protected $restifier;
    
    /** @var \Wandu\Restifier\Resources\ResourceAdapter */
    protected $singleResource;
    
    public function setUp()
    {
        $this->restifier = new Restifier();
        $this->singleResource = new ResourceAdapter(
            new TransformResourceTestUser("wan2land", new TransformResourceTestGroup("admin")),
            function (TransformResourceTestUser $user) {
                return [
                    'username' => $user->getUsername(),
                ];
            },
            function (TransformResourceTestUser $user, string $name) {
                switch ($name) {
                    case "group": return [
                        "group" => [
                            'name' => $user->getGroup()->getName(),
                        ],
                    ];
                }
            }
        );
    }
    
    public function testSingleItem()
    {
        static::assertEquals([
            "username" => "wan2land",
        ], $this->restifier->transform($this->singleResource));
    }

    public function testSingleItemWithIncludes()
    {
        static::assertEquals([
            "username" => "wan2land",
            "group" => [
                "name" => "admin",
            ],
        ], $this->restifier->transform($this->singleResource, ['group']));

        static::assertEquals([
            "username" => "wan2land",
        ], $this->restifier->transform($this->singleResource, ['group' => false]));

        static::assertEquals([
            "username" => "wan2land",
            "group" => [
                "name" => "admin",
            ],
        ], $this->restifier->transform($this->singleResource, ['group' => true]));

        static::assertEquals([
            "username" => "wan2land",
        ], $this->restifier->transform($this->singleResource, ['group' => function () { return false; }]));

        static::assertEquals([
            "username" => "wan2land",
            "group" => [
                "name" => "admin",
            ],
        ], $this->restifier->transform($this->singleResource, ['group' => function () { return true; }]));

        static::assertEquals([
            "username" => "wan2land",
        ], $this->restifier->transform($this->singleResource, ['group' => function (TransformResource $origin) {
            return $origin->transform()['username'] !== 'wan2land';
        }]));

        static::assertEquals([
            "username" => "wan2land",
            "group" => [
                "name" => "admin",
            ],
        ], $this->restifier->transform($this->singleResource, ['group' => function (TransformResource $origin) {
            return $origin->transform()['username'] === 'wan2land';
        }]));
    }

}

class TransformResourceTestGroup
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

    /**
     * @return int
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}

class TransformResourceTestUser
{
    protected $group;
    protected $username;
    protected $createdAt;
    protected $updatedAt;

    public function __construct($username, TransformResourceTestGroup $group)
    {
        $this->group = $group;
        $this->username = $username;
        $this->createdAt = $this->createdAt = time();
    }

    /**
     * @return \Wandu\Restifier\TransformResourceTestGroup
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

    /**
     * @return int
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
