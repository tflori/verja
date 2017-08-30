<?php

namespace Verja\Test\Filter;

use PHPUnit\Framework\TestCase;
use Verja\Filter;

class FromStringTest extends TestCase
{
    /** @test */
    public function returnsAnEmptyArray()
    {
        $filters = Filter::fromString('');

        self::assertSame([], $filters);
    }

    /** @test */
    public function returnsOneTrimFilterWihtoutParameters()
    {
        $filters = Filter::fromString('trim');

        self::assertEquals([new Filter\Trim()], $filters);
    }

    /** @test */
    public function returnsTwoTrimFiltersWithParameters()
    {
        $filters = Filter::fromString(' trim:" " | trim:$ ');

        self::assertEquals([
            new Filter\Trim(' '),
            new Filter\Trim('$'),
        ], $filters);
    }
}
