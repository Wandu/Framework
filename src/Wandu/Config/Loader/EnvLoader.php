<?php
namespace Wandu\Config\Loader;

use M1\Env\Parser;

class EnvLoader extends LoaderAbstract
{
    /**
     * {@inheritdoc}
     */
    public function load()
    {
        return (new Parser(file_get_contents($this->fileName)))->getContent();
    }
}
