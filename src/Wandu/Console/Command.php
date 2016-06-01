<?php
namespace Wandu\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Command
{
    /** @var \Symfony\Component\Console\Input\InputInterface */
    protected $input;

    /** @var \Symfony\Component\Console\Output\OutputInterface */
    protected $output;

    /** @var string */
    protected $description = '';
    
    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return \Wandu\Console\Command
     */
    public function withIO(InputInterface $input, OutputInterface $output)
    {
        $new = clone $this;
        $new->input = $input;
        $new->output = $output;
        return $new;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    abstract function execute();
}
