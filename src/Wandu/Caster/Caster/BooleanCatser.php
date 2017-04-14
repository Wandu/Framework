<?php
namespace Wandu\Caster\Caster;

use Wandu\Caster\CasterInterface;

class BooleanCatser implements CasterInterface
{
    /** @var array */
    protected $allowedBooleanString = [
        '0', 'false', 'False', 'FALSE', 'n', 'N', 'no', 'No', 'NO', 'off', 'Off', 'OFF'
    ];

    /**
     * @param array $allowedBooleanString
     */
    public function __construct(array $allowedBooleanString = null)
    {
        if ($allowedBooleanString) {
            $this->allowedBooleanString = $allowedBooleanString;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function cast($value)
    {
        if ($value === null) {
            return false;
        }
        if (in_array($value, $this->allowedBooleanString)) {
            return false;
        }
        return (bool) $value;
    }
}
