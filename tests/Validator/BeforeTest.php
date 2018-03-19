<?php

namespace Verja\Test\Validator;

use Carbon\Carbon;
use Verja\Error;
use Verja\Test\TestCase;
use Verja\Validator\Before;

class BeforeTest extends TestCase
{
    /** @test */
    public function requiresDateTimeParameter()
    {
        if (PHP_VERSION_ID < 70100) {
            self::expectException(Warning::class);
        } else {
            // new exception since php 7.1
            self::expectException(\ArgumentCountError::class);
        }

        /** @noinspection PhpParamsInspection */
        new Before();
    }

    /** @dataProvider provideDateTimeObjects
     * @param $value
     * @param $before
     * @param $valid
     * @test */
    public function validatesDateTimeObjects($value, $before, $valid)
    {
        $validator = new Before($before);

        $result = $validator->validate($value);

        self::assertSame($valid, $result);
    }

    /** @test */
    public function allowsStringInConstructor()
    {
        $validator = new Before('now');

        $result = $validator->validate(new \DateTime('-5 seconds'));

        self::assertTrue($result);
    }

    /** @test */
    public function allowsStringForValidation()
    {
        $validator = new Before('now');

        $result = $validator->validate('-5 seconds');

        self::assertTrue($result);
    }

    /** @test */
    public function throwsWhenDateTimeHasWrongType()
    {
        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('$dateTime has to be');

        new Before(42);
    }

    /** @test */
    public function throwsWhenStringIsNotAcceptedByDateTime()
    {
        self::expectException(\Exception::class);
        self::expectExceptionMessage('Failed to parse time string');

        new Before('I have no idea');
    }

    /** @test */
    public function validateReturnsFalseForInvalidTimeString()
    {
        $now = new \DateTime();
        $validator = new Before($now);

        $result = $validator->validate('i have no idea');

        self::assertFalse($result);
        self::assertEquals(new Error(
            'NO_DATE',
            'i have no idea',
            'value should be a valid date',
            ['dateTime' => $now]
        ), $validator->getError());
    }

    /** @test */
    public function otherTypesAreInvalid()
    {
        $now = new \DateTime();
        $validator = new Before($now);

        $result = $validator->validate(42);

        self::assertFalse($result);
        self::assertEquals(new Error(
            'NO_DATE',
            42,
            'value should be a valid date',
            ['dateTime' => $now]
        ), $validator->getError());
    }

    /** @test */
    public function storesAnError()
    {
        $now = new \DateTime();
        $validator = new Before($now);

        $validator->validate('+1 hour');

        self::assertEquals(new Error(
            'NOT_BEFORE',
            '+1 hour',
            sprintf('value should be before %s', $now->format('c')),
            ['dateTime' => $now]
        ), $validator->getError());
    }

    public function provideDateTimeObjects()
    {
        $now = Carbon::now();

        $data = [
            [new \DateTime('-1 hour'), new \DateTime(), true],
            [new \DateTime('+1 hour'), new \DateTime(), false],
            ['now', 'now', false],
            ['2016-01-21', '1984-01-21', false],
            ['2016-01-21', 'now', true],
            [$now->format('c'), $now->format('c'), true], // without milliseconds
            [
                \DateTime::createFromFormat('U.u', microtime(true)),
                \DateTime::createFromFormat('U.u', microtime(true)+0.002), // make sure there are 2 milliseconds more
                true
            ], // with milliseconds
        ];

        return $data;
    }
}
