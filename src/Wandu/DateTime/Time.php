<?php
namespace Wandu\DateTime;

use Carbon\Carbon;
use RuntimeException;

class Time
{
    /**
     * @param \Carbon\Carbon $carbon
     * @return \Wandu\DateTime\Time
     */
    public static function fromCarbon(Carbon $carbon)
    {
        if (!class_exists(Carbon::class)) {
            throw new RuntimeException('Unable to fromCarbon. the Carbon is not installed.');
        }
        return static::fromTimes($carbon->hour, $carbon->minute, $carbon->second);
    }

    /**
     * @param int $hours
     * @param int $minutes
     * @param int $seconds
     * @return \Wandu\DateTime\Time
     */
    public static function fromTimes($hours, $minutes = 0, $seconds = 0)
    {
        return new Time($hours * 3600 + $minutes * 60 + $seconds);
    }

    /** @var int */
    protected $timestamp;

    /**
     * @param int $timestamp
     */
    public function __construct($timestamp)
    {
        $this->setTimestamp($timestamp);
    }

    /**
     * @param int $timestamp
     * @return static
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp % 86400;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%02d:%02d:%02d', $this->getHours(), $this->getMinutes(), $this->getSeconds());
    }

    /**
     * @return int
     */
    public function getSeconds()
    {
        return $this->timestamp % 60;
    }

    /**
     * @return int
     */
    public function getMinutes()
    {
        return floor($this->timestamp / 60) % 60;
    }

    /**
     * @return float
     */
    public function getHours()
    {
        return floor($this->timestamp / 3600);
    }
}
