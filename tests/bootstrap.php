<?php
require __DIR__ . '/../vendor/autoload.php';

function myCapture(Closure $closure)
{
    ob_start();
    $closure->__invoke();
    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;
}
