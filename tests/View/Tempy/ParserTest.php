<?php
namespace Wandu\View\Tempy;

use Mockery;
use PHPUnit_Framework_TestCase;

class ParserTest extends PHPUnit_Framework_TestCase
{
    /** @var \Wandu\View\Tempy\Parser */
    protected $parser;

    public function setUp()
    {
        $this->parser = new Parser();
    }

//    public function provider()
//    {
//        return [
//            ['variable-as-variable'],
//            ['variable-with-default'],
//            ['function-variable-with-default'],
//        ];
//    }

    public function testParse()
    {
//        $input = trim(file_get_contents(__DIR__ . "/input/{$fileName}.tempy"));
//        $output = trim(file_get_contents(__DIR__ . "/output/{$fileName}.php"));
//
//        $this->assertEquals($output, $this->parser->parse($input));
    }
}
