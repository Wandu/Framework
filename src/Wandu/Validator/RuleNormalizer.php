<?php
namespace Wandu\Validator;

use InvalidArgumentException;
use Wandu\Validator\Contracts\Rule;

class RuleNormalizer
{
    public function normalize($rule)
    {
        while (is_callable($rule) || (is_object($rule) && $rule instanceof Rule)) {
            if (is_callable($rule)) {
                $rule = call_user_func($rule);
            } else {
                $rule = $rule->rules();
            }
        }
        // string -> array
        if (!is_array($rule)) {
            $rule = [$rule];
        }
        $normalized = [];
        foreach ($rule as $key => $value) {
            if (is_int($key) || $key === '' || $key === null) {
                $normalized[''] = array_merge($normalized[''] ?? [], (array) $value);
            } else {
                $normalized[$key] = $this->normalize($value);
            }
        }
        return $normalized;
    }
}
