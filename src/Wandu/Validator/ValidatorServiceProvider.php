<?php
namespace Wandu\Validator;

use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;

class ValidatorServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->closure(ValidatorFactory::class, function () {
            return validator();
        });
        $app->alias('validator', ValidatorFactory::class);
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
    }
}
