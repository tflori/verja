<?php

namespace Verja\Test\Filter;

use Verja\Filter\Trim;
use Verja\Test\TestCase;

class CommonTest extends TestCase
{
    /** @test */
    public function filterIsInvokable()
    {
        $filter = new Trim();

        $result = $filter(' foo bar ');

        self::assertSame('foo bar', $result);
    }
}
