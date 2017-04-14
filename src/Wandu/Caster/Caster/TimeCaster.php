<?php
namespace Wandu\Caster\Caster;

use Wandu\Caster\CasterInterface;
use Wandu\DateTime\Time;

class TimeCaster implements CasterInterface
{
    /** @var string */
    protected $timezone;

    /**
     * @param string $timezone
     */
    public function __construct($timezone = null)
    {
        $this->timezone = $timezone;
    }

    /**
     * {@inheritdoc}
     */
    public function cast($value)
    {
        if (is_numeric($value)) return new Time($value, $this->timezone);
        return Time::fromText($value, $this->timezone);
    }
}
