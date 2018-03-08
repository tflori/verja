<?php

namespace Verja\Filter;

use Verja\Filter;
use Verja\Gate;
use Verja\Validator\DateTime as DateTimeValidator;

class DateTime extends Filter
{
    /** @var string */
    protected $format;

    /** @var bool */
    protected $strict;

    /** @var \DateTimeZone */
    protected $timeZone;

    /**
     * DateTime constructor.
     *
     * @param string|\DateTimeZone|int $timeZone
     * @param string                   $format
     * @param bool                     $strict
     */
    public function __construct($timeZone = null, string $format = null, bool $strict = false)
    {
        $this->format = $format;
        $this->strict = $strict;
        $this->timeZone = self::safeCreateDateTimeZone($timeZone);
    }

    /**
     * Filter $value
     *
     * @param mixed $value
     * @param array $context
     * @return mixed
     */
    public function filter($value, array $context = [])
    {
        Gate::assert(new DateTimeValidator($this->format, $this->strict), $value);

        return empty($this->format) ? new \DateTime($value, $this->timeZone) :
            \DateTime::createFromFormat($this->format, $value, $this->timeZone);
    }

    /**
     * Creates a DateTimeZone from a string, DateTimeZone or integer offset.
     *
     * @param \DateTimeZone|string|int|null $object
     *
     * @throws \InvalidArgumentException
     *
     * @return \DateTimeZone
     */
    protected static function safeCreateDateTimeZone($object)
    {
        if ($object === null) {
            return new \DateTimeZone(date_default_timezone_get());
        }

        if ($object instanceof \DateTimeZone) {
            return $object;
        }

        $tz = @timezone_open((string) $object);

        if ($tz === false) {
            throw new \InvalidArgumentException('Unknown or bad timezone ('.$object.')');
        }

        return $tz;
    }
}
