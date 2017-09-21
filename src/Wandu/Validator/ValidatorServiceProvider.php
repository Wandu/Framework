<?php
namespace Wandu\Validator;

use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProvider;

class ValidatorServiceProvider extends ServiceProvider
{
    /** @var array */
    protected $testers = [];
    
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->bind(TesterLoader::class)->assign('testers', ['value' => $this->testers]);
        $app->bind(ValidatorFactory::class)->after(function (ValidatorFactory $factory) {
            return ValidatorFactory::$instance = $factory;
        });
    }
}
