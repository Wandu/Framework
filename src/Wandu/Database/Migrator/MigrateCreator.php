<?php
namespace Wandu\Database\Migrator;

use InvalidArgumentException;

class MigrateCreator
{
    /** @var \Wandu\Database\Migrator\Configuration */
    protected $config;
    
    /** @var \Wandu\Database\Migrator\MigrateTemplateInterface */
    protected $template;

    /**
     * @param \Wandu\Database\Migrator\Configuration $config
     * @param \Wandu\Database\Migrator\MigrateTemplateInterface $template
     */
    public function __construct(Configuration $config, MigrateTemplateInterface $template)
    {
        $this->config = $config;
        $this->template = $template;
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

        $contents = $this->template->getContext($name);

        file_put_contents($filePath, $contents);
        return $filePath;
    }
}
