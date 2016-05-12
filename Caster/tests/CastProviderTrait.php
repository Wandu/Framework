<?php
namespace Wandu\Caster;

trait CastProviderTrait
{
    public function castingProvider()
    {
        return [
            ['No', 'unknown', new UnsupportTypeException('unknown')],

            [['10', '20', '30'], 'int[]', [10, 20, 30]],
            [['10', '20', '30'], 'integer[]', [10, 20, 30]],
            [['10', '20', '30'], 'string[]', ['10', '20', '30']],
            [['10', '20', '30'], 'string', '10,20,30'],

            ['10,20,30', 'int[]', [10, 20, 30]],
            ['10,20,30', 'integer[]', [10, 20, 30]],
            ['10,20,30', 'string[]', ['10', '20', '30']],
            ['10,20,30', 'string', '10,20,30'],

            ['10', 'int[]', [10]],
            ['10', 'integer[]', [10]],
            ['10', 'string[]', ['10']],
            ['10', 'string', '10'],

            ['10', 'int', 10],
            ['10', 'number', 10.0],
            ['10', 'float', 10.0],
            ['10', 'double', 10.0],
            ['10', 'bool', true],
            ['10', 'boolean', true],
            ['', 'boolean', false],
            ['true', 'boolean', true],
            ['false', 'boolean', false],
            ['off', 'boolean', false],
            ['Off', 'boolean', false],
            ['FALSE', 'boolean', false],
            ['No', 'boolean', false],

            [null, 'string', ''],
            [null, 'int', 0],
            [null, 'number', 0.0],
            [null, 'float', 0.0],
            [null, 'double', 0.0],
            [null, 'bool', false],
            [null, 'boolean', false],

            [null, 'string?', null],
            [null, 'int?', null],
            [null, 'number?', null],
            [null, 'float?', null],
            [null, 'double?', null],
            [null, 'bool?', null],
            [null, 'boolean?', null],

//            [null, 'string[]?', null],
//            [null, 'int[]?', null],
//            [null, 'number[]?', null],
//            [null, 'float[]?', null],
//            [null, 'double[]?', null],
//            [null, 'bool[]?', null],
//            [null, 'boolean[]?', null],
        ];
    }
}
