<?php

namespace Verja\Test\Filter;

use Carbon\Carbon;
use Verja\Exception\InvalidValue;
use Verja\Filter\DateTime;
use Verja\Test\TestCase;

class DateTimeTest extends TestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        date_default_timezone_set('Europe/Berlin');
    }

    /** @test */
    public function illegalTimeZoneStringThrows()
    {
        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('Unknown or bad timezone (Pangaea/Metropolis)');

        new DateTime('Pangaea/Metropolis', null, false);
    }

    /** @test */
    public function invalidValueThrows()
    {
        $filter = new DateTime();

        self::expectException(InvalidValue::class);
        self::expectExceptionMessage('value should be a valid date');

        $filter->filter('I have no idea');
    }

    /** @test */
    public function wrongFormatThrows()
    {
        $filter = new DateTime(null, 'm/d/Y');

        self::expectException(InvalidValue::class);
        self::expectExceptionMessage('value should be a valid date in format m/d/Y');

        $filter->filter('2016-01-21');
    }

    /** @test */
    public function acceptedFormatThrowsInStrict()
    {
        $filter = new DateTime(null, 'Y-m-d', true);

        self::expectException(InvalidValue::class);
        self::expectExceptionMessage('value should be a valid date in format Y-m-d');

        $filter->filter('2016-21-01');
    }

    /** @dataProvider provideValidDates
     * @param $format
     * @param $timeZone
     * @param $value
     * @param $expected
     * @test */
    public function returnsDateTimeObject($format, $timeZone, $value, $expected)
    {
        $filter = new DateTime($timeZone, $format, false);

        /** @var \DateTime $result */
        $result = $filter->filter($value);

        self::assertInstanceOf(\DateTime::class, $result);
        self::assertSame(
            $expected instanceof Carbon ? $expected->getTimestamp() : Carbon::now()->getTimestamp() + $expected,
            $result->getTimestamp()
        );
    }

    public function provideValidDates()
    {
        date_default_timezone_set('Europe/Berlin');
        $now = Carbon::now();
        $utc = (clone $now)->setTimezone('UTC');
        $colombo = (clone $utc)->setTimezone('Asia/Colombo');

        return [
            ['Y-m-d H:i:s', null, $now->format('Y-m-d H:i:s'), $now],
            ['Y-m-d H:i:s', $utc->getTimezone(), $utc->format('Y-m-d H:i:s'), $now],
            ['Y-m-d H:i:s', $colombo->getTimezone(), $colombo->format('Y-m-d H:i:s'), $now],
            ['Y-m-d H:i:s', $now->format('e'), $now->format('Y-m-d H:i:s'), $now],
            [null, null, '+2 Hours', 7200],
            [null, $colombo->getTimezone(), '+2 Hours', 7200],
            [
                'Y-m-d H:i:s',
                $colombo->getTimezone(),
                $utc->format('Y-m-d H:i:s'),
                (clone $now)->addSeconds(-$colombo->getOffset())
            ],
        ];
    }
}
