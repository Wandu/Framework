<?php
namespace Wandu\Console\Symfony;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wandu\Console\Command;

class CommandProxy extends SymfonyCommand
{
    public function __construct($name, Command $command)
    {
        parent::__construct($name);
        $this->command = $command;
        $this->setDescription($command->getDescription());
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return $this->command->withIO($input, $output)->execute();
    }
}
