<?php
namespace Wandu\Http\Parameters;

use InvalidArgumentException;
use Mockery;
use SessionHandlerInterface;
use Wandu\Http\Contracts\CookieJarInterface;

class SessionTest extends ParameterTest
{
    /** @var \Wandu\Http\Session\Session */
    private $session;

    /** @var \Wandu\Http\Contracts\ParameterInterface */
    protected $param1;

    /** @var \Wandu\Http\Contracts\ParameterInterface */
    protected $param2;

    /** @var \Wandu\Http\Contracts\ParameterInterface */
    protected $param3;

    public function setUp()
    {
        $cookieJar = Mockery::mock(CookieJarInterface::class);
        $cookieJar->shouldReceive('has')->with('WdSessId')->andReturn(true);
        $cookieJar->shouldReceive('get')->with('WdSessId')->andReturn(sha1('something'));

        $this->param1 = new Session($cookieJar, $this->createHandler([
            'string' => 'string!',
            'number' => '10',
        ]));
        $this->param2 = new Session($cookieJar, $this->createHandler([
            'null' => null,
            'empty' => '',
            'false' => false,
        ]));

        $this->param3 = new Session($cookieJar, $this->createHandler([
            'string1' => 'string 1!',
            'string2' => 'string 2!',
        ]), null, new Parameter([
            'string1' => 'string 1 fallback!',
            'fallback' => 'fallback!',
        ]));

        $this->session = new Session($cookieJar, $this->createHandler([
            'id' => 37,
            'username' => 'wan2land'
        ]));
    }
    
    protected function createHandler(array $attributes = [])
    {
        $cookieJar = Mockery::mock(SessionHandlerInterface::class);
        $cookieJar->shouldReceive('read')->with(sha1('something'))->andReturn(serialize($attributes));
        return $cookieJar;
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testInvalidName()
    {
        try {
            $this->session->set(30, 30);
            static::fail();
        } catch (InvalidArgumentException $e) {
            static::assertEquals('The session name must be string; "30"', $e->getMessage());
        }
        try {
            $this->session->set('', 30);
            static::fail();
        } catch (InvalidArgumentException $e) {
            static::assertEquals('The session name cannot be empty.', $e->getMessage());
        }
    }

    public function testGet()
    {
        static::assertEquals(37, $this->session->get('id'));
        static::assertNull($this->session->get('not_exists_key'));
    }

    public function testSetAndRemove()
    {
        // first
        static::assertEquals(37, $this->session->get('id'));
        static::assertTrue($this->session->has('id'));

        static::assertNull($this->session->get('_new'));
        static::assertFalse($this->session->has('_new'));

        // set
        $this->session->set('_new', "new value!");
        static::assertEquals('new value!', $this->session->get('_new'));
        static::assertTrue($this->session->has('_new'));

        $this->session->remove('_new');
        static::assertNull($this->session->get('_new'));
        static::assertFalse($this->session->has('_new'));

        $this->session->remove('id');
        static::assertNull($this->session->get('id'));
        static::assertFalse($this->session->has('id'));
    }

    public function testArrayAccess()
    {
        static::assertSame($this->session->get('id'), $this->session['id']);
        static::assertSame($this->session->get('unknown'), $this->session['unknown']);

        static::assertSame($this->session->has('id'), isset($this->session['id']));
        static::assertSame($this->session->has('unknown'), isset($this->session['unknown']));

        static::assertFalse($this->session->has('added'));
        $this->session['added'] = 'added!';
        static::assertTrue($this->session->has('added'));

        static::assertTrue($this->session->has('id'));
        unset($this->session['id']);
        static::assertFalse($this->session->has('id'));
    }

    /**
     * @issue #4 add session flash method
     * @ref https://github.com/Wandu/Http/issues/4
     */
    public function testFlash()
    {
        $this->session->flash('flash', 'hello world!');

        static::assertEquals('hello world!', $this->session->get('flash'));
        static::assertNull($this->session->get('flash'));
    }

    public function testHasWithFlash()
    {
        $this->session->flash('flash', 'hello world!');

        static::assertTrue($this->session->has('flash'));
        static::assertTrue($this->session->has('flash'));
        static::assertTrue($this->session->has('flash'));

        static::assertEquals('hello world!', $this->session->get('flash'));

        static::assertFalse($this->session->has('flash'));
        static::assertFalse($this->session->has('flash'));
        static::assertFalse($this->session->has('flash'));

        static::assertNull($this->session->get('flash'));
    }

    public function testToArrayWithFlash()
    {
        $this->session->flash('flash', 'hello world!');

        static::assertEquals([
            'flash' => 'hello world!',
            'id' => 37,
            'username' => 'wan2land'
        ], $this->session->toArray());

        static::assertEquals([
            'id' => 37,
            'username' => 'wan2land'
        ], $this->session->toArray());
    }
}
