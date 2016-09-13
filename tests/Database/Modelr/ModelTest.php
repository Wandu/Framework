<?php
namespace Wandu\Database\Modelr;

use PHPUnit_Framework_TestCase;
use Wandu\Database\Connector\MysqlConnector;
use Wandu\Database\Modelr\Contracts\ModelInterface;
use Wandu\Database\Modelr\Traits\ModelMethods;
use Wandu\Database\Query\QueryBuilder;

class ModelTest extends PHPUnit_Framework_TestCase
{
    public function testSimpleSelectQuery()
    {
        
        User::setConnection();
        
        $users = User::all(function (QueryBuilder $builder) {
            return $builder->where(QueryBuilder::and());
        });

        $user = User::first(function (QueryBuilder $builder) {
            return $builder->where('id', 30);
        });
        
        
        $user = new User();
        $user->save();
    }
}

class User extends Model  
{
    /** @var string */
    protected static $table = "users";
    
    /** @var array */
    protected static $columns = [];
}
