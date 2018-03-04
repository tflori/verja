<?php

namespace Verja\Test\Validator;

use Verja\Error;
use Verja\Test\TestCase;
use Verja\Validator\Between;

class BetweenTest extends TestCase
{
    /** @test */
    public function isFalseForNonNumericValues()
    {
        $validator = new Between();

        $validator->validate('a string');

        self::assertEquals(new Error('NO_NUMBER', 'a string', 'value should be a number'), $validator->getError());
    }

    /** @test */
    public function isFalseWhenLessThanMin()
    {
        $validator = new Between(42);

        $validator->validate(23);

        self::assertEquals(new Error(
            'TOO_SMALL',
            23,
            'value should be at least 42',
            [
                'min' => 42,
                'max' => null,
            ]
        ), $validator->getError());
    }

    /** @test */
    public function nullDoesNotGetValidated()
    {
        $validator = new Between(null, null);

        $result = $validator->validate(PHP_INT_MIN) && $validator->validate(PHP_INT_MAX);

        self::assertTrue($result);
    }

    /** @test */
    public function isFalseWhenGreaterThanMax()
    {
        $validator = new Between(null, 42);

        $validator->validate(666);

        self::assertEquals(new Error(
            'TOO_BIG',
            666,
            'value should be maximal 42',
            [
                'min' => null,
                'max' => 42,
            ]
        ), $validator->getError());
    }
}
