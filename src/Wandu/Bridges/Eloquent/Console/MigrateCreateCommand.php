<?php
namespace Wandu\Bridges\Eloquent\Console;

class MigrateCreateCommand extends AbstractMigrateCommand
{
    /** @var string */
    protected $description = 'Create a migration file.';

    /** @var array */
    protected $arguments = [
        'name' => 'the name for the migration',
    ];

    function execute()
    {
        $name = $this->input->getArgument('name');
        $fileName = date('ymd_His_') . $name . '.php';
        $filePath = $this->path . '/' . $fileName;
        if (file_exists($filePath) || !is_dir($this->path)) {
            throw new \InvalidArgumentException(sprintf('cannot write the file at %s.', $filePath));
        }
        
        $contents = <<<PHP
<?php

use Wandu\Database\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

class {$name} extends Migration
{
    /**
     * @param \Illuminate\Database\Schema\Builder \$schema
     */
    public function migrate(Builder \$schema)
    {
        \$schema->create('{articles}', function (Blueprint \$table) {
            \$table->bigIncrements('id');
            \$table->timestamps();
        });
    }

    /**
     * @param \Illuminate\Database\Schema\Builder \$schema
     */
    public function rollback(Builder \$schema)
    {
        \$schema->dropIfExists('{articles}');
    }
}

PHP;
        file_put_contents($filePath, $contents);
        $this->output->writeln(
            '<info>create</info> .' . str_replace(WANDU_PATH, '', $filePath)
        );
    }
}
