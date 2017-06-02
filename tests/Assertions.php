<?php
namespace Wandu;

use Closure;
use Exception;
use PHPUnit\Framework\Assert;
use Throwable;

trait Assertions
{
    public static function assertOutputBufferEquals($expected, Closure $closure, $message = '')
    {
        $depth = ob_get_level();
        ob_start();
        
        $closure->__invoke();

        $contents = '';
        while (ob_get_level() - $depth > 0) {
            $contents .= ob_get_contents();
            ob_end_clean();
        }
        Assert::assertEquals($expected, $contents, $message);
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
