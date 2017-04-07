<?php
namespace Wandu\Database\Contracts\Entity;

use Wandu\Database\Entity\Metadata;

interface MetadataReaderInterface
{
    /**
     * @param string $class
     * @return \Wandu\Database\Entity\Metadata
     */
    public function getMetadataFrom(string $class): Metadata;
}
