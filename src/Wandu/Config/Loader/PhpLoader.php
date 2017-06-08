<?php
namespace Wandu\Config\Loader;

class PhpLoader extends LoaderAbstract
{
    /**
     * {@inheritdoc}
     */
    public function load()
    {
        return require $this->fileName;
    }
}
