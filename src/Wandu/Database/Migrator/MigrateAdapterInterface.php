<?php
namespace Wandu\Database\Migrator;

interface MigrateAdapterInterface
{
    /**
     * @return \Wandu\Database\Migrator\MigrateAdapterInterface
     */
    public function initialize();

    /**
     * @return array
     */
    public function versions();

    /**
     * @param string $id
     * @return array
     */
    public function version($id);

    /**
     * @param string $id
     * @param string $source
     * @return \Wandu\Database\Migrator\MigrateAdapterInterface
     */
    public function up($id, $source);

    /**
     * @param string $id
     * @return \Wandu\Database\Migrator\MigrateAdapterInterface
     */
    public function down($id);
}
