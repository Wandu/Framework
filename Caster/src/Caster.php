<?php
namespace Wandu\Caster;

class Caster implements CasterInterface
{
    /** @var array */
    protected static $supportCasts = [
        'string', 'int', 'integer', 'num', 'number', 'float', 'double', 'bool', 'boolean'
    ];

    /** @var array */
    protected $boolFalse = [
        '0', 'false', 'False', 'FALSE', 'n', 'N', 'no', 'No', 'NO', 'off', 'Off', 'OFF'
    ];

    /**
     * {@inheritdoc}
     */
    public function cast($value, $type)
    {
        if (($p = strpos($type, '[]')) !== false) {
            $value = $this->normalizeArray($value);
            $type = substr($type, 0, $p);
            return array_map(function ($item) use ($type) {
                return $this->cast($item, $type);
            }, $value);
        }
        if (strpos($type, '?') !== false) {
            if ($value === null) {
                return null;
            }
            $type = str_replace($type, '?', '');
        }
        return $this->castPlainType($type, $value);
    }

    /**
     * @param mixed $value
     * @return array
     */
    private function normalizeArray($value)
    {
        if (is_array($value)) {
            return $value;
        }
        if (strpos($value, ',') !== false) {
            return explode(',', $value);
        }
        return [$value];
    }

    private function castPlainType($type, $value)
    {
        if ($value === null) {
            switch ($type) {
                case 'string':
                    return '';
                case 'int':
                case 'integer':
                    return 0;
                case 'num':
                case 'number':
                case 'float':
                case 'double':
                    return (float) 0;
                case 'bool':
                case 'boolean':
                    return false;
            }
        }

        if (is_array($value)) {
            $value = implode(',', $value);
        }

        switch ($type) {
            case 'string':
                return (string) $value;
            case 'int':
            case 'integer':
                return (int) $value;
            case 'num':
            case 'number':
            case 'float':
            case 'double':
                return (double) $value;
            case 'bool':
            case 'boolean':
                if (in_array($value, $this->boolFalse)) {
                    return false;
                }
                return (bool) $value;
        }

        throw new UnsupportTypeException($type);
    }
}
