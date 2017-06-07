<?php
namespace Wandu\Annotation;

use Wandu\Collection\ArrayList;
use Wandu\Collection\ArrayMap;
use Wandu\Collection\Contracts\ListInterface;
use Wandu\Collection\Contracts\MapInterface;

class AnnotationBag
{
    /** @var \Wandu\Collection\Contracts\ListInterface */
    protected $classAnnos;
    
    /** @var \Wandu\Collection\Contracts\MapInterface */
    protected $propAnnos;

    /** @var \Wandu\Collection\Contracts\MapInterface */
    protected $methodAnnos;

    /**
     * @param array $classAnnos
     * @param array $propAnnos
     * @param array $methodAnnos
     */
    public function __construct(array $classAnnos, array $propAnnos, array $methodAnnos)
    {
        $this->classAnnos = new ArrayList($classAnnos);
        $this->propAnnos = (new ArrayMap($propAnnos))->map(function ($items) {
            return new ArrayList($items);
        });
        $this->methodAnnos = (new ArrayMap($methodAnnos))->map(function ($items) {
            return new ArrayList($items);
        });
    }

    /**
     * @return \Wandu\Collection\Contracts\ListInterface
     */
    public function getClassAnnotations(): ListInterface
    {
        return $this->classAnnos;
    }

    /**
     * @return \Wandu\Collection\Contracts\MapInterface
     */
    public function getPropertiesAnnotations(): MapInterface
    {
        return $this->propAnnos;
    }

    /**
     * @param string $name
     * @return \Wandu\Collection\Contracts\ListInterface
     */
    public function getPropertyAnnotations(string $name): ListInterface
    {
        return $this->propAnnos[$name] ?? new ArrayList();
    }

    /**
     * @return \Wandu\Collection\Contracts\MapInterface
     */
    public function getMethodsAnnotations(): MapInterface
    {
        return $this->methodAnnos;
    }

    /**
     * @param string $name
     * @return \Wandu\Collection\Contracts\ListInterface
     */
    public function getMethodAnnotations(string $name): ListInterface
    {
        return $this->methodAnnos[$name] ?? new ArrayList();
    }
}
