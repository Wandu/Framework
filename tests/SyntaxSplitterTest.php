<?php
namespace Wandu\Tempy;

use Mockery;
use PHPUnit_Framework_TestCase;
use Wandu\Tempy\Exception\SyntaxException;

class SyntaxSplitterTest extends PHPUnit_Framework_TestCase
{
    /** @var \Wandu\Tempy\SyntaxSplitter */
    protected $splitter;

    public function setUp()
    {
        $this->splitter = new SyntaxSplitter();
    }

    public function testSplitByBracket()
    {
        $this->splitter->analyze("{{ }} {{ }}");

        try {
            $this->splitter->analyze("{{ {{ }}");
            $this->fail();
        } catch (SyntaxException $e) {
        }

        try {
            $this->splitter->analyze("{{ }} }}");
            $this->fail();
        } catch (SyntaxException $e) {
        }

        try {
            $this->splitter->analyze("{{ }} {{");
            $this->fail();
        } catch (SyntaxException $e) {
        }
    }
}
