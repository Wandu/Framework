<?php
namespace Wandu\Console\Symfony;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wandu\Console\Command;
use Wandu\Console\Exception\ConsoleException;

class CommandAdapter extends SymfonyCommand
{
    /** @var \Wandu\Console\Command */
    protected $command;
    
    /**
     * @param \Wandu\Console\Command $command
     */
    public function __construct(Command $command)
    {
        parent::__construct('_');
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

        foreach ($command->getOptions() as $option => $description) {
            if (substr($option, -1) === '?') {
                $this->addOption(substr($option, 0, -1), null, InputOption::VALUE_OPTIONAL, $description);
            } elseif (substr($option, -2) === '[]') {
                $this->addOption(substr($option, 0, -2), null, InputOption::VALUE_IS_ARRAY, $description);
            } else {
                $this->addOption($option, null, InputOption::VALUE_REQUIRED, $description);
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
