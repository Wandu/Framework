<?php
namespace Wandu\Migrator\Contracts;

interface Adapter
{
    /**
     */
    public function initialize();

    /**
     * @return array
     */
    public function getAppliedIds(): array;

    /**
     * @param string $id
     * @return bool
     */
    public function isApplied($id): bool;

    /**
     * @param string $id
     * @param string $name
     * @return \Wandu\Migrator\Contracts\Migration
     */
    public function getMigrationInstance(string $id, string $name): Migration;

    /**
     * @param string $id
     */
    public function addToMigrationTable($id);

    /**
     * @param string $id
     */
    public function removeFromMigrationTable($id);
}
