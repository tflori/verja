<?php

namespace Verja\Test\Filter;

use Verja\Filter;
use Verja\Test\TestCase;

class FromStringTest extends TestCase
{
    /** @test */
    public function returnsNullForEmptyString()
    {
        $filter = Filter::fromString('');

        self::assertNull($filter);
    }

    /** @test */
    public function parametersAreNotRequired()
    {
        $filter = Filter::fromString('trim');

        self::assertEquals(new Filter\Trim(), $filter);
    }

    /** @test */
    public function parametersFollowColon()
    {
        $filter = Filter::fromString('trim:/$%');

        self::assertEquals(new Filter\Trim('/$%'), $filter);
    }

    /** @test */
    public function parametersCanHaveSpaces()
    {
        $filter = Filter::fromString('trim: ');

        self::assertEquals(new Filter\Trim(' '), $filter);
    }

    /** @test */
    public function multipleParametersAllowed()
    {
        $filter = Filter::fromString('replace:a:b');

        self::assertEquals(new Filter\Replace('a', 'b'), $filter);
    }

    /** @dataProvider provideFilterStringWithEmptyParameters
     * @param $definition
     * @param $expected
     * @test */
    public function parametersCanBeEmpty($definition, $expected)
    {
        $filter = Filter::fromString($definition);

        self::assertEquals($expected, $filter);
    }

    public function provideFilterStringWithEmptyParameters()
    {
        return [
            [ 'replace:a:""', new Filter\Replace('a', '') ],
            [ "replace:'':b", new Filter\Replace('', 'b') ],
            [ 'replace:0:1', new Filter\Replace('0', '1') ]
        ];
    }

    /** @test */
    public function parametersCanBeJsonArray()
    {
        $filter = Filter::fromString('replace:[ ["a", "b"], ["6", "7"] ]');

        self::assertEquals(new Filter\Replace(['a', 'b'], ['6', '7']), $filter);
    }

    /** @test */
    public function throwsWhenFilterSpecificationIsInvalid()
    {
        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('$str is not a valid string for Verja\Parser::parseClassNameWithParameters');

        Filter::fromString(':something');
    }

    /** @test */
    public function throwsWhenFilterIsUnknown()
    {
        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('Filter \'unknownFilter\' not found');

        Filter::fromString('unknownFilter');
    }
}
