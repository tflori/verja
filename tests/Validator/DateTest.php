<?php

namespace Verja\Test\Validator;

use Verja\Error;
use Verja\Test\TestCase;
use Verja\Validator\Date;

class DateTest extends TestCase
{
    /** @dataProvider provideValidDates
     * @param $date
     * @test */
    public function anyStringConvertableToDateTimeIsValid($date)
    {
        $validator = new Date();

        $result = $validator->validate($date);

        self::assertTrue($result);
    }

    /** @test */
    public function storesAnErrorWhenInvalid()
    {
        $validator = new Date();

        $validator->validate('foo bar');

        self::assertEquals(new Error('NO_DATE', 'foo bar', 'value should be a valid date'), $validator->getError());
    }

    /** @dataProvider provideFormattedDates
     * @param $format
     * @param $strict
     * @param $date
     * @param $valid
     * @test */
    public function restrictsToFormat($format, $strict, $date, $valid)
    {
        $validator = new Date($format, $strict);

        $result = $validator->validate($date);

        self::assertSame($valid, $result);
    }

    /** @test */
    public function storesAnErrorWithFormat()
    {
        $validator = new Date('Y-m-d', true);

        $validator->validate('2016-21-01');

        self::assertEquals(
            new Error(
                'NO_FORMATTED_DATE',
                '2016-21-01',
                'value is not a valid date in format Y-m-d',
                [ 'format' => 'Y-m-d', 'strict' => true ]
            ),
            $validator->getError()
        );
    }

    /** @test */
    public function providesAnInverseError()
    {
        $validator = new Date();

        $error = $validator->getInverseError('next month');

        self::assertEquals(new Error('IS_DATE', 'next month', 'value should not be a valid date'), $error);
    }

    /** @test */
    public function providesAnInverseErrorWithFormat()
    {
        $validator = new Date('Y-m-d');

        $error = $validator->getInverseError('2016-21-01');

        self::assertEquals(
            new Error(
                'IS_FORMATTED_DATE',
                '2016-21-01',
                'value should not be valid date in format Y-m-d',
                [ 'format' => 'Y-m-d', 'strict' => false ]
            ),
            $error
        );
    }

    public function provideValidDates()
    {
        return [
            ['now'],
            ['-3 hours'],
            ['2010-12-31 23:59:59'],
            ['30-June 2008'],
            ['next week'],
        ];
    }

    public function provideFormattedDates()
    {
        return [
            ['Y-m-d', true, '2016-01-21', true],
            ['Y-m-d H:i:s', true, '1970-01-01 00:00:00', true],
            ['Y-m-d', true, '2016-21-01', false],
            ['Y-m-d', false, '2016-21-01', true],
            ['Y-m-d', true, '21.01.2016', false],
            ['dS of F Y', false, '21st of January 2016', true],
            ['dS of F Y', true, '21st of January 2016', false],
            ['Y-m-d H:i:s oNw', false, '2016-01-21 12:00:00 oNw', true],
            ['Y-m-d H:i:s oNw', true, '2016-01-21 12:00:00 oNw', false],
        ];
    }
}
