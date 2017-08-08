<?php
namespace Wandu\Migrator\Contracts;

interface MigrationInformation
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getId(): string;
}
