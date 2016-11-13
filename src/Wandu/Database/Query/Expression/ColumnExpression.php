<?php
namespace Wandu\Database\Query\Expression;

use Wandu\Database\Contracts\ExpressionInterface;
use Wandu\Database\Support\Attributes;
use Wandu\Database\Support\Helper;

/**
 * @see http://dev.mysql.com/doc/refman/5.7/en/create-table.html
 *
 * ColumnExpression:
 *     col_name column_definition
 *     [FIRST | AFTER col_name ] ............. (for alter table)
 * 
 * column_definition:
 *     data_type [NOT NULL | NULL] [DEFAULT default_value]
 *         [AUTO_INCREMENT] [UNIQUE [KEY] | [PRIMARY] KEY]
 *         [ReferenceExpression]
 * 
 * data_type:
 *     BIT[(length)]
 *   | TINYINT[(length)] [UNSIGNED] [ZEROFILL]
 *   | SMALLINT[(length)] [UNSIGNED] [ZEROFILL]
 *   | MEDIUMINT[(length)] [UNSIGNED] [ZEROFILL]
 *   | INT[(length)] [UNSIGNED] [ZEROFILL]
 *   | INTEGER[(length)] [UNSIGNED] [ZEROFILL]
 *   | BIGINT[(length)] [UNSIGNED] [ZEROFILL]
 *   | REAL[(length,decimals)] [UNSIGNED] [ZEROFILL]
 *   | DOUBLE[(length,decimals)] [UNSIGNED] [ZEROFILL]
 *   | FLOAT[(length,decimals)] [UNSIGNED] [ZEROFILL]
 *   | DECIMAL[(length[,decimals])] [UNSIGNED] [ZEROFILL]
 *   | NUMERIC[(length[,decimals])] [UNSIGNED] [ZEROFILL]
 *   | DATE
 *   | TIME[(fsp)]
 *   | TIMESTAMP[(fsp)]
 *   | DATETIME[(fsp)]
 *   | YEAR
 *   | CHAR[(length)] [BINARY] [CHARACTER SET charset_name] [COLLATE collation_name]
 *   | VARCHAR(length) [BINARY] [CHARACTER SET charset_name] [COLLATE collation_name]
 *   | BINARY[(length)]
 *   | VARBINARY(length)
 *   | TINYBLOB
 *   | BLOB
 *   | MEDIUMBLOB
 *   | LONGBLOB
 *   | TINYTEXT [BINARY] [CHARACTER SET charset_name] [COLLATE collation_name]
 *   | TEXT [BINARY] [CHARACTER SET charset_name] [COLLATE collation_name]
 *   | MEDIUMTEXT [BINARY] [CHARACTER SET charset_name] [COLLATE collation_name]
 *   | LONGTEXT [BINARY] [CHARACTER SET charset_name] [COLLATE collation_name]
 *   | ENUM(value1,value2,value3,...) [CHARACTER SET charset_name] [COLLATE collation_name]
 *   | SET(value1,value2,value3,...) [CHARACTER SET charset_name] [COLLATE collation_name]
 *   | JSON
 *   | spatial_type
 *
 * @method \Wandu\Database\Query\Expression\ColumnExpression nullable()
 * @method \Wandu\Database\Query\Expression\ColumnExpression default(mixed $value)
 * @method \Wandu\Database\Query\Expression\ColumnExpression autoIncrement()
 * @method \Wandu\Database\Query\Expression\ColumnExpression unique()
 * @method \Wandu\Database\Query\Expression\ColumnExpression primary()
 *
 * @method \Wandu\Database\Query\Expression\ColumnExpression length(int $length)
 * @method \Wandu\Database\Query\Expression\ColumnExpression decimal(int $decimal)
 * @method \Wandu\Database\Query\Expression\ColumnExpression fsp($fsp)
 * @method \Wandu\Database\Query\Expression\ColumnExpression values(array $values)
 * @method \Wandu\Database\Query\Expression\ColumnExpression unsigned()
 * @method \Wandu\Database\Query\Expression\ColumnExpression binary()
 * @method \Wandu\Database\Query\Expression\ColumnExpression charset()
 * @method \Wandu\Database\Query\Expression\ColumnExpression collation()
 *
 * @method \Wandu\Database\Query\Expression\ColumnExpression first()
 * @method \Wandu\Database\Query\Expression\ColumnExpression after(string $column)
 */
class ColumnExpression implements ExpressionInterface
{
    use Attributes;
    
    /** @var array */
    protected static $typesHavingLength = [
        'bit',
        'tinyint',
        'smallint',
        'mediumint',
        'int',
        'integer',
        'bigint',
        'char',
        'varchar',
        'binary',
        'varbinary',
    ];

    /** @var array */
    protected static $typesHavingUnsigned = [
        'bit',
        'tinyint',
        'smallint',
        'mediumint',
        'int',
        'integer',
        'bigint',
        'real',
        'double',
        'float',
        'decimal',
        'numeric',
    ];

    /** @var array */
    protected static $typesHavingDecimal = [
        'real',
        'double',
        'float',
        'decimal',
        'numeric',
    ];
    
    /** @var array */
    protected static $typesHavingBinary = [
        'char',
        'varchar',
        'tinytext',
        'text',
        'mediumtext',
        'longtext',
    ];

    /** @var array */
    protected static $typesHavingFsp = [
        'time',
        'timestamp',
        'datetime',
    ];

    /** @var array */
    protected static $typesHavingValues = [
        'enum',
        'set',
    ];

    /** @var string */
    protected $name;

    /** @var string */
    protected $type;
    
    /** @var \Wandu\Database\Query\Expression\ReferenceExpression */
    protected $reference;

    /**
     * @param string $name
     * @param string $type
     * @param array $attributes
     */
    public function __construct($name, $type, array $attributes = [])
    {
        $this->name = $name;
        $this->type = $type;
        $this->attributes = $attributes;
    }

    /**
     * @param string $table
     * @param string|array $column
     * @return \Wandu\Database\Query\Expression\ReferenceExpression
     */
    public function reference($table, $column)
    {
        return $this->reference = new ReferenceExpression($table, is_array($column) ? $column : [$column]);
    }

    /**
     * {@inheritdoc}
     */
    public function toSql()
    {
        $stringToReturn = "`{$this->name}` " . $this->getTypeString();
        if (isset($this->attributes['nullable']) && $this->attributes['nullable']) {
            $stringToReturn .= ' NULL';
        } else {
            $stringToReturn .= ' NOT NULL';
        }
        if (isset($this->attributes['default'])) {
            $stringToReturn .= " DEFAULT ";
            if ($this->attributes['default'] instanceof ExpressionInterface) {
                $stringToReturn .= $this->attributes['default']->toSql();
            } else {
                $stringToReturn .= "'" . addslashes($this->attributes['default']) . "'";
            }
        }
        if (isset($this->attributes['auto_increment'])) {
            $stringToReturn .= " AUTO_INCREMENT";
        }
        if (isset($this->attributes['unique'])) {
            $stringToReturn .= ' UNIQUE KEY';
        }
        if (isset($this->attributes['primary'])) {
            $stringToReturn .= ' PRIMARY KEY';
        }
        if (isset($this->reference)) {
            $referenceString = $this->reference->toSql();
            if ($referenceString) {
                $stringToReturn .= " {$referenceString}";
            }
        }
        return $stringToReturn;
    }

    protected function getTypeString()
    {
        $stringToReturn = strtoupper($this->type);
        $lowerType = strtolower($this->type);
        $enableLength = in_array($lowerType, static::$typesHavingLength) && isset($this->attributes['length']);
        $enableDecimal = in_array($lowerType, static::$typesHavingDecimal) && isset($this->attributes['decimal']);
        $enableFsp = in_array($lowerType, static::$typesHavingFsp) && isset($this->attributes['fsp']);
        $enableValues = in_array($lowerType, static::$typesHavingValues) && isset($this->attributes['values']);
        if ($enableLength || $enableDecimal || $enableFsp || $enableValues) {
            $stringToReturn .= '(';
            if ($enableLength && $enableDecimal) {
                $stringToReturn .= $this->attributes['length'] . ", " . $this->attributes['decimal'];
            } elseif ($enableLength) {
                $stringToReturn .= $this->attributes['length'];
            } elseif ($enableFsp) {
                $stringToReturn .= $this->attributes['fsp'];
            } elseif ($enableValues) {
                $stringToReturn .= Helper::arrayImplode(", ", $this->attributes['values'], '\'', '\'');
            }
            $stringToReturn .= ')';
        }
        if (in_array($lowerType, static::$typesHavingUnsigned) && isset($this->attributes['unsigned'])) {
            $stringToReturn .= " UNSIGNED";
        }
        if (in_array($lowerType, static::$typesHavingBinary)) {
            if (isset($this->attributes['binary'])) {
                $stringToReturn .= " BINARY";
            }
            if (isset($this->attributes['charset'])) {
                $stringToReturn .= " CHARACTER SET {$this->attributes['charset']}";
            }
            if (isset($this->attributes['collation'])) {
                $stringToReturn .= " COLLATE {$this->attributes['collation']}";
            }
        }
        if (isset($this->attributes['first']) && $this->attributes['first']) {
            $stringToReturn .= ' FIRST';
        }
        if (isset($this->attributes['after'])) {
            $stringToReturn .= " AFTER `{$this->attributes['after']}`";
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
