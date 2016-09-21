<?php
namespace Wandu\Database\Schema\Expression;

use Wandu\Database\Query\ExpressionInterface;
use Wandu\Database\Support\Attributes;
use Wandu\Database\Support\Helper;

/**
 * @see http://dev.mysql.com/doc/refman/5.7/en/create-table.html
 *
 * ReferenceExpression:
 *     REFERENCES tbl_name (index_col_name,...)
 *         [ON DELETE reference_option]
 *         [ON UPDATE reference_option]
 * reference_option:
 *     RESTRICT | CASCADE | SET NULL | NO ACTION
 *
 * @method \Wandu\Database\Schema\Expression\ReferenceExpression onUpdate(int $option)
 * @method \Wandu\Database\Schema\Expression\ReferenceExpression onDelete(int $option)
 */
class ReferenceExpression implements ExpressionInterface
{
    use Attributes;
    
    const OPTION_NO_ACTION = 0;
    const OPTION_RESTRICT = 1;
    const OPTION_CASCADE = 2;
    const OPTION_SET_NULL = 3;

    /** @var string */
    protected $table;

    /** @var array */
    protected $columns;

    /**
     * @param string $table
     * @param array $columns
     * @param array $attributes
     */
    public function __construct($table, array  $columns, array $attributes = [])
    {
        $this->table = $table;
        $this->columns = $columns;
        $this->attributes = $attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function toSql()
    {
        $sqlToReturn = "REFERENCES `{$this->table}`";
        $sqlToReturn .= '(' . Helper::arrayImplode(', ', $this->columns, '`', '`') . ')';
        if (isset($this->attributes['on_delete'])) {
            switch ($this->attributes['on_delete']) {
                case static::OPTION_NO_ACTION:
                    $sqlToReturn .= ' ON DELETE NO ACTION'; break;
                case static::OPTION_RESTRICT:
                    $sqlToReturn .= ' ON DELETE RESTRICT'; break;
                case static::OPTION_CASCADE:
                    $sqlToReturn .= ' ON DELETE CASCADE'; break;
                case static::OPTION_SET_NULL:
                    $sqlToReturn .= ' ON DELETE SET NULL'; break;
            }
        }
        if (isset($this->attributes['on_update'])) {
            switch ($this->attributes['on_update']) {
                case static::OPTION_NO_ACTION:
                    $sqlToReturn .= ' ON UPDATE NO ACTION'; break;
                case static::OPTION_RESTRICT:
                    $sqlToReturn .= ' ON UPDATE RESTRICT'; break;
                case static::OPTION_CASCADE:
                    $sqlToReturn .= ' ON UPDATE CASCADE'; break;
                case static::OPTION_SET_NULL:
                    $sqlToReturn .= ' ON UPDATE SET NULL'; break;
            }
        }
        return $sqlToReturn; 
    }

    /**
     * {@inheritdoc}
     */
    public function getBindings()
    {
        return [];
    }
}
