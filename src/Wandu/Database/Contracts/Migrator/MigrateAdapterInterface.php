<?php
namespace Wandu\Database\Contracts\Migrator;

interface MigrateAdapterInterface
{
    /**
     */
    public function initialize();

    /**
     * @return array
     */
    public function versions(): array;

    /**
     * @param string $id
     * @return array
     */
    public function version($id);

    /**
     * @param string $id
     */
    public function up($id);

    /**
     * @param string $id
     */
    public function down($id);
}
