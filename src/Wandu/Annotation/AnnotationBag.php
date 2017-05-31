<?php
namespace Wandu\Annotation;

class AnnotationBag
{
    /** @var array */
    protected $classAnnos;
    
    /** @var array[] */
    protected $propAnnos;
    
    /** @var array[] */
    protected $methodAnnos;

    /**
     * @param array $classAnnos
     * @param array $propAnnos
     * @param array $methodAnnos
     */
    public function __construct(array $classAnnos, array $propAnnos, array $methodAnnos)
    {
        $this->classAnnos = $classAnnos;
        $this->propAnnos = $propAnnos;
        $this->methodAnnos = $methodAnnos;
    }

    /**
     * @return array
     */
    public function getClassAnnotations(): array
    {
        return $this->classAnnos;
    }

    /**
     * @return array
     */
    public function getPropertiesAnnotations(): array
    {
        return $this->propAnnos;
    }

    /**
     * @param string $name
     * @return array
     */
    public function getPropertyAnnotations(string $name): array
    {
        return $this->propAnnos[$name] ?? [];
    }

    /**
     * @return array
     */
    public function getMethodsAnnotations(): array
    {
        return $this->methodAnnos;
    }

    /**
     * @param string $name
     * @return array
     */
    public function getMethodAnnotations(string $name): array
    {
        return $this->methodAnnos[$name] ?? [];
    }
}
