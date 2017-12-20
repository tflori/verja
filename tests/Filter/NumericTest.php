<?php

namespace Verja\Test\Filter;

use Verja\Exception\InvalidValue;
use Verja\Filter\Numeric;
use Verja\Test\TestCase;

class NumericTest extends TestCase
{
    /** @dataProvider provideNumerics
     * @test */
    public function returnsNumeric($value, $decimalPoint, $expected)
    {
        $filter = new Numeric($decimalPoint);

        $result = $filter->filter($value);

        self::assertSame($expected, $result);
    }

    public function provideNumerics()
    {
        return [
            [23.1, '#', 23.1],
            [42, '-', 42],
            ['0.1', '.', 0.1],
            ['4#2E1', '#', 42],
        ];
    }

    /** @test */
    public function throwsInvalidValueOtherwise()
    {
        $filter = new Numeric();

        self::expectException(InvalidValue::class);
        self::expectExceptionMessage('Assertion failed: value should be numeric');

        $filter->filter('23a');
    }
}
