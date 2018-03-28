<?php

namespace Verja\Test\Filter;

use Verja\Filter\ConvertCase;
use Verja\Test\TestCase;

class ConvertCaseTest extends TestCase
{
    /** @dataProvider provideCaseConverts
     * @param $value
     * @param $mode
     * @param $expected
     * @test */
    public function changesCase($value, $mode, $expected)
    {
        $filter = new ConvertCase($mode);

        $result = $filter->filter($value);

        self::assertSame($expected, $result);
    }

    public function provideCaseConverts()
    {
        return [
            ['foo', MB_CASE_UPPER, 'FOO'],
            ['FOO', MB_CASE_LOWER, 'foo'],
            ['foo bar', MB_CASE_TITLE, 'Foo Bar'],
            ['foo', 'upper', 'FOO'],
            ['FOO', 'lower', 'foo'],
            ['foo bar', 'title', 'Foo Bar'],
            ['FOO BAR', 'title', 'Foo Bar'],
            ['ÄАОШУ', 'lower', 'äаошу'],
            ['фоо бап', 'title', 'Фоо Бап']
        ];
    }
}
