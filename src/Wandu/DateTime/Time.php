<?php
namespace Wandu\DateTime;

use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use InvalidArgumentException;
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
        return static::fromTimes($carbon->hour, $carbon->minute, $carbon->second, $carbon->timezone);
    }

    /**
     * @param string $text
     * @param \DateTimeZone|string|int $timezone
     * @return \Wandu\DateTime\Time
     */
    public static function fromText(string $text, $timezone = null)
    {
        $split = explode(':', $text);
        return static::fromTimes(
            (int)($split[0] ?? 0),
            (int)($split[1] ?? 0),
            (int)($split[2] ?? 0),
            $timezone
        );
    }

    /**
     * @param int $hours
     * @param int $minutes
     * @param int $seconds
     * @param \DateTimeZone|string|int $timezone
     * @return \Wandu\DateTime\Time
     */
    public static function fromTimes($hours, $minutes = 0, $seconds = 0, $timezone = null)
    {
        return new Time($hours * 3600 + $minutes * 60 + $seconds, $timezone);
    }

    /** @var int */
    protected $timestamp;
    
    /** @var \DateTimeZone */
    protected $timezone;

    /**
     * @param int $timestamp
     * @param \DateTimeZone|string|int $timezone
     */
    public function __construct(int $timestamp, $timezone = null)
    {
        $this->setTimestamp($timestamp);
        $this->timezone = static::safeCreateDateTimeZone($timezone);
    }

    /**
     * @param int $timestamp
     * @return static
     */
    public function setTimestamp($timestamp)
    {
        while ($timestamp < 0) { // 음수처리
            $timestamp += 86400;
        }
        $this->timestamp = $timestamp % 86400;
        return $this;
    }

    /**
     * @param \DateTimeZone|string|int $timezone
     */
    public function setTimezone($timezone)
    {
        $now = new DateTime();

        // get offset
        $timezone = static::safeCreateDateTimeZone($timezone);
        $offset = $timezone->getOffset($now) - $this->timezone->getOffset($now);
        
        $this->timezone = $timezone;
        $this->setTimestamp($this->timestamp + $offset);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%02d:%02d:%02d', $this->hours(), $this->minutes(), $this->seconds());
    }

    /**
     * @return int
     */
    public function seconds()
    {
        return (int)($this->timestamp % 60);
    }

    /**
     * @return int
     */
    public function minutes()
    {
        return (int)(floor($this->timestamp / 60) % 60);
    }

    /**
     * @return int
     */
    public function hours()
    {
        return (int) floor($this->timestamp / 3600);
    }

    /**
     * @ref https://github.com/briannesbitt/Carbon/blob/master/src/Carbon/Carbon.php#L228
     * 
     * @param \DateTimeZone|string|int $object
     * @return \DateTimeZone
     */
    protected static function safeCreateDateTimeZone($object)
    {
        if ($object === null) {
            return new DateTimeZone(date_default_timezone_get());
        }
        if ($object instanceof DateTimeZone) {
            return $object;
        }

        if (is_numeric($object)) {
            $tzName = timezone_name_from_abbr(null, $object * 3600, true);
            if ($tzName === false) {
                throw new InvalidArgumentException('Unknown or bad timezone ('.$object.')');
            }
            $object = $tzName;
        }

        $tz = @timezone_open((string) $object);
        if ($tz === false) {
            throw new InvalidArgumentException('Unknown or bad timezone ('.$object.')');
        }
        return $tz;
    }
}
