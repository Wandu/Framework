<?php
namespace Wandu\Database\Entity;

use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
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

        class_exists(Table::class);
        class_exists(Column::class);

        /* @var \Wandu\Database\Annotations\Table $table */
        if ($table = $this->reader->getClassAnnotation($classRefl, Table::class)) {
            $settings['primaryKey'] = $table->primaryKey;
            $settings['increments'] = $table->increments;
        }

        $columns = [];
        $casts = [];
        foreach ($propertiesRefl as $propertyRefl) {
            /* @var \Wandu\Database\Annotations\Column $column */
            if ($column = $this->reader->getPropertyAnnotation($propertyRefl, Column::class)) {
                $columns[$propertyRefl->name] = $column->name;
                $casts[$propertyRefl->name] = $column->cast;
            }
        }
        if (count($columns)) {
            $settings['columns'] = $columns;
        }
        if (count($casts)) {
            $settings['casts'] = $casts;
        }
        return new Metadata($table->name, $settings);
    }
}
