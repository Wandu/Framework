<?php
namespace Wandu\Database\Contracts;

interface ModelInterface
{
    /**
     * @param array $values
     * @return \Wandu\Database\Contracts\ModelInterface
     */
    public static function fromRepository(array $values);
    
    /**
     * @return array
     */
    public function toRepository();

    /**
     * @param string|int $identifier
     */
    public function setIdentifier($identifier);

    /**
     * @return string|int
     */
    public function getIdentifier();
}
