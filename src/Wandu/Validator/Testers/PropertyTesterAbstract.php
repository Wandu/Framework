<?php
namespace Wandu\Validator\Testers;

use Wandu\Validator\Contracts\Tester;

abstract class PropertyTesterAbstract implements Tester
{
    /** @var string[] */
    protected $keys;

    public function __construct($criteria)
    {
        $this->keys = array_map("trim", explode(".", $criteria));
    }
    
    protected function getProp($origin)
    {
        $keys = $this->keys;
        $prop = $origin;
        while (count($keys)) {
            $key = array_shift($keys);
            if (!isset($prop[$key])) return;
            $prop = $prop[$key];
        }
        return $prop;
    }
}
