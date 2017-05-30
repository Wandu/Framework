<?php
namespace Wandu\Validator;

use Countable;

class ErrorBag implements Countable
{
    /** @var string[] */
    private $prefixes = [];

    /** @var array */
    public $errors = [];

    /**
     * @param string $prefix
     */
    public function pushPrefix(string $prefix)
    {
        array_push($this->prefixes, $prefix);
    }
    
    public function popPrefix()
    {
        array_pop($this->prefixes);
    }

    /**
     * @param string $type
     * @param array $target
     */
    public function store(string $type, array $target = [])
    {
        array_push($this->errors, [
            $type,
            array_merge($this->prefixes, $target)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->errors);
    }

    /**
     * @return array
     */
    public function errors(): array
    {
        return array_map(function ($error) {
            $target = "";
            if (count($error[1])) {
                $target = implode(".", $error[1]);
            }
            return $error[0] . ($target ? "@{$target}" : "");
        }, $this->errors);
    }
}
