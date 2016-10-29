<?php
namespace Wandu\Foundation\Definitions;

use Wandu\Foundation\Contracts\DefinitionInterface;
use Wandu\Installation\InstallServiceProvider;

class NeedInstallDefinition implements DefinitionInterface
{
    /**
     * {@inheritdoc}
     */
    public function configs()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function providers()
    {
        return [
            InstallServiceProvider::class,
        ];
    }
}
