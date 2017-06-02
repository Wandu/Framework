<?php
namespace Wandu\Database\Entity;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Wandu\Database\Sakila\SakilaFilm;

class MetadataTest extends TestCase
{
    /** @var \Wandu\Database\Entity\MetadataReader */
    protected $reader;
    
    public function setUp()
    {
        $this->reader = new MetadataReader(new AnnotationReader());
    }
    
    public function testGetClass()
    {
        $metadata = $this->reader->getMetadataFrom(SakilaFilm::class);
        
        static::assertSame(SakilaFilm::class, $metadata->getClass());
    }

    public function testIsIncrements()
    {
        $metadata = $this->reader->getMetadataFrom(SakilaFilm::class);

        static::assertTrue($metadata->isIncrements());
    }

    public function testHasOne()
    {
        $metadata = $this->reader->getMetadataFrom(SakilaFilm::class);

        static::assertTrue($metadata->isIncrements());
    }
}
