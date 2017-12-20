<?php

namespace Verja\Test\Filter;

use Verja\Exception\InvalidValue;
use Verja\Filter\Boolean;
use Verja\Test\TestCase;

class BooleanTest extends TestCase
{
    /** @dataProvider provideBooleanValues
     * @test */
    public function returnsBooleanWhenPossible($value, $expected)
    {
        $filter = new Boolean();

        $result = $filter->filter($value);

        self::assertSame($expected, $result);
    }

    public function provideBooleanValues()
    {
        return [
            [true, true],
            [false, false],
            [1, true],
            [0, false],
            [-1, true],
            ['1', true],
            ['0', false],
            ['true', true],
            ['false', false],
            ['t', true],
            ['f', false],
        ];
    }

    /** @test */
    public function throwsInvalidValueOtherwise()
    {
        $filter = new Boolean();

        self::expectException(InvalidValue::class);
        self::expectExceptionMessage('Assertion failed: value should be a boolean');

        $filter->filter(0.1);
    }
}
