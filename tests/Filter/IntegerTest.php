<?php

namespace Verja\Test\Filter;

use Verja\Exception\InvalidValue;
use Verja\Filter\Integer;
use Verja\Test\TestCase;

class IntegerTest extends TestCase
{
    /** @dataProvider provideIntegers
     * @test */
    public function returnsIntegers($value, $expected)
    {
        $filter = new Integer();

        $result = $filter->filter($value);

        self::assertSame($expected, $result);
    }

    public function provideIntegers()
    {
        return [
            [1, 1],
            [-23, -23],
            ['42', 42],
            ['9E3', 9000],
            ['5.5E3', 5500],
            ['-2.3E1', -23],
            ['420E-1', 42],
        ];
    }

    /** @test */
    public function throwsInvalidValueOtherwise()
    {
        $filter = new Integer();

        self::expectException(InvalidValue::class);
        self::expectExceptionMessage('Assertion failed: value should be an integer');

        $filter->filter('1E-1');
    }
}
