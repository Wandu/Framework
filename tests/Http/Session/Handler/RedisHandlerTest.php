<?php
namespace Wandu\Http\Session\Handler;

use Mockery;
use Predis\Client;
use Predis\Connection\ConnectionException;
use Wandu\Http\Session\HandlerTestCase;

class RedisHandlerTest extends HandlerTestCase
{
    /** @var \Predis\Client */
    protected $client;
    
    public function setUp()
    {
        $this->client = new Client();
        try {
            $this->client->ping();
        } catch (ConnectionException $e) {
            $this->markTestSkipped('cannot redis connection!');
        }
        $this->adapter = new RedisHandler($this->client);
    }

    public function tearDown()
    {
        $this->client->flushall();
    }

    /**
     * {@inheritdoc}
     */
    public function testGarbageCollection()
    {
        // test 1 second.
        $this->adapter = new RedisHandler($this->client, 1);
        parent::testGarbageCollection();
    }

    protected function getCountOfSessionFiles()
    {
        return count($this->client->keys('wandu*'));
    }
}
