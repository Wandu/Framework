<?php
namespace Wandu\Validator;

use Wandu\Validator\Contracts\Tester;
use Wandu\Validator\Exception\TesterNotFoundException;
use Wandu\Validator\Testers as Testers;

class TesterLoader
{
    /** @var string[] */
    protected $testers;

    /** @var \Wandu\Validator\Contracts\Tester[] */
    protected $caches = [];

    public function __construct(array $testers = [])
    {
        $this->testers = $testers + [
                'after' => Testers\AfterTester::class,
                'always_false' => Testers\AlwaysFalseTester::class,
                'always_true' => Testers\AlwaysTrueTester::class,
                'before' => Testers\BeforeTester::class,
                'between' => Testers\BetweenTester::class,
                'bool' => Testers\BooleanTester::class,
                'boolean' => Testers\BooleanTester::class,
                'email' => Testers\EmailTester::class,
                'equal_to' => Testers\EqualToTester::class,
                'eq' => Testers\EqualToTester::class,
                'float' => Testers\FloatTester::class,
                'floatable' => Testers\NumericTester::class,
                'greater_than' => Testers\GreaterThanTester::class,
                'greater_than_or_equal' => Testers\GreaterThanOrEqualTester::class,
                'gt' => Testers\GreaterThanTester::class,
                'gte' => Testers\GreaterThanOrEqualTester::class,
                'integerable' => Testers\IntegerableTester::class,
                'int' => Testers\IntegerTester::class,
                'integer' => Testers\IntegerTester::class,
                'length_between' => Testers\LengthBetweenTester::class,
                'length_max' => Testers\LengthMaxTester::class,
                'length_min' => Testers\LengthMinTester::class,
                'less_than' => Testers\LessThanTester::class,
                'less_than_or_equal' => Testers\LessThanOrEqualTester::class,
                'lt' => Testers\LessThanTester::class,
                'lte' => Testers\LessThanOrEqualTester::class,
                'max' => Testers\MaxTester::class,
                'min' => Testers\MinTester::class,
                'numeric' => Testers\NumericTester::class,
                'printable' => Testers\PrintableTester::class,
                'regexp' => Testers\RegExpTester::class,
                'stringable' => Testers\StringableTester::class,
                'string' => Testers\StringTester::class,
            ];
    }

    /**
     * @param string $name
     * @param string $testClassName
     */
    public function addTester($name, $testClassName)
    {
        $this->testers[$name] = $testClassName;
    }
    
    /**
     * @param string $tester
     * @return \Wandu\Validator\Contracts\Tester
     */
    public function load(string $tester): Tester
    {
        list($name, $arguments) = $this->getMethodAndParams($tester);
        if (!array_key_exists($tester, $this->caches)) {
            $this->caches[$tester] = $this->create($name, $arguments);
        }
        return $this->caches[$tester];
    }

    /**
     * @param string $tester
     * @param array $arguments
     * @return \Wandu\Validator\Contracts\Tester
     */
    public function create(string $tester, array $arguments = []): Tester
    {
        $className = $this->getClassName($tester);
        return new $className(...$arguments);
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
