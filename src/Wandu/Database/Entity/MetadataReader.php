<?php
namespace Wandu\Database\Entity;

use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use Wandu\Database\Annotations\BelongTo;
use Wandu\Database\Annotations\Column;
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
        $settings = [
            'class' => $class,
        ];
        $classRefl = new ReflectionClass($class);
        $propertiesRefl = $classRefl->getProperties();

        /* @var \Wandu\Database\Annotations\Table $table */
        if ($table = $this->reader->getClassAnnotation($classRefl, Table::class)) {
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
                    $columns[$propertyRefl->name] = $prop->name;
                    $casts[$propertyRefl->name] = $prop->cast;
                } elseif ($prop instanceof BelongTo) {
                    $relations[$propertyRefl->name] = $prop;
                }
            }
        }
        if (count($columns)) {
            $settings['columns'] = $columns;
        }
        if (count($casts)) {
            $settings['casts'] = $casts;
        }
        if (count($relations)) {
            $settings['relations'] = $relations;
        }
        return new Metadata($table->name, $settings);
    }
}
