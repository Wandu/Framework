<?php
namespace Wandu;

use Composer\Semver\Semver;
use PHPUnit_Framework_TestCase;
use Wandu\Foundation\Application;

class DeployTest extends PHPUnit_Framework_TestCase
{
    /** @var string */
    protected $basePath = __DIR__ . '/../';
    
    /** @var string */
    protected $rootComposerJson = 'composer.json';
    
    /** @var array */
    protected $jsonFiles = [
        'src/Wandu/Caster/composer.json',
        'src/Wandu/Collection/composer.json',
        'src/Wandu/Config/composer.json',
        'src/Wandu/Console/composer.json',
        'src/Wandu/Database/composer.json',
        'src/Wandu/DateTime/composer.json',
        'src/Wandu/DI/composer.json',
        'src/Wandu/Event/composer.json',
        'src/Wandu/Foundation/composer.json',
        'src/Wandu/Http/composer.json',
        'src/Wandu/Q/composer.json',
        'src/Wandu/Router/composer.json',
        'src/Wandu/Support/composer.json',
        'src/Wandu/Validator/composer.json',
        'src/Wandu/View/composer.json',
    ];
    
    public function testIsJsonSyntaxOK()
    {
        foreach (array_merge($this->jsonFiles, [$this->rootComposerJson]) as $jsonFile) {
            $this->getJsonFromFile($jsonFile);
        }
    }

    public function testPhpVersions()
    {
        $mainRequires = $this->getJsonFromFile($this->rootComposerJson)['require'];

        foreach (array_merge($this->jsonFiles, [$this->rootComposerJson]) as $jsonFile) {
            $subComposer = $this->getJsonFromFile($jsonFile);
            $subRequires = $subComposer['require'];
            $subName = $subComposer['name'];
            foreach ($subRequires as $name => $version) {
                if (strpos($name, 'wandu/') === 0) continue;
                static::assertArrayHasKey($name, $mainRequires);
                static::assertEquals($mainRequires[$name], $version, "File: {$jsonFile} -> {$name}");
            }
        }
    }

    public function testCheckAutoloadFiles()
    {
        $rootJson = $this->getJsonFromFile($this->rootComposerJson);
        $rootAutoloadFiles = $rootJson['autoload']['files'];  
        foreach ($rootAutoloadFiles as $file) {
            static::assertFileExists($this->basePath . $file); // exists files
        }
        
        $subAutoloadFiles = [];
        foreach (array_merge($this->jsonFiles) as $jsonFile) {
            $packagePath = dirname($jsonFile);
            $json = $this->getJsonFromFile($jsonFile);
            if (isset($json['autoload']['files'])) {
                foreach ($json['autoload']['files'] as $file) {
                    static::assertFileExists("{$packagePath}/{$file}"); // exists files
                    $subAutoloadFiles[] = "{$packagePath}/{$file}";
                }
            }
        }

        sort($rootAutoloadFiles);
        sort($subAutoloadFiles);
        
        static::assertEquals($rootAutoloadFiles, $subAutoloadFiles);
    }

    public function testCheckRequire()
    {
        $rootJson = $this->getJsonFromFile($this->rootComposerJson);
        $rootRequires = array_merge($rootJson['require'], $rootJson['require-dev'], $rootJson['replace']);

        foreach (array_merge($this->jsonFiles) as $jsonFile) {
            $subJson = $this->getJsonFromFile($jsonFile);
            if (isset($subJson['require'])) {
                foreach ($subJson['require'] as $subRequireName => $subRequireVersion) {
                    static::assertArrayHasKey($subRequireName, $rootRequires); // root require contains all of sub require package
                    if ($rootRequires[$subRequireName] === 'self.version') {
                        static::assertTrue(Semver::satisfies(Application::VERSION, $subRequireVersion)); // self.version version check
                    } else {
                        static::assertEquals($rootRequires[$subRequireName], $subRequireVersion); 
                    }
                }
            }
        }
    }

    /*
     * "extra": {
     *     "branch-alias": {
     *         "dev-master": "3.0-dev"
     *     }
     * }
     */
    public function testExtraVersion()
    {
        $mainExtra = $this->getJsonFromFile($this->rootComposerJson)['extra'];

        foreach (array_merge($this->jsonFiles, [$this->rootComposerJson]) as $jsonFile) {
            $subComposer = $this->getJsonFromFile($jsonFile);
            static::assertArrayHasKey('extra', $subComposer, "File: {$jsonFile}");
            $subExtra = $subComposer['extra'];
            static::assertEquals($subExtra, $mainExtra, "File: {$jsonFile}");
        }
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
