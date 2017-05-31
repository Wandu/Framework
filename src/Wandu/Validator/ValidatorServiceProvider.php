<?php
namespace Wandu\Validator;

use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;

class ValidatorServiceProvider implements ServiceProviderInterface
{
    /** @var array */
    protected $testers = [];
    
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->closure(TesterFactory::class, function () {
            return new TesterFactory($this->testers);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
    }
}
