<?php
namespace Wandu\View;

use Wandu\View\Phiew\Configuration;
use Wandu\View\Phiew\FileResolver;

/**
 * "Phiew" is an upward compatible class of "PhpView".
 */
class PhiewTest extends PhpViewTest
{
    public function setUp()
    {
        $config = new Configuration([
            'path' => [
                __DIR__ . '/views',
            ]
        ]);
        $this->view = new Phiew(new FileResolver($config));
    }
    
    public function testLayout()
    {
        $expected = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>title from home.php</title>
    <style data-name="1"></style>
    <style data-name="2"></style>
</head>
<body>
<main>title = title from phpunit, hello wandu</main>
<footer>1.0.0</footer>
<script data-name="1"></script>
<script data-name="2"></script>
</body>
</html>

HTML;
        $actual = $this->view->render('home.php', [
            'title' => 'title from phpunit',
            'version' => '1.0.0',
            'who' => 'stranger',
        ]);
        
        static::assertEquals($expected, $actual);
    }
}
