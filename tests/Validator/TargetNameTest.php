<?php
namespace Wandu\Validator;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Wandu\Assertions;

class TargetNameTest extends TestCase
{
    use Assertions;
    
    public function testError()
    {
        $exception = static::catchException(function () {
            TargetName::parse(',foobar[][]?[]?');
        });

        static::assertInstanceOf(InvalidArgumentException::class, $exception);
        static::assertEquals("Invalid target name. Did you mean this? 'foobar[][]?'.", $exception->getMessage());
    }

    public function provideTargetNames()
    {
        return [
            ['name', 'name', false, []],
            ['name?', 'name', true, []],
            ['name[]', 'name', false, [null]],
            ['name[]?', 'name', true, [null]],
            ['name[][][]', 'name', false, [null, null, null]],
            ['name[][][]?', 'name', true, [null, null, null]],
            ['name[10][20][30]', 'name', false, [10, 20, 30]],
            ['name[10][20][30]?', 'name', true, [10, 20, 30]],
        ];
    }
    
    /**
     * @dataProvider provideTargetNames
     * @param string $targetName
     * @param string $expectedName
     * @param bool $expectedOptional
     * @param array $expectedIterator
     */
    public function testNullable($targetName, $expectedName, $expectedOptional, $expectedIterator)
    {
        $name = TargetName::parse($targetName);

        static::assertSame($expectedName, $name->getName());
        static::assertSame($expectedOptional, $name->isOptional());
        static::assertSame($expectedIterator, $name->getIterator());
    }
}
