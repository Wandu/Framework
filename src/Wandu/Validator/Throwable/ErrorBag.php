<?php
namespace Wandu\Validator\Throwable;

use Countable;
use Wandu\Validator\Contracts\ErrorThrowable;

class ErrorBag implements ErrorThrowable, Countable
{
    /** @var array */
    public $errors = [];

    /**
     * @param string $type
     * @param array $keys
     */
    public function throws(string $type, array $keys = [])
    {
        array_push($this->errors, [$type, $keys]);
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
            $target = array_reduce($error[1] ?? [], function ($carry, $param) {
                if ($carry === null) return $param;
                if (is_numeric($param) &&is_int($param + 0)) {
                    return $carry . '[' . $param . ']';
                }
                return $carry . '.' . $param;
            });
            return $error[0] . ($target ? "@{$target}" : "");
        }, $this->errors);
    }
}
