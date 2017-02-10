<?php
namespace Wandu\DI\Methods;

use PHPUnit_Framework_TestCase;
use stdClass;
use Wandu\DI\Container;
use Wandu\DI\Exception\CannotResolveException;

class CallTest extends PHPUnit_Framework_TestCase
{
    public function testCall()
    {
        $container = new Container();

        // create fail..
        try {
            $container->call(__NAMESPACE__ . '\\callTestFunctionHasTypeParam');
            static::fail();
        } catch (CannotResolveException $e) {
            static::assertEquals(null, $e->getClass());
            static::assertEquals('param', $e->getParameter());
        }

        $container->bind(CallTestDependencyInterface::class, CallTestDependency::class);

        // create success
        $result = $container->call(__NAMESPACE__ . '\\callTestFunctionHasTypeParam');
        static::assertInstanceOf(CallTestDependencyInterface::class, $result);
        static::assertInstanceOf(CallTestDependency::class, $result);
    }

    public function testCallSingleParamClassWithArguments()
    {
        $container = new Container();

        try {
            $container->call(__NAMESPACE__ . '\\callTestFunctionHasParam');
            static::fail();
        } catch (CannotResolveException $e) {
            static::assertEquals(null, $e->getClass());
            static::assertEquals('param', $e->getParameter());
        }

        // single param class
        $result = $container->call(__NAMESPACE__ . '\\callTestFunctionHasParam', [['username' => 'wan2land']]);
        static::assertSame(['username' => 'wan2land'], $result);

        $result = $container->call(__NAMESPACE__ . '\\callTestFunctionHasParam', ['param' => ['username' => 'wan3land']]);
        static::assertSame(['username' => 'wan3land'], $result);
    }

    public function testCallMultiParamsClassWithArguments()
    {
        $container = new Container();

        try {
            $container->call(__NAMESPACE__ . '\\callTestFunctionHasMultiParam');
            static::fail();
        } catch (CannotResolveException $e) {
            static::assertEquals(null, $e->getClass());
            static::assertEquals('param1', $e->getParameter());
        }

        $container->bind(CallTestDependencyInterface::class, CallTestDependency::class);

        try {
            $container->call(__NAMESPACE__ . '\\callTestFunctionHasMultiParam');
            static::fail();
        } catch (CannotResolveException $e) {
            static::assertEquals(null, $e->getClass());
            static::assertEquals('param2', $e->getParameter());
        }

        // only sequential
        $result = $container->call(__NAMESPACE__ . '\\callTestFunctionHasMultiParam', [
            $param1 = new CallTestDependency(),
            $param2 = new stdClass,
        ]);
        static::assertSame($param1, $result[0]);
        static::assertSame($param2, $result[1]);
        static::assertSame('param3', $result[2]);
        static::assertSame('param4', $result[3]);

        // only assoc
        $result = $container->call(__NAMESPACE__ . '\\callTestFunctionHasMultiParam', [
            'param2' => $param2 = new stdClass,
            'param4' => $param4 = new stdClass,
        ]);
        static::assertInstanceOf(CallTestDependencyInterface::class, $result[0]);
        static::assertSame($param2, $result[1]);
        static::assertSame('param3', $result[2]);
        static::assertSame($param4, $result[3]);

        // complex
        $result = $container->call(__NAMESPACE__ . '\\callTestFunctionHasMultiParam', [
            $param1 = new CallTestDependency(),
            $param2 = new stdClass,
            'param4' => $param4 = new stdClass,
            'param3' => $param3 = new stdClass,
        ]);
        static::assertSame($param1, $result[0]);
        static::assertSame($param2, $result[1]);
        static::assertSame($param3, $result[2]);
        static::assertSame($param4, $result[3]);
    }
    
    public function testCallCallable()
    {
        $container = new Container();

        $closure = function () {
            return ['$CLOSURE', func_get_args()];
        };

        // closure
        $result = $container->call($closure, ['param1', 'param2']);
        static::assertEquals(['$CLOSURE', ['param1', 'param2']], $result);

        // function
        $result = $container->call(__NAMESPACE__ . '\\callTestFunction', ['param2', 'param3']);
        static::assertEquals(['function', ['param2', 'param3']], $result);

        // static method
        $result = $container->call(CallTestInvokers::class . '::staticMethod', ['param3', 'param4']); 
        static::assertEquals(['staticMethod', ['param3', 'param4']], $result);

        // array of static
        $result = $container->call([CallTestInvokers::class, 'staticMethod'], ['param4', 'param5']);
        static::assertEquals(['staticMethod', ['param4', 'param5']], $result);

        // array of method
        $result = $container->call([new CallTestInvokers, 'instanceMethod'], ['param5', 'param6']);
        static::assertEquals(['instanceMethod', ['param5', 'param6']], $result);

        // invoker
        $result = $container->call(new CallTestInvokers(), ['param6', 'param7']);
        static::assertEquals(['__invoke', ['param6', 'param7']], $result);

        // __call
        $result = $container->call([new CallTestInvokers(), 'callViaCallMagicMethod'], ['param7', 'param8']); 
        static::assertEquals(['__call', 'callViaCallMagicMethod', ['param7', 'param8']], $result);

        // __staticCall
        $result = $container->call([CallTestInvokers::class, 'callViaStaticCallMagicMethod'], ['param8', 'param9']);
        static::assertEquals(['__callStatic', 'callViaStaticCallMagicMethod', ['param8', 'param9']], $result);
    }


    public function testCallWithOnlyAlias()
    {
        $container = new Container();
        $container->alias(CallTestCallWithOnlyAliasInterface::class, CallTestCallWithOnlyAlias::class);

        try {
            $container->call(function (CallTestCallWithOnlyAliasInterface $depend) {
                return $depend;
            });
            static::fail();
        } catch (CannotResolveException $e) {
        }

        $expected = new CallTestCallWithOnlyAlias(1111);
        
        $actual = $container->with([
            CallTestCallWithOnlyAlias::class => $expected
        ])->call(function (CallTestCallWithOnlyAliasInterface $depend) {
            return $depend;
        });
        static::assertSame($expected, $actual);
    }
}

interface CallTestDependencyInterface {}
class CallTestDependency implements CallTestDependencyInterface {}

interface CallTestCallWithOnlyAliasInterface {}
class CallTestCallWithOnlyAlias implements CallTestCallWithOnlyAliasInterface {
    public function __construct($param) {}
}

function callTestFunctionHasTypeParam(CallTestDependencyInterface $param) { return $param; }
function callTestFunctionHasParam($param) { return $param; }
function callTestFunctionHasMultiParam(CallTestDependencyInterface $param1, $param2, $param3 = 'param3', $param4 = 'param4')
{
    return [$param1, $param2, $param3, $param4];
}
function callTestFunction()
{
    return ['function', func_get_args()];
}
class CallTestInvokers
{
    /**
     * @return string
     */
    public static function staticMethod()
    {
        return ['staticMethod', func_get_args()];
    }

    /**
     * @return string
     */
    public function instanceMethod()
    {
        return ['instanceMethod', func_get_args()];
    }

    /**
     * @return string
     */
    public function __invoke()
    {
        return ['__invoke', func_get_args()];
    }

    /**
     * @param $name
     * @param $arguments
     * @return array
     */
    public function __call($name, $arguments)
    {
        return ['__call', $name, $arguments];
    }

    /**
     * @param $name
     * @param $arguments
     * @return array
     */
    public static function __callStatic($name, $arguments)
    {
        return ['__callStatic', $name, $arguments];
    }
}
