<?php
namespace Wandu\Config\Loader;

use Symfony\Component\Yaml\Yaml;

class YmlLoader extends LoaderAbstract
{
    /**
     * {@inheritdoc}
     */
    public function load()
    {
        return Yaml::parse(file_get_contents($this->fileName));
    }
}
