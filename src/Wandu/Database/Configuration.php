<?php
namespace Wandu\Database;

use Doctrine\Common\Annotations\AnnotationReader;
use Wandu\Caster\Caster;
use Wandu\Caster\CastManagerInterface;
use Wandu\Database\Contracts\Entity\MetadataReaderInterface;
use Wandu\Database\Entity\MetadataReader;
use Wandu\Event\Contracts\EventEmitter;

class Configuration
{
    /** @var \Wandu\Database\Contracts\Entity\MetadataReaderInterface */
    protected $metadataReader;
    
    /** @var \Wandu\Caster\CastManagerInterface */
    protected $caster;
    
    /** @var \Wandu\Event\Contracts\EventEmitter */
    protected $emitter;
    
    public function __construct(
        MetadataReaderInterface $metadataReader = null,
        CastManagerInterface $caster = null,
        EventEmitter $emitter = null
    ) {
        $this->metadataReader = $metadataReader ?? new MetadataReader(new AnnotationReader());
        $this->caster = $caster ?? new Caster();
        $this->emitter = $emitter;
    }
    
    /**
     * @return \Wandu\Database\Contracts\Entity\MetadataReaderInterface
     */
    public function getMetadataReader(): MetadataReaderInterface
    {
        return $this->metadataReader;
    }

    /**
     * @return \Wandu\Caster\CastManagerInterface
     */
    public function getCaster(): CastManagerInterface
    {
        return $this->caster;
    }

    /**
     * @return \Wandu\Event\Contracts\EventEmitter|null
     */
    public function getEmitter()
    {
        return $this->emitter;
    }
}
