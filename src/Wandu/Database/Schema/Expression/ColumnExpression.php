<?php
namespace Wandu\Database\Schema\Expression;

use Wandu\Database\Schema\ExpressionInterface;
use Wandu\Database\Support\Attributes;
use Wandu\Database\Support\Helper;

/**
 * @see http://dev.mysql.com/doc/refman/5.7/en/create-table.html
 *
 * create_definition:
 *     col_name column_definition
 * 
 * column_definition:
 *     data_type [NOT NULL | NULL] [DEFAULT default_value]
 *         [AUTO_INCREMENT] [UNIQUE [KEY] | [PRIMARY] KEY]
 *         [COLUMN_FORMAT {FIXED|DYNAMIC|DEFAULT}]
 *         [reference_definition]
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
 *   | CHAR[(length)] [BINARY]
 *         [CHARACTER SET charset_name] [COLLATE collation_name]
 *   | VARCHAR(length) [BINARY]
 *         [CHARACTER SET charset_name] [COLLATE collation_name]
 *   | BINARY[(length)]
 *   | VARBINARY(length)
 *   | TINYBLOB
 *   | BLOB
 *   | MEDIUMBLOB
 *   | LONGBLOB
 *   | TINYTEXT [BINARY]
 *         [CHARACTER SET charset_name] [COLLATE collation_name]
 *   | TEXT [BINARY]
 *         [CHARACTER SET charset_name] [COLLATE collation_name]
 *   | MEDIUMTEXT [BINARY]
 *         [CHARACTER SET charset_name] [COLLATE collation_name]
 *   | LONGTEXT [BINARY]
 *         [CHARACTER SET charset_name] [COLLATE collation_name]
 *   | ENUM(value1,value2,value3,...)
 *         [CHARACTER SET charset_name] [COLLATE collation_name]
 *   | SET(value1,value2,value3,...)
 *         [CHARACTER SET charset_name] [COLLATE collation_name]
 *   | JSON
 *   | spatial_type
 * 
 * reference_definition:
 *     REFERENCES tbl_name (index_col_name,...)
 *         [MATCH FULL | MATCH PARTIAL | MATCH SIMPLE]
 *         [ON DELETE reference_option]
 *         [ON UPDATE reference_option]
 *
 * @method \Wandu\Database\Schema\Expression\ColumnExpression nullable()
 * @method \Wandu\Database\Schema\Expression\ColumnExpression default(mixed $value)
 * @method \Wandu\Database\Schema\Expression\ColumnExpression autoIncrement()
 * @method \Wandu\Database\Schema\Expression\ColumnExpression unique()
 * @method \Wandu\Database\Schema\Expression\ColumnExpression primary()
 *
 * @method \Wandu\Database\Schema\Expression\ColumnExpression length(int $length)
 * @method \Wandu\Database\Schema\Expression\ColumnExpression decimal(int $decimal)
 * @method \Wandu\Database\Schema\Expression\ColumnExpression fsp($fsp)
 * @method \Wandu\Database\Schema\Expression\ColumnExpression values(array $values)
 * @method \Wandu\Database\Schema\Expression\ColumnExpression unsigned()
 * @method \Wandu\Database\Schema\Expression\ColumnExpression binary()
 * @method \Wandu\Database\Schema\Expression\ColumnExpression charset()
 * @method \Wandu\Database\Schema\Expression\ColumnExpression collation()
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

    public function __toString()
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
                $stringToReturn .= $this->attributes['default']->__toString();
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
        return $stringToReturn;
    }
}
