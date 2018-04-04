<?php

namespace Verja\Test\Filter;

use Verja\Filter\PregReplace;
use Verja\Test\TestCase;

class PregReplaceTest extends TestCase
{
    /** @dataProvider provideTestCases
     * @param $pattern
     * @param $replace
     * @param $value
     * @param $expected
     * @test */
    public function replacesRegularExpressions($pattern, $replace, $value, $expected)
    {
        $filter = new PregReplace($pattern, $replace);

        $result = $filter->filter($value);

        self::assertSame($expected, $result);
    }

    /** @test */
    public function leavesNonStrings()
    {
        $filter = new PregReplace('/foo/', 'bar');

        $result = $filter->filter(new \DateTime());

        self::assertInstanceOf(\DateTime::class, $result);
    }

    /** @test */
    public function allowsReplaceToBeACallback()
    {
        $filter = new PregReplace('~(?:[^A-Za-z-]|^)([A-Za-z-]+)([^A-Za-z-]|$)~', function ($matches) {
            list($match, $word, $del) = $matches;

            if ($word == 'foo') {
                return $del;
            }

            return $match;
        });

        $result = $filter->filter('foo: you should not use foo in this text');

        self::assertSame(': you should not use in this text', $result);
    }

    /** @test */
    public function allowsPatternToBeAnArray()
    {
        $filter = new PregReplace(['/(^|\W)foo/', '/(^|\W)bar/'], '');

        $result = $filter->filter('foo and bar is not allowed here');

        self::assertSame(' and is not allowed here', $result);
    }

    public function provideTestCases()
    {
        return [
            ['~foo~', 'BAR', 'foo but not FOO', 'BAR but not FOO'],
            ['~e~', 'o', 'fee', 'foo'],
            ['/(^\d+) (.*) (\d+$)/', '$3 $2 $1', '23 not 42', '42 not 23'],
        ];
    }
}
