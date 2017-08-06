<?php
namespace Wandu\Console\Contracts;

use Symfony\Component\Console\Command\Command;

interface CommandAttachable
{
    /**
     * @param string $name
     * @param string|\Wandu\Console\Command|\Symfony\Component\Console\Command\Command $command
     * @return \Symfony\Component\Console\Command\Command
     */
    public function attach(string $name, $command): Command;
}
