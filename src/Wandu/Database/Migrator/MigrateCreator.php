<?php
namespace Wandu\Database\Migrator;

use InvalidArgumentException;
use Wandu\Database\Migrator\Contracts\MigrationTemplate;

class MigrateCreator
{
    /** @var \Wandu\Database\Migrator\Configuration */
    protected $config;
    
    /** @var \Wandu\Database\Migrator\Contracts\MigrationTemplate */
    protected $template;

    /**
     * @param \Wandu\Database\Migrator\Contracts\MigrationTemplate $template
     * @param \Wandu\Database\Migrator\Configuration $config
     */
    public function __construct(MigrationTemplate $template, Configuration $config)
    {
        $this->template = $template;
        $this->config = $config;
    }

    /**
     * @param string $name
     * @return string
     */
    public function create($name)
    {
        $fileName = date('ymd_His_') . $name . '.php';
        $filePath = $this->config->getPath() . '/' . $fileName;
        if (file_exists($filePath) || !is_dir($this->config->getPath())) {
            throw new InvalidArgumentException(sprintf('cannot write the file at %s.', $filePath));
        }

        file_put_contents($filePath, $this->template->template($name));
        return $filePath;
    }
}
