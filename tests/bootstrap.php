<?php
use Wandu\Foundation\Application;
use Wandu\Foundation\Kernels\NullKernel;

$app = new Application(new NullKernel());
$app->execute();
