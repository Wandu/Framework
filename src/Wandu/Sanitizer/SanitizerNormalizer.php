<?php
namespace Wandu\Sanitizer;

use Wandu\Validator\ValidatorNormalizer;

class SanitizerNormalizer extends ValidatorNormalizer
{
    /**
     * {@inheritdoc}
     */
    public function normalize($rule): array
    {
        if (is_string($rule) && class_exists($rule)) {
            $rule = (new $rule)->rule();
        }
        return parent::normalize($rule);
    }
}
