<?php
namespace Wandu\Tempy;

use Mockery;
use PHPUnit_Framework_TestCase;

class ParserTest extends PHPUnit_Framework_TestCase
{
    /** @var \Wandu\Tempy\Parser */
    protected $parser;

    public function setUp()
    {
        $this->parser = new Parser();
    }

    public function provider()
    {
        return [
            ['variable-as-variable.php'],
            ['variable-with-default.php'],
            ['condition.php'],
        ];
    }

    /**
     * @dataProvider provider
     * @param string $fileName
     */
    public function testParse($fileName)
    {
        $input = trim(file_get_contents(__DIR__ . "/input/{$fileName}"));
        $output = trim(file_get_contents(__DIR__ . "/output/{$fileName}"));

        $this->assertEquals($output, $this->parser->parse($input));
    }
}
