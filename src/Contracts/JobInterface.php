<?php
namespace Wandu\Q\Contracts;

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
