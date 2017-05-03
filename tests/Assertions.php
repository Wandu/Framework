<?php
namespace Wandu;

use Closure;
use PHPUnit_Framework_Assert;
use Throwable;
use Exception;

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
        PHPUnit_Framework_Assert::assertEquals($expected, $contents, $message);
    }
    
    public static function assertException($expected, Closure $closure, $message = '')
    {
        try {
            $closure();
        } catch (Exception $e) {
            PHPUnit_Framework_Assert::assertEquals($expected, $e, $message);
            return;
        } catch (Throwable $e) {
            PHPUnit_Framework_Assert::assertEquals($expected, $e, $message);
            return;
        }
        PHPUnit_Framework_Assert::fail($message);
    }

    public static function assertExceptionInstanceOf($expected, Closure $closure, $message = '')
    {
        try {
            $closure();
        } catch (Exception $e) {
            PHPUnit_Framework_Assert::assertInstanceOf($expected, $e, $message);
            return;
        } catch (Throwable $e) {
            PHPUnit_Framework_Assert::assertInstanceOf($expected, $e, $message);
            return;
        }
        PHPUnit_Framework_Assert::fail($message);
    }

    public static function assertExceptionMessageEquals($expected, Closure $closure, $message = '')
    {
        try {
            $closure();
        } catch (Exception $e) {
            PHPUnit_Framework_Assert::assertEquals($expected, $e->getMessage(), $message);
            return;
        } catch (Throwable $e) {
            PHPUnit_Framework_Assert::assertEquals($expected, $e->getMessage(), $message);
            return;
        }
        PHPUnit_Framework_Assert::fail($message);
    }
}
