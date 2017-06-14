<?php
namespace Wandu\Q\Adapter;

use Pheanstalk\Exception\ConnectionException;
use Pheanstalk\Pheanstalk;

class BeanstalkdAdapterTest extends TestCase
{
    public function setUp()
    {
        try {
            $beans = new Pheanstalk('127.0.0.1');
            $this->queue = new BeanstalkdAdapter($beans, uniqid('phpunit_'));
        } catch (ConnectionException $e) {
            static::markTestSkipped("cannot connect to beanstalkd");
        }
    }
}
