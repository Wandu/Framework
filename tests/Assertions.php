<?php
namespace Wandu;

use Closure;
use Exception;
use PHPUnit\Framework\Assert;
use ReflectionClass;
use Throwable;

trait Assertions
{
    public static function assertEqualsAndSameProperty($expected, $actual)
    {
        if (!is_object($actual) && !is_array($actual)) {
            static::assertSame($expected, $actual);
            return;
        }
        static::assertEquals($expected, $actual);
        if (is_array($expected)) {
            foreach ($expected as $key => $_) {
                static::assertEqualsAndSameProperty($expected[$key], $actual[$key]);
            }
        } else {
            static::assertSame(get_class($expected), get_class($actual));

            $reflClass = new ReflectionClass(get_class($expected));
            $reflProps = $reflClass->getProperties();

            foreach ($reflProps as $reflProp) {
                $reflProp->setAccessible(true);
                static::assertEqualsAndSameProperty($reflProp->getValue($expected), $reflProp->getValue($actual));
            }
        }
    }
    
    public static function assertOutputBufferEquals($expected, Closure $closure, $message = '')
    {
        $depth = ob_get_level();
        ob_start();
        
        $result = $closure->__invoke();

        $contents = '';
        while (ob_get_level() - $depth > 0) {
            $contents .= ob_get_contents();
            ob_end_clean();
        }
        Assert::assertEquals($expected, $contents, $message);
        return $result;
    }
    
    public static function assertException($expected, Closure $closure, $message = '')
    {
        try {
            $closure();
        } catch (Exception $e) {
            Assert::assertEquals($expected, $e, $message);
            return;
        } catch (Throwable $e) {
            Assert::assertEquals($expected, $e, $message);
            return;
        }
        Assert::fail($message);
    }

    static public function catchException(Closure $handler)
    {
        try {
            $handler();
        } catch (Throwable $e) {
            return $e;
        }
        return null;
    }
    
    public static function assertExceptionInstanceOf($expected, Closure $closure, $message = '')
    {
        try {
            $closure();
        } catch (Exception $e) {
            Assert::assertInstanceOf($expected, $e, $message);
            return;
        } catch (Throwable $e) {
            Assert::assertInstanceOf($expected, $e, $message);
            return;
        }
        Assert::fail($message);
    }

    public static function assertExceptionMessageEquals($expected, Closure $closure, $message = '')
    {
        try {
            $closure();
        } catch (Exception $e) {
            Assert::assertEquals($expected, $e->getMessage(), $message);
            return;
        } catch (Throwable $e) {
            Assert::assertEquals($expected, $e->getMessage(), $message);
            return;
        }
        Assert::fail($message);
    }
}
