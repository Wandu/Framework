<?php
namespace Wandu\Validator\Testers;

use Wandu\Validator\Contracts\Tester;

class BeforeTester implements Tester
{
    /** @var string */
    protected $criteria;

    public function __construct($criteria)
    {
        $this->criteria = $criteria;
    }

    /**
     * {@inheritdoc}
     */
    public function test($data, $origin = null, array $keys = []): bool
    {
        return strtotime($this->criteria) >= $data;
    }
}
