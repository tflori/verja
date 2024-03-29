<?php

namespace Verja\Test\Filter;

use Verja\Exception\FilterNotFound;
use Verja\Filter;
use Verja\Test\TestCase;
use Verja\Test\Examples\CustomFilter;

class FromStringTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Filter::resetNamespaces();
    }

    /** @dataProvider provideInvalidDefinitions
     * @param string $definition
     * @test */
    public function throwsWhenFilterSpecificationIsInvalid($definition)
    {
        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('is not a valid string for Verja\Parser::parseClassNameWithParameters');

        Filter::fromString($definition);
    }

    public function provideInvalidDefinitions()
    {
        return [
            [ ':something' ],
            [ '' ],
            [ ' ' ],
        ];
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
    public function throwsWhenFilterIsUnknown()
    {
        self::expectException(FilterNotFound::class);
        self::expectExceptionMessage('Filter \'UnknownFilter\' not found');

        Filter::fromString('unknownFilter');
    }

    /** @test */
    public function defineAdditionalNamespace()
    {
        Filter::registerNamespace(CustomFilter::class);

        /** @noinspection PhpUndefinedMethodInspection */
        $filter = Filter::unknown();

        self::assertInstanceOf(CustomFilter\Unknown::class, $filter);
    }

    /** @test */
    public function lastInFirstOut()
    {
        Filter::registerNamespace(CustomFilter::class);

        $filter = Filter::fromString('trim');

        self::assertInstanceOf(CustomFilter\Trim::class, $filter);
    }

    /** @test */
    public function throwsWhenFilterIsNotAFilter()
    {
        Filter::registerNamespace(CustomFilter::class);

        self::expectException(FilterNotFound::class);

        Filter::fromString('noFilter');
    }
}
