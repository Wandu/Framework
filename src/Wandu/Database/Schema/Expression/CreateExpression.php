<?php
namespace Wandu\Database\Schema\Expression;

use Wandu\Database\Schema\ExpressionInterface;
use Wandu\Database\Schema\TableDefinition;
use Wandu\Database\Support\Attributes;

/**
 * @see http://dev.mysql.com/doc/refman/5.7/en/create-table.html
 * 
 * CREATE TABLE [IF NOT EXISTS] $table
 *     (create_definition,...)
 *     [table_options]
 * 
 * create_definition:
 *     col_name column_definition
 *   | [CONSTRAINT [symbol]] PRIMARY KEY [index_type] (index_col_name,...)
 *         [index_option] ...
 *   | {INDEX|KEY} [index_name] [index_type] (index_col_name,...)
 *         [index_option] ...
 *   | [CONSTRAINT [symbol]] UNIQUE [INDEX|KEY]
 *         [index_name] [index_type] (index_col_name,...)
 *         [index_option] ...
 *   | [CONSTRAINT [symbol]] FOREIGN KEY
 *         [index_name] (index_col_name,...) reference_definition
 * 
 * table_option:
 *     ENGINE [=] engine_name
 *   | [DEFAULT] CHARACTER SET [=] charset_name
 *   | [DEFAULT] COLLATE [=] collation_name
 *
 * @method \Wandu\Database\Schema\Expression\CreateExpression ifNotExists()
 * @method \Wandu\Database\Schema\Expression\CreateExpression engine(string $engine)
 * @method \Wandu\Database\Schema\Expression\CreateExpression charset(string $charset)
 * @method \Wandu\Database\Schema\Expression\CreateExpression collation(string $collation)
 */
class CreateExpression implements ExpressionInterface
{
    use Attributes;
    
    /** @var string */
    protected $table;
    
    /** @var array */
    protected $columns;

    /** @var array */
    protected $options;

    /**
     * @param string $table
     * @param callable $defineHandler
     * @param array $options
     */
    public function __construct($table, callable $defineHandler = null, array $options = [])
    {
        $this->table = $table;
        call_user_func($defineHandler, $this);
    }

    /**
     * @param string $name
     * @param int $length
     * @return \Wandu\Database\Schema\Expression\ColumnExpression
     */
    public function bit($name, $length = 1)
    {
        return $this->addColumn($name, 'bit', [
            'length' => $length,
        ]);
    }

    /**
     * @param string $name
     * @param int $length
     * @return \Wandu\Database\Schema\Expression\ColumnExpression
     */
    public function tinyInteger($name, $length = 4)
    {
        return $this->addColumn($name, 'tinyint', [
            'length' => $length,
        ]);
    }

    /**
     * @param string $name
     * @param int $length
     * @return \Wandu\Database\Schema\Expression\ColumnExpression
     */
    public function smallInteger($name, $length = 6)
    {
        return $this->addColumn($name, 'smallint', [
            'length' => $length,
        ]);
    }

    /**
     * @param string $name
     * @param int $length
     * @return \Wandu\Database\Schema\Expression\ColumnExpression
     */
    public function mediumInteger($name, $length = 9)
    {
        return $this->addColumn($name, 'mediumint', [
            'length' => $length,
        ]);
    }

    /**
     * @param string $name
     * @param int $length
     * @return \Wandu\Database\Schema\Expression\ColumnExpression
     */
    public function integer($name, $length = 11)
    {
        return $this->addColumn($name, 'int', [
            'length' => $length,
        ]);
    }

    /**
     * @param string $name
     * @param int $length
     * @return \Wandu\Database\Schema\Expression\ColumnExpression
     */
    public function bigInteger($name, $length = 20)
    {
        return $this->addColumn($name, 'bigint', [
            'length' => $length,
        ]);
    }

    /**
     * @param string $name
     * @param int $length
     * @param int $decimal
     * @return \Wandu\Database\Schema\Expression\ColumnExpression
     */
    public function double($name, $length = null, $decimal = null)
    {
        return $this->addColumn($name, 'real', [
            'length' => $length,
            'decimal' => $decimal,
        ]);
    }

    /**
     * @param string $name
     * @param int $length
     * @param int $decimal
     * @return \Wandu\Database\Schema\Expression\ColumnExpression
     */
    public function float($name, $length = null, $decimal = null)
    {
        return $this->addColumn($name, 'real', [
            'length' => $length,
            'decimal' => $decimal,
        ]);
    }

    /**
     * @param string $name
     * @param int $fsp
     * @return \Wandu\Database\Schema\Expression\ColumnExpression
     */
    public function date($name, $fsp = null)
    {
        return $this->addColumn($name, 'date', [
            'fsp' => $fsp,
        ]);
    }

    /**
     * @param string $name
     * @param int $fsp
     * @return \Wandu\Database\Schema\Expression\ColumnExpression
     */
    public function time($name, $fsp = null)
    {
        return $this->addColumn($name, 'time', [
            'fsp' => $fsp,
        ]);
    }

    /**
     * @param string $name
     * @param int $fsp
     * @return \Wandu\Database\Schema\Expression\ColumnExpression
     */
    public function timestamp($name, $fsp = null)
    {
        return $this->addColumn($name, 'timestamp', [
            'fsp' => $fsp,
        ]);
    }

    /**
     * @param string $name
     * @param int $fsp
     * @return \Wandu\Database\Schema\Expression\ColumnExpression
     */
    public function datetime($name, $fsp = null)
    {
        return $this->addColumn($name, 'datetime', [
            'fsp' => $fsp,
        ]);
    }

    /**
     * @param string $name
     * @param int $length
     * @return \Wandu\Database\Schema\Expression\ColumnExpression
     */
    public function string($name, $length = 255)
    {
        return $this->addColumn($name, 'varchar', [
            'length' => $length,
        ]);
    }

    /**
     * @param string $name
     * @param int $length
     * @return \Wandu\Database\Schema\Expression\ColumnExpression
     */
    public function char($name, $length = 1)
    {
        return $this->addColumn($name, 'char', [
            'length' => $length,
        ]);
    }

    /**
     * @param string $name
     * @param int $length
     * @return \Wandu\Database\Schema\Expression\ColumnExpression
     */
    public function varchar($name, $length)
    {
        return $this->addColumn($name, 'varchar', [
            'length' => $length,
        ]);
    }

    /**
     * @param string $name
     * @param int $length
     * @return \Wandu\Database\Schema\Expression\ColumnExpression
     */
    public function binary($name, $length = 1)
    {
        return $this->addColumn($name, 'binary', [
            'length' => $length,
        ]);
    }

    /**
     * @param string $name
     * @param int $length
     * @return \Wandu\Database\Schema\Expression\ColumnExpression
     */
    public function varbinary($name, $length)
    {
        return $this->addColumn($name, 'varbinary', [
            'length' => $length,
        ]);
    }

    /**
     * @param string $name
     * @return \Wandu\Database\Schema\Expression\ColumnExpression
     */
    public function tinyBlob($name)
    {
        return $this->addColumn($name, 'tinyblob');
    }

    /**
     * @param string $name
     * @return \Wandu\Database\Schema\Expression\ColumnExpression
     */
    public function blob($name)
    {
        return $this->addColumn($name, 'blob');
    }

    /**
     * @param string $name
     * @return \Wandu\Database\Schema\Expression\ColumnExpression
     */
    public function mediumBlob($name)
    {
        return $this->addColumn($name, 'mediumblob');
    }

    /**
     * @param string $name
     * @return \Wandu\Database\Schema\Expression\ColumnExpression
     */
    public function longBlob($name)
    {
        return $this->addColumn($name, 'longblob');
    }

    /**
     * @param string $name
     * @return \Wandu\Database\Schema\Expression\ColumnExpression
     */
    public function tinyText($name)
    {
        return $this->addColumn($name, 'tinytext');
    }

    /**
     * @param string $name
     * @return \Wandu\Database\Schema\Expression\ColumnExpression
     */
    public function text($name)
    {
        return $this->addColumn($name, 'text');
    }

    /**
     * @param string $name
     * @return \Wandu\Database\Schema\Expression\ColumnExpression
     */
    public function mediumText($name)
    {
        return $this->addColumn($name, 'mediumtext');
    }

    /**
     * @param string $name
     * @return \Wandu\Database\Schema\Expression\ColumnExpression
     */
    public function longText($name)
    {
        return $this->addColumn($name, 'longText');
    }

    /**
     * @param string $name
     * @param array $values
     * @return \Wandu\Database\Schema\Expression\ColumnExpression
     */
    public function enum($name, array $values = [])
    {
        return $this->addColumn($name, 'enum', [
            'values' => $values,
        ]);
    }

    /**
     * @param string $name
     * @param array $values
     * @return \Wandu\Database\Schema\Expression\ColumnExpression
     */
    public function set($name, array $values = [])
    {
        return $this->addColumn($name, 'set', [
            'values' => $values,
        ]);
    }

    /**
     * @param string $name
     * @param string $type
     * @param array $attributes
     * @return \Wandu\Database\Schema\Expression\ColumnExpression
     */
    public function addColumn($name, $type, array $attributes = [])
    {
        return $this->columns[] = new ColumnExpression($name, $type, $attributes);
    }
    
    public function __toString()
    {
        $sql = "CREATE TABLE";
        if (isset($this->attributes['if_not_exists'])) {
            $sql .= ' IF NOT EXISTS';
        }
        $sql .= " `{$this->table}` (";
        $sql .= implode(', ', array_reduce($this->columns, function ($carry, $definition) {
            if (is_string($definition) && $definition) {
                $carry[] = $definition;
            } elseif ($definition instanceof ExpressionInterface && $definitionAsString = $definition->__toString()) {
                $carry[] = $definitionAsString;
            }
            return $carry;
        }, []));
        $sql .= ')';
        if (isset($this->attributes['engine'])) {
            $sql .= " ENGINE={$this->attributes['engine']}";
        }
        if (isset($this->attributes['charset'])) {
            $sql .= " CHARSET={$this->attributes['charset']}";
        }
        if (isset($this->attributes['collation'])) {
            $sql .= " COLLATE={$this->attributes['collation']}";
        }
        return $sql;
    }
}
