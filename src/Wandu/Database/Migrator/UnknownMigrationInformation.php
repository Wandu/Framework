<?php
namespace Wandu\Database\Migrator;

use RuntimeException;
use Wandu\Database\Migrator\Contracts\MigrationInformation;

class UnknownMigrationInformation implements MigrationInformation
{
    /** @var string */
    protected $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        throw new RuntimeException('name is unknown');
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return $this->id;
    }
}
