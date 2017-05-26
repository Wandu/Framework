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
        $app->closure(TesterFactory::class, function () {
            return new TesterFactory();
        })->after(function (TesterFactory $instance) {
            $instance->setAsGlobal();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
    }
}
