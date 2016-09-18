<?php
namespace Wandu\Database\Schema\Expression;

use Wandu\Database\Query\ExpressionInterface;
use Wandu\Database\Support\Attributes;
use Wandu\Database\Support\Helper;

/**
 * @see http://dev.mysql.com/doc/refman/5.7/en/create-table.html
 *
 * ConstraintExpression:
 *     KEY [index_name] [index_type] (index_col_name,...)
 *   | PRIMARY KEY [index_type] (index_col_name,...)
 *   | UNIQUE KEY [index_name] [index_type] (index_col_name,...)
 *   | FOREIGN KEY [index_name] (index_col_name,...) ReferenceExpression
 * index_type:
 *     USING {BTREE | HASH}
 *
 * @method \Wandu\Database\Schema\Expression\ConstraintExpression using(int $indexType)
 */
class ConstraintExpression implements ExpressionInterface
{
    use Attributes;
    
    const KEY_TYPE_NONE = 0;
    const KEY_TYPE_PRIMARY = 1;
    const KEY_TYPE_UNIQUE = 2;
    const KEY_TYPE_FOREIGN = 3;
    
    const INDEX_TYPE_NONE = 0; // but maybe use B-Tree
    const INDEX_TYPE_BTREE = 1;
    const INDEX_TYPE_HASH = 2;
    
    /** @var array */
    protected $columns;
    
    /** @var string */
    protected $name;

    /** @var \Wandu\Database\Schema\Expression\ReferenceExpression */
    protected $reference;
    
    /**
     * @param array $columns
     * @param string $name
     * @param array $attributes
     */
    public function __construct(array  $columns, $name = null, array $attributes = [])
    {
        $this->columns = $columns;
        $this->name = $name;
        $this->attributes = $attributes;
    }

    /**
     * @param string $table
     * @param string|array $column
     * @return \Wandu\Database\Schema\Expression\ReferenceExpression
     */
    public function reference($table, $column)
    {
        return $this->reference = new ReferenceExpression($table, is_array($column) ? $column : [$column]);
    }

    /**
     * @example
     *          KEY [index_name] [index_type] (index_col_name,...)
     *  PRIMARY KEY              [index_type] (index_col_name,...)
     *  UNIQUE  KEY [index_name] [index_type] (index_col_name,...)
     *  FOREIGN KEY [index_name]              (index_col_name,...) ReferenceExpression
     * 
     * {@inheritdoc}
     */
    public function toSql()
    {
        $stringToReturn = '';
        $keyType = isset($this->attributes['key_type']) ? $this->attributes['key_type'] : static::KEY_TYPE_NONE;
        switch ($keyType) {
            case static::KEY_TYPE_PRIMARY:
                $stringToReturn .= 'PRIMARY '; break;
            case static::KEY_TYPE_UNIQUE:
                $stringToReturn .= 'UNIQUE '; break;
            case static::KEY_TYPE_FOREIGN:
                $stringToReturn .= 'FOREIGN '; break;
            default:
                $keyType = static::KEY_TYPE_NONE; // normalize
        }
        $stringToReturn .= 'KEY';
        if ($keyType !== static::KEY_TYPE_PRIMARY && $this->name) {
            $stringToReturn .= " `{$this->name}`";
        }
        if ($keyType !== static::KEY_TYPE_FOREIGN && isset($this->attributes['using'])) {
            switch ($this->attributes['using']) {
                case static::INDEX_TYPE_BTREE:
                    $stringToReturn .= ' USING BTREE'; break;
                case static::INDEX_TYPE_HASH:
                    $stringToReturn .= ' USING HASH'; break;
            }
        }
        $stringToReturn .= ' (' . Helper::arrayImplode(', ', $this->columns, '`', '`') .')';
        if ($keyType === static::KEY_TYPE_FOREIGN && isset($this->reference)) {
            $referenceString = $this->reference->toSql();
            if ($referenceString) {
                $stringToReturn .= " {$referenceString}";
            }
        }
        return $stringToReturn;
    }

    /**
     * {@inheritdoc}
     */
    public function getBindings()
    {
        return [];
    }
}
