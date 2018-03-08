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
        self::assertSame($expected, $result->getTimestamp());
    }

    public function provideValidDates()
    {
        date_default_timezone_set('Europe/Berlin');
        $now = Carbon::now();
        $utcNow = (clone $now)->setTimezone('UTC');
        $colomboNow = (clone $utcNow)->setTimezone('Asia/Colombo');

        return [
            ['Y-m-d H:i:s', null, $now->format('Y-m-d H:i:s'), $now->getTimestamp()],
            ['Y-m-d H:i:s', $utcNow->getTimezone(), $utcNow->format('Y-m-d H:i:s'), $now->getTimestamp()],
            ['Y-m-d H:i:s', $colomboNow->getTimezone(), $colomboNow->format('Y-m-d H:i:s'), $now->getTimestamp()],
            ['Y-m-d H:i:s', $now->format('e'), $now->format('Y-m-d H:i:s'), $now->getTimestamp()],
            [null, null, '+2 Hours', Carbon::now()->getTimestamp()+7200],
            [null, $colomboNow->getTimezone(), '+2 Hours', Carbon::now()->getTimestamp()+7200],
            [
                null,
                $colomboNow->getTimezone(),
                $utcNow->format('Y-m-d H:i:s'),
                $now->getTimestamp() - $colomboNow->getOffset()
            ],
        ];
    }
}
