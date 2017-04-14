<?php
namespace Wandu\Caster\Caster;

use Carbon\Carbon;
use Wandu\Caster\CasterInterface;

class CarbonCaster implements CasterInterface
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
        return new Carbon($value, $this->timezone);
    }
}
