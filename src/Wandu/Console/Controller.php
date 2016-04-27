<?php
namespace Wandu\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Controller
{
    /** @var \Symfony\Component\Console\Input\InputInterface */
    protected $input;

    /** @var \Symfony\Component\Console\Output\OutputInterface */
    protected $output;

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return \Wandu\Console\Controller
     */
    public function withIO(InputInterface $input, OutputInterface $output)
    {
        $new = clone $this;
        $new->input = $input;
        $new->output = $output;
        return $new;
    }

    abstract function execute();
}
