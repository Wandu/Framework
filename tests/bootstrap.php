<?php
use Doctrine\Common\Annotations\AnnotationRegistry;
use Wandu\Foundation\Application;
use Wandu\Foundation\Kernels\NullKernel;

AnnotationRegistry::registerLoader('class_exists');

$app = new Application(new NullKernel());
$app->execute();
