<?php
namespace Wandu\Validator;

use Wandu\Validator\Contracts\Rule;
use Wandu\Validator\Contracts\RuleNormalizable;

class ValidatorNormalizer implements RuleNormalizable
{
    public function normalize($rule): array
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
        $normalized = [[], []];
        foreach ($rule as $key => $value) {
            if (is_int($key) || $key === '' || $key === null) {
                $normalized[0] = array_merge($normalized[0] ?? [], (array) $value);
            } else {
                $target = TargetName::parse($key);
                $normalized[1][] = [
                    [$target->getName(), $target->getIterator(), $target->isOptional(), ],
                    $this->normalize($value),
                ];
            }
        }
        return $normalized;
    }
}
