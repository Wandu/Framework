<?php
namespace Wandu\Config\Loader;

class JsonLoader extends LoaderAbstract
{
    /**
     * {@inheritdoc}
     */
    public function load()
    {
        return json_decode(file_get_contents($this->fileName), true);
    }
}
