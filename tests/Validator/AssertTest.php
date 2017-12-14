<?php

namespace Verja\Test\Validator;

use Verja\Exception\InvalidValue;
use Verja\Test\TestCase;
use Verja\Validator;

class AssertTest extends TestCase
{
    /** @test */
    public function throwsWhenInvalid()
    {
        self::expectException(InvalidValue::class);
        self::expectExceptionMessage('Assertion failed: value should not be empty');

        Validator::assert('notEmpty', '');
    }

    /** @test */
    public function throwsWithCommonErrorMessage()
    {
        self::expectException(InvalidValue::class);
        self::expectExceptionMessage('Failed asserting that "the value" is Callback');

        Validator::assert(function () {
            return false;
        }, 'the value');
    }

    /** @test */
    public function returnsTheValue()
    {
        $result = Validator::assert('notEmpty', 'the value');

        self::assertSame('the value', $result);
    }
}
