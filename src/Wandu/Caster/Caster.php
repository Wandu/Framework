<?php
namespace Wandu\Caster;

use Wandu\Caster\Caster\ArrayCaster;
use Wandu\Caster\Caster\BooleanCatser;
use Wandu\Caster\Caster\FloatCaster;
use Wandu\Caster\Caster\IntegerCaster;
use Wandu\Caster\Caster\StringCaster;

class Caster implements CastManagerInterface
{
    /** @var \Wandu\Caster\CasterInterface[] */
    protected $casters = [];

    /**
     * @param \Wandu\Caster\CasterInterface[] $casters
     */
    public function __construct(array $casters = [])
    {
        $this->casters = $casters + $this->getDefaultCasters();
    }

    /**
     * {@inheritdoc}
     */
    public function addCaster(string $type, CasterInterface $caster)
    {
        $this->casters[$type] = $caster;
    }
    
    /**
     * {@inheritdoc}
     */
    public function cast($value, string $type)
    {
        $originType = $type;
        if ($this->isNullable($type) && $value === null) {
            return null;
        }
        $type = rtrim($type, '?'); // strip nullable
        if ($this->isArrayable($type)) {
            $type = substr($type, 0, -2); // strip []
            return array_map(function ($item) use ($type) {
                return $this->cast($item, $type);
            }, $this->getCaster('[]', $originType)->cast($value));
        }
        return $this->getCaster($type, $originType)->cast($value);
    }
    
    /**
     * @param string $type
     * @param string $originType
     * @return \Wandu\Caster\CasterInterface
     */
    protected function getCaster(string $type, string $originType): CasterInterface
    {
        if (isset($this->casters[$type])) {
            return $this->casters[$type];
        }
        throw new UnsupportTypeException($originType);
    }
    
    /**
     * @param string $type
     * @return bool
     */
    protected function isArrayable($type): bool
    {
        return substr($type, -2) === '[]';
    }
    
    /**
     * @param string $type
     * @return bool
     */
    protected function isNullable($type): bool
    {
        return substr($type, -1) === '?';
    }

    /**
     * @return \Wandu\Caster\CasterInterface[]
     */
    protected function getDefaultCasters()
    {
        $integerCaster = new IntegerCaster();
        $floatCaster = new FloatCaster();
        $booleanCaster = new BooleanCatser();
        $arrayCaster = new ArrayCaster();
        return [
            'string' => new StringCaster(),
            'int' => $integerCaster,
            'integer' => $integerCaster,
            'num' => $floatCaster,
            'number' => $floatCaster,
            'float' => $floatCaster,
            'double' => $floatCaster,
            'bool' => $booleanCaster,
            'boolean' => $booleanCaster,
            'array' => $arrayCaster,
            '[]' => $arrayCaster, // special caster
        ];
    }
}
