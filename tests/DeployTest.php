<?php
namespace Wandu;

use Mockery;
use PHPUnit_Framework_TestCase;

class DeployTest extends PHPUnit_Framework_TestCase
{
    /** @var string */
    protected $basePath = __DIR__ . '/../';
    
    /** @var string */
    protected $rootJsonFiles = 'composer.json';
    
    /** @var array */
    protected $jsonFiles = [
        'src/Wandu/Caster/composer.json',
        'src/Wandu/Compiler/composer.json',
        'src/Wandu/Config/composer.json',
        'src/Wandu/Console/composer.json',
        'src/Wandu/DI/composer.json',
        'src/Wandu/Event/composer.json',
        'src/Wandu/Foundation/composer.json',
        'src/Wandu/Http/composer.json',
        'src/Wandu/Q/composer.json',
        'src/Wandu/Router/composer.json',
        'src/Wandu/Tempy/composer.json',
    ];
    
    public function testIsJsonSyntaxOK()
    {
        foreach (array_merge($this->jsonFiles, [$this->rootJsonFiles]) as $jsonFile) {
            $this->getJsonFromFile($jsonFile);
        }
    }

    public function testPhpVersions()
    {
        $composer = $this->getJsonFromFile($this->rootJsonFiles);
        $requirePhpVersion = $composer['require']['php'];
    }
    
    protected function getJsonFromFile($file)
    {
        $contents = json_decode(file_get_contents($this->basePath . $file), true);
        if (json_last_error()) {
            throw new \RuntimeException("Json Error({$file}) : " . json_last_error_msg());
        }
        return $contents;
    }
}
