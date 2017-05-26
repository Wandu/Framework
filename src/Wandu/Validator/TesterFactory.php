<?php
namespace Wandu\Validator;

use Wandu\Validator\Contracts\TesterInterface;
use Wandu\Validator\Exception\TesterNotFoundException;
use Wandu\Validator\Testers\AlwaysFalseTester;
use Wandu\Validator\Testers\AlwaysTrueTester;
use Wandu\Validator\Testers\BooleanTester;
use Wandu\Validator\Testers\EmailTester;
use Wandu\Validator\Testers\FloatTester;
use Wandu\Validator\Testers\IntegerableTester;
use Wandu\Validator\Testers\IntegerTester;
use Wandu\Validator\Testers\LengthMaxTester;
use Wandu\Validator\Testers\LengthMinTester;
use Wandu\Validator\Testers\MaxTester;
use Wandu\Validator\Testers\MinTester;
use Wandu\Validator\Testers\NumericTester;
use Wandu\Validator\Testers\PrintableTester;
use Wandu\Validator\Testers\RegExpTester;
use Wandu\Validator\Testers\StringableTester;
use Wandu\Validator\Testers\StringTester;

class TesterFactory
{
    /** @var \Wandu\Validator\TesterFactory */
    public static $instance;
    
    /** @var string[] */
    protected $testers;

    /** @var \Wandu\Validator\Contracts\TesterInterface[] */
    protected $caches = [];

    public function __construct(array $testers = [])
    {
        $this->testers = $testers + [
                'always_false' => AlwaysFalseTester::class,
                'always_true' => AlwaysTrueTester::class,
                'bool' => BooleanTester::class,
                'boolean' => BooleanTester::class,
                'email' => EmailTester::class,
                'float' => FloatTester::class,
                'floatable' => NumericTester::class,
                'integerable' => IntegerableTester::class,
                'int' => IntegerTester::class,
                'integer' => IntegerTester::class,
                'length_max' => LengthMaxTester::class,
                'length_min' => LengthMinTester::class,
                'max' => MaxTester::class,
                'min' => MinTester::class,
                'numeric' => NumericTester::class,
                'printable' => PrintableTester::class,
                'regexp' => RegExpTester::class,
                'stringable' => StringableTester::class,
                'string' => StringTester::class,
            ];
    }

    /**
     * @return \Wandu\Validator\TesterFactory
     */
    public function setAsGlobal()
    {
        $instance = static::$instance;
        static::$instance = $this;
        return $instance;
    }
    
    /**
     * @param string $tester
     * @param array ...$arguments
     * @return \Wandu\Validator\Contracts\TesterInterface
     */
    public function from(string $tester, ...$arguments): TesterInterface
    {
        if (count($arguments)) {
            $className = $this->getClassName($tester);
            return new $className(...$arguments);
        }
        list($name, $arguments) = $this->getMethodAndParams($tester);
        if (!array_key_exists($tester, $this->caches)) {
            $className = $this->getClassName($name);
            $this->caches[$tester] = new $className(...$arguments);
        }
        return $this->caches[$tester];
    }

    /**
     * @param string $pattern
     * @return array
     */
    protected function getMethodAndParams($pattern)
    {
        $pattern = trim($pattern);
        if (false === $pivot = strpos($pattern, ':')) {
            return [$pattern, []]; // "simple"
        }
        $method = substr($pattern, 0, $pivot);
        preg_match_all('/\/[^\/]*\/|[^,]+/', substr($pattern, $pivot + 1), $matches);
        $params = array_reduce(
            $matches[0],
            function ($carry, $value) {
                $value = trim($value);
                if ($value) {
                    $carry[] = $value;
                }
                return $carry;
            },
            []
        );
        return [$method, $params];
    }

    /**
     * @param string $name
     * @return string
     */
    protected function getClassName($name)
    {
        if (isset($this->testers[$name])) {
            $className = $this->testers[$name];
            if (class_exists($className)) {
                return $className;
            }
        }
        throw new TesterNotFoundException($name);
    }
}
