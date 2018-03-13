<?php

namespace Verja\Test\Validator;

use Verja\Error;
use Verja\Test\TestCase;
use Verja\Validator\After;

class AfterTest extends TestCase
{
    /** @test */
    public function requiresDateTimeParameter()
    {
        self::expectException('ArgumentCountError');

        new After();
    }

    /** @dataProvider provideDateTimeObjects
     * @param $value
     * @param $before
     * @param $valid
     * @test */
    public function validatesDateTimeObjects($value, $before, $valid)
    {
        $validator = new After($before);

        $result = $validator->validate($value);

        self::assertSame($valid, $result);
    }

    /** @test */
    public function allowsStringInConstructor()
    {
        $validator = new After('now');

        $result = $validator->validate(new \DateTime());

        self::assertTrue($result);
    }

    /** @test */
    public function allowsStringForValidation()
    {
        $validator = new After('now');

        $result = $validator->validate('now');

        self::assertTrue($result);
    }

    /** @test */
    public function throwsWhenDateTimeHasWrongType()
    {
        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('$dateTime has to be');

        new After(42);
    }

    /** @test */
    public function throwsWhenStringIsNotAcceptedByDateTime()
    {
        self::expectException(\Exception::class);
        self::expectExceptionMessage('Failed to parse time string');

        new After('I have no idea');
    }

    /** @test */
    public function validateReturnsFalseForInvalidTimeString()
    {
        $now = new \DateTime();
        $validator = new After($now);

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
        $validator = new After($now);

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
        $validator = new After($now);

        $validator->validate('-1 hour');

        self::assertEquals(new Error(
            'NOT_AFTER',
            '-1 hour',
            sprintf('value should be after %s', $now->format('c')),
            ['dateTime' => $now]
        ), $validator->getError());
    }

    public function provideDateTimeObjects()
    {
        return [
            [new \DateTime('+1 hour'), new \DateTime(), true],
            [new \DateTime('-1 hour'), new \DateTime(), false],
            [new \DateTime(), new \DateTime(), false],
            ['now', 'now', true],
            ['2016-01-21', '1984-01-21', true],
            ['2016-01-21', 'now', false],
        ];
    }
}
