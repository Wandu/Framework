<?php
namespace Wandu\Queue\Contracts;

interface JobInterface
{
    /**
     * @return mixed
     */
    public function read();

    /**
     * @return mixed
     */
    public function delete();
}
