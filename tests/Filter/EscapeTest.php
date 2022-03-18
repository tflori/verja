<?php

namespace Verja\Test\Filter;

use Verja\Filter\Escape;
use Verja\Test\TestCase;

class EscapeTest extends TestCase
{
    /** @dataProvider provideStringsWithSpecialChars
     * @param $value
     * @param $expected
     * @test */
    public function replacesSpecialCharacters($value, $expected)
    {
        $filter = new Escape();

        $filtered = $filter->filter($value);

        self::assertSame($expected, $filtered);
    }

    /** @dataProvider provideStringsWithEscapedChars
     * @param $value
     * @test */
    public function doesNotReplaceEscapedChars($value)
    {
        $filter = new Escape();

        $filtered = $filter->filter($value);

        self::assertSame($value, $filtered);
    }

    /** @test */
    public function doesNotConvertNonStrings()
    {
        $filter = new Escape();

        $filtered = $filter->filter(42);

        self::assertIsInt($filtered);
    }

    /** @dataProvider provideStringsWithHtmlEntities
     * @param $value
     * @test */
    public function doesNotConvertHtmlEntities($value)
    {
        $filter = new Escape();

        $filtered = $filter->filter($value);

        self::assertSame($value, $filtered);
    }

    /** @dataProvider provideStringsWithHtmlEntities
     * @param $value
     * @param $expected
     * @test */
    public function convertsHtmlEntities($value, $expected)
    {
        $filter = new Escape(false, false);

        $filtered = $filter->filter($value);

        self::assertSame($expected, $filtered);
    }

    /** @dataProvider provideStringsWithEscapedChars
     * @param $value
     * @param $expected
     * @test */
    public function escapesAlreadyEscapedEntities($value, $expected)
    {
        $filter = new Escape(true);

        $filtered = $filter->filter($value);

        self::assertSame($expected, $filtered);
    }

    public function provideStringsWithSpecialChars()
    {
        return [
            ['a double quote "', 'a double quote &quot;'],
            ['a greater than >', 'a greater than &gt;'],
            ['a less than <', 'a less than &lt;'],
            ['an ampersand &', 'an ampersand &amp;'],
        ];
    }

    public function provideStringsWithEscapedChars()
    {
        return [
            ['a double quote &quot;', 'a double quote &amp;quot;'],
            ['a german umlaut &auml;', 'a german umlaut &amp;auml;'],
            ['a times sign &times;', 'a times sign &amp;times;'],
        ];
    }

    public function provideStringsWithHtmlEntities()
    {
        return [
            ['a times sign ×', 'a times sign &times;'],
            ['a bullet sign •', 'a bullet sign &bull;'],
            ['a euro sign €', 'a euro sign &euro;'],
        ];
    }
}
