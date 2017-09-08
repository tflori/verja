<?php

namespace Verja\Test\Filter;

use Verja\Filter\Callback;
use Verja\Test\TestCase;

class CallbackTest extends TestCase
{
    /** @test */
    public function callsCallbackToFilter()
    {
        $calls = [];
        $filter = new Callback(function () use (&$calls) {
            $calls[] = func_get_args();
            return true;
        });

        $filter->filter('value');

        self::assertSame([
            [ 'value', [] ]
        ], $calls);
    }
}
