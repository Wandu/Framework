<?php
namespace Wandu\Q\Adapter;

class ArrayAdapterTest extends TestCase
{
    public function setUp()
    {
        $this->queue = new ArrayAdapter();
    }
}
