<?php
namespace ___NAMESPACE___;

use Wandu\Foundation\Definitions\StandardDefinition;
use Wandu\Router\Router;
use Wandu\DI\ContainerInterface;

use ___NAMESPACE___\Controllers\HelloWorldController;

class ApplicationDefinition extends StandardDefinition
{
    /** @var array */
    protected $configs;

    /**
     * @param array $configs
     */
    public function __construct(array $configs)
    {
        $this->configs = $configs;
    }

    /**
     * {@inheritdoc}
     */
    public function configs()
    {
        return $this->configs;
    }

    /**
     * {@inheritdoc}
     */
    public function providers(ContainerInterface $app)
    {
        parent::providers($app);
        $app->register(new ApplicationServiceProvider());
    }

    /**
     * {@inheritdoc}
     */
    public function routes(Router $router)
    {
        $router->get('/', HelloWorldController::class);
    }
};
