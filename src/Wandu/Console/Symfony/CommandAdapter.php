<?php
namespace Wandu\Console\Symfony;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wandu\Console\Command;
use Wandu\Console\Exception\ConsoleException;

class CommandAdapter extends SymfonyCommand
{
    /**
     * @param string $name
     * @param \Wandu\Console\Command $command
     */
    public function __construct($name, Command $command)
    {
        parent::__construct($name);

        $this->command = $command;

        $this->setDescription($command->getDescription());
        $this->setHelp($command->getHelp());

        // arguments
        foreach ($command->getArguments() as $argument => $description) {
            if (substr($argument, -1) === '?') {
                $this->addArgument(substr($argument, 0, -1), InputArgument::OPTIONAL, $description);
            } elseif (substr($argument, -2) === '[]') {
                $this->addArgument(substr($argument, 0, -2), InputArgument::IS_ARRAY, $description);
            } else {
                $this->addArgument($argument, InputArgument::REQUIRED, $description);
            }
        }
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            return $this->command->withIO($input, $output)->execute();
        } catch (ConsoleException $e) {
            $output->writeln($e->getMessage());
            return -1;
        }
    }
}
