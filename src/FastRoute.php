<?php
namespace Wandu\Router;

use FastRoute\DataGenerator;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use FastRoute\RouteParser;
use FastRoute\RouteParser\Std as StdRouteParser;
use FastRoute\DataGenerator\GroupCountBased as GroupCountBasedDataGenerator;
use FastRoute\Dispatcher\GroupCountBased as GroupCountBasedDispatcher;

class FastRoute
{
    /** @var RouteCollector */
    private $collector;

    /**
     * @param DataGenerator $generator
     * @param RouteParser $parser
     */
    public function __construct(DataGenerator $generator = null, RouteParser $parser = null)
    {
        if (!isset($parser)) {
            $parser = new StdRouteParser();
        }
        if (!isset($generator)) {
            $generator = new GroupCountBasedDataGenerator;
        }
        $this->collector = new RouteCollector($parser, $generator);
    }

    /**
     * @param string|string[] $httpMethod
     * @param string $route
     * @param string $handler
     */
    public function addRoute($httpMethod, $route, $handler)
    {
        $this->collector->addRoute($httpMethod, $route, $handler);
    }

    /**
     * @return Dispatcher
     */
    public function getDispatcher()
    {
        return new GroupCountBasedDispatcher($this->collector->getData());
    }

    /**
     * @param string $cachePath
     * @param bool $recached
     * @return Dispatcher
     */
    public function getCachedDispatcher($cachePath, $recached = false)
    {
        if (!$recached && file_exists($cachePath)) {
            $dispatchData = require $cachePath;
            if (!is_array($dispatchData)) {
                throw new \RuntimeException('Invalid cache file "' . $cachePath . '"');
            }
            return new GroupCountBasedDispatcher($dispatchData);
        }

        $dispatchData = $this->collector->getData();
        file_put_contents(
            $cachePath,
            '<?php return ' . var_export($dispatchData, true) . ';'
        );
        return new GroupCountBasedDispatcher($dispatchData);
    }
}
