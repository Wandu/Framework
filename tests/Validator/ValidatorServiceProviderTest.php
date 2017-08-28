<?php
namespace Wandu\Validator;

use Wandu\DI\ServiceProviderInterface;
use Wandu\ServiceProviderTestCase;

class ValidatorServiceProviderTest extends ServiceProviderTestCase
{
    /**
     * {@inheritdoc}
     */
    public function getServiceProvider(): ServiceProviderInterface
    {
        return new ValidatorServiceProvider();
    }

    /**
     * {@inheritdoc}
     */
    public function getRegisterClasses(): array
    {
        return [
            TesterLoader::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases(): array
    {
        return [
        ];
    }
}
