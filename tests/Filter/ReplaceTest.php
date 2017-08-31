<?php

namespace Verja\Test\Filter;

use PHPUnit\Framework\TestCase;
use Verja\Filter\Replace;

class ReplaceTest extends TestCase
{
    /** @test */
    public function replacesSearchByReplace()
    {
        $filter = new Replace('a', 'b');

        $result = $filter->filter('a');

        self::assertSame('b', $result);
    }

    /** @test */
    public function parametersCanBeArray()
    {
        $filter = new Replace(['a', 'b'], ['x', 'y']);

        $result = $filter->filter('ab');

        self::assertSame('xy', $result);
    }
}
