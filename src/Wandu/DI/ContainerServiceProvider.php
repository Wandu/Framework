<?php
namespace Wandu\DI;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;

class ContainerServiceProvider implements ServiceProviderInterface
{
    public function register(ContainerInterface $app)
    {
        $app->bind(Reader::class, AnnotationReader::class);
    }

    public function boot(ContainerInterface $app)
    {
    }
}