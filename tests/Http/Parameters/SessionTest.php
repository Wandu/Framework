<?php
namespace Wandu\Http\Parameters;

use InvalidArgumentException;
use Mockery;
use SessionHandlerInterface;
use Wandu\Http\Contracts\CookieJarInterface;

class SessionTest extends ParameterTest
{
    use TestGetAndSet;
    
    /** @var \Wandu\Http\Contracts\SessionInterface */
    protected $param1;

    /** @var \Wandu\Http\Contracts\SessionInterface */
    protected $param2;

    /** @var \Wandu\Http\Contracts\SessionInterface */
    protected $param3;

    public function setUp()
    {
        $cookieJar = Mockery::mock(CookieJarInterface::class);
        $cookieJar->shouldReceive('has')->with('WdSessId')->andReturn(true);
        $cookieJar->shouldReceive('get')->with('WdSessId')->andReturn(sha1('something'));

        $this->param1 = new Session($cookieJar, $this->createHandler($this->param1Attributes));
        $this->param2 = new Session($cookieJar, $this->createHandler($this->param2Attributes));
        $this->param3 = new Session($cookieJar, $this->createHandler($this->param3Attributes), null, new Parameter($this->param3FallbackAttributes));
    }
    
    public function testInvalidName()
    {
        try {
            $this->param1->set(30, 30);
            static::fail();
        } catch (InvalidArgumentException $e) {
            static::assertEquals('The session name must be string; "30"', $e->getMessage());
        }
        try {
            $this->param2->set('', 30);
            static::fail();
        } catch (InvalidArgumentException $e) {
            static::assertEquals('The session name cannot be empty.', $e->getMessage());
        }
    }

    /**
     * @issue #4 add session flash method
     * @ref https://github.com/Wandu/Http/issues/4
     */
    public function testFlash()
    {
        static::assertNull($this->param1->get('flash'));

        $this->param1->flash('flash', 'flash!!!');

        static::assertEquals('flash!!!', $this->param1->get('flash'));
        static::assertNull($this->param1->get('flash'));
    }

    public function testHasWithFlash()
    {
        static::assertNull($this->param1->get('flash'));

        $this->param1->flash('flash', 'flash!!!');

        static::assertTrue($this->param1->has('flash'));
        static::assertTrue($this->param1->has('flash'));
        static::assertTrue($this->param1->has('flash'));

        static::assertEquals('flash!!!', $this->param1->get('flash'));

        static::assertFalse($this->param1->has('flash'));
        static::assertFalse($this->param1->has('flash'));
        static::assertFalse($this->param1->has('flash'));

        static::assertNull($this->param1->get('flash'));
    }

    public function testToArrayWithFlash()
    {
        $this->param1->flash('flash', 'flash!!!');

        static::assertEquals([
            'string' => 'string!',
            'number' => '10',
            'array' => [
                'null' => null,
                'empty' => '',
                'false' => false,
            ],
            'array_of_array' => [
                [
                    'string' => 'string!',
                    'number' => '10',
                ],
                [
                    'string' => 'string!!',
                    'number' => '11',
                ],
                [
                    'string' => 'string!!!',
                    'number' => '12',
                ],
            ],
            'flash' => 'flash!!!',
        ], $this->param1->toArray());

        static::assertEquals([
            'string' => 'string!',
            'number' => '10',
            'array' => [
                'null' => null,
                'empty' => '',
                'false' => false,
            ],
            'array_of_array' => [
                [
                    'string' => 'string!',
                    'number' => '10',
                ],
                [
                    'string' => 'string!!',
                    'number' => '11',
                ],
                [
                    'string' => 'string!!!',
                    'number' => '12',
                ],
            ],
        ], $this->param1->toArray());
    }

    /**
     * @param array $attributes
     * @return \SessionHandlerInterface
     */
    private function createHandler(array $attributes = [])
    {
        $cookieJar = Mockery::mock(SessionHandlerInterface::class);
        $cookieJar->shouldReceive('read')->with(sha1('something'))->andReturn(serialize($attributes));
        return $cookieJar;
    }
}
