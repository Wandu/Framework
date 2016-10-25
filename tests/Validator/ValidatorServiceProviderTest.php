<?php
namespace Wandu\Validator;

use Wandu\ServiceProviderTestCase;

class ValidatorServiceProviderTest extends ServiceProviderTestCase
{
    public function getServiceProvider()
    {
        return new ValidatorServiceProvider();
    }

    public function getRegisterClasses()
    {
        return [
            ValidatorFactory::class,
            'validator' => ValidatorFactory::class,
        ];
    }
}
