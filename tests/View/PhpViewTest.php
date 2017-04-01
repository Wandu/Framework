<?php
namespace Wandu\View;

use Exception;
use PHPUnit_Framework_TestCase;

class PhpViewTest extends PHPUnit_Framework_TestCase
{
    /** @var \Wandu\View\Contracts\RenderInterface */
    protected $view;
    
    public function setUp()
    {
        $this->view = new PhpView(__DIR__ . '/views');
    }
    
    public function testRenderFail()
    {
        try {
            $this->view->render('unknown.php');
            static::fail();
        } catch (FileNotFoundException $e) {
        }
        try {
            $this->view->render('exception-error.php');
            static::fail();
        } catch (Exception $e) {
            static::assertEquals('something wrong!', $e->getMessage());
        }
    }

    public function testRender()
    {
        static::assertEquals('hello stranger', $this->view->render('hello-stranger.php', ['who' => 'stranger']));
        static::assertEquals('hello wandu', $this->view->render('hello-stranger.php', ['who' => 'wandu']));
    }

    public function testWith()
    {
        $viewWithValues = $this->view->with(['who' => 'stranger']);

        static::assertNotEquals($viewWithValues, $this->view); // difference!
        static::assertEquals('hello stranger', $viewWithValues->render('hello-stranger.php'));
        static::assertEquals('hello wandu', $viewWithValues->render('hello-stranger.php', ['who' => 'wandu'])); // overwrite
    }
}
