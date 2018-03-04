<?php

namespace Verja\Test\Gate;

use Verja\Exception\InvalidValue;
use Verja\Test\TestCase;
use Verja\Gate;

class AssertTest extends TestCase
{
    /** @test */
    public function throwsWhenInvalid()
    {
        self::expectException(InvalidValue::class);
        self::expectExceptionMessage('Assertion failed: value should not be empty');

        Gate::assert('notEmpty', '');
    }

    /** @test */
    public function throwsWithCommonErrorMessage()
    {
        self::expectException(InvalidValue::class);
        self::expectExceptionMessage('Failed asserting that "the value" is valid (unknown error)');

        Gate::assert(function () {
            return false;
        }, 'the value');
    }

    /** @test */
    public function returnsTheValue()
    {
        $result = Gate::assert('notEmpty', 'the value');

        self::assertSame('the value', $result);
    }

    /** @test */
    public function exceptionListsAllErrors()
    {
        self::expectException(InvalidValue::class);
        self::expectExceptionMessage('Assertion failed: value should not be empty; value should be in array');

        Gate::assert(['notEmpty', 'inArray:a,b,c'], '');
    }
}
