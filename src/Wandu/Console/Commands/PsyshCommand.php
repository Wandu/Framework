<?php
namespace Wandu\Console\Commands;

use Psy\Shell;
use Wandu\Console\Command;

class PsyshCommand extends Command
{
    /** @var string */
    protected $description = "Execute psysh with Wandu";

    /** @var \Psy\Shell */
    protected $shell;
    
    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        return $this->shell->run();
    }
}
