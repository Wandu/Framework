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
    
    /** @var string */
    protected $help = '';
    
    /** @var array */
    protected $arguments = [];
    
    /** @var array */
    protected $options = [];
    
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

    /**
     * @return string
     */
    public function getHelp()
    {
        return $this->help;
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
    
    abstract function execute();
}
