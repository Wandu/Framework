<?php
namespace Wandu\DateTime;

use Carbon\Carbon;
use DateTime;
use RuntimeException;

class Date
{
    /**
     * @param \Carbon\Carbon $carbon
     * @return \Wandu\DateTime\Date
     */
    public static function fromCarbon(Carbon $carbon)
    {
        if (!class_exists(Carbon::class)) {
            throw new RuntimeException('Unable to fromCarbon. the Carbon is not installed.');
        }
        return static::fromDates($carbon->year, $carbon->month, $carbon->day);
    }

    /**
     * @param string $text
     * @param \DateTimeZone|string|int $timezone
     * @return \Wandu\DateTime\Date
     */
    public static function fromText(string $text, $timezone = null)
    {
        return new Date($text, $timezone);
    }

    /**
     * @param int $year
     * @param int $month
     * @param int $day
     * @return \Wandu\DateTime\Date
     */
    public static function fromDates(int $year, int $month, int $day)
    {
        $datetime = new DateTime();
        $datetime->setDate($year, $month, $day);
        $datetime->setTime(0, 0, 0);
        return new Date($datetime);
    }

    /** @var \DateTime */
    protected $datetime;

    /**
     * @param string|\DateTime $time
     */
    public function __construct($time)
    {
        if ($time instanceof DateTime) {
            $this->datetime = $time;
        } else {
            $this->datetime = new DateTime($time);
        }
    }
    
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->datetime->format('Y-m-d');
    }

    /**
     * @return int
     */
    public function year(): int
    {
        return (int)($this->datetime->format('Y'));
    }

    /**
     * @return int
     */
    public function month(): int
    {
        return (int)($this->datetime->format('n'));
    }

    /**
     * @return int
     */
    public function day(): int
    {
        return (int)($this->datetime->format('j'));
    }
}
