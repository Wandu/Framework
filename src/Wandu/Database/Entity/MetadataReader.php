<?php
namespace Wandu\Database\Entity;

use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use Wandu\Database\Annotations\Cast;
use Wandu\Database\Annotations\Column;
use Wandu\Database\Annotations\RelationInterface;
use Wandu\Database\Annotations\Table;
use Wandu\Database\Contracts\Entity\MetadataReaderInterface;

class MetadataReader implements MetadataReaderInterface
{
    /** @var \Doctrine\Common\Annotations\Reader */
    protected $reader;
    
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadataFrom(string $class): Metadata
    {
        $class = ltrim($class, '\\');
        $settings = [];

        $classRefl = new ReflectionClass($class);
        $propertiesRefl = $classRefl->getProperties();

        /* @var \Wandu\Database\Annotations\Table $table */
        if ($table = $this->reader->getClassAnnotation($classRefl, Table::class)) {
            $settings['table'] = $table->name;
            $settings['connection'] = $table->connection;
            $settings['primaryKey'] = $table->primaryKey;
            $settings['increments'] = $table->increments;
        }

        $columns = [];
        $casts = [];
        $relations = [];
        foreach ($propertiesRefl as $propertyRefl) {
            foreach ($this->reader->getPropertyAnnotations($propertyRefl) as $prop) {
                if ($prop instanceof Column) {
                    $columns[$propertyRefl->name] = $prop;
                } elseif ($prop instanceof Cast) {
                    $casts[$propertyRefl->name] = $prop;
                } elseif ($prop instanceof RelationInterface) {
                    $relations[$propertyRefl->name] = $prop;
                }
            }
        }
        $settings['columns'] = $columns;
        $settings['casts'] = $casts;
        $settings['relations'] = $relations;

        return new Metadata($class, $settings);
    }
}
