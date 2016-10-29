<?php
namespace Wandu\View;

use Exception;
use PHPUnit_Framework_TestCase;

class PhpViewTest extends PHPUnit_Framework_TestCase
{
    public function testRenderFail()
    {
        $view = new PhpView(__DIR__ . '/views');
        
        try {
            $view->render('unknown.php');
            static::fail();
        } catch (FileNotFoundException $e) {
        }

        try {
            $view->render('exception-error.php');
            static::fail();
        } catch (Exception $e) {
            static::assertEquals('something wrong!', $e->getMessage());
        }
    }

    public function testRender()
    {
        $view = new PhpView(__DIR__ . '/views');
        static::assertEquals('hello stranger', $view->render('hello-stranger.php', ['who' => 'stranger']));

        $view = new PhpView(__DIR__ . '/views');
        static::assertEquals('hello wandu', $view->render('hello-stranger.php', ['who' => 'wandu']));
    }

    public function testWith()
    {
        $view = new PhpView(__DIR__ . '/views');
        $viewWithValues = $view->with(['who' => 'stranger']);

        static::assertNotEquals($viewWithValues, $view); // difference!
        static::assertEquals('hello stranger', $viewWithValues->render('hello-stranger.php'));
        static::assertEquals('hello wandu', $viewWithValues->render('hello-stranger.php', ['who' => 'wandu'])); // overwrite
    }
}
