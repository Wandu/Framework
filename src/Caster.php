<?php
namespace Wandu\Caster;

class Caster implements CasterInterface
{
    /** @var array */
    protected $boolFalse = [
        '0', 'false', 'False', 'FALSE', 'n', 'N', 'no', 'No', 'NO', 'off', 'Off', 'OFF'
    ];

    /**
     * {@inheritdoc}
     */
    public function cast($value, $type)
    {
        if (is_array($type)) {
            $value = $this->castToArray($value);
            foreach ($type as $key => $value) {
            }
            return $value;
        }
        if (($p = strpos($type, '[]')) !== false || $type === 'array') {
            $value = $this->castToArray($value);
            if ($type === 'array') {
                return $value;
            }
            $typeInArray = substr($type, 0, $p);
            return array_map(function ($item) use ($typeInArray) {
                return $this->cast($item, $typeInArray);
            }, $value);
        }
        switch ($type) {
            case 'string':
                if (is_array($value)) {
                    return implode(',', $value);
                }
                break;
            case "num":
            case "number":
                $type = 'float';
                break;
            case "bool":
            case "boolean":
                if (in_array($value, $this->boolFalse)) {
                    $value = false;
                }
                break;
        }

        settype($value, $type);
        return $value;
    }

    /**
     * @param mixed $value
     * @return array
     */
    private function castToArray($value)
    {
        if (is_array($value)) {
            return $value;
        }
        if (strpos($value, ',') !== false) {
            return explode(',', $value);
        }
        return [$value];
    }
}
