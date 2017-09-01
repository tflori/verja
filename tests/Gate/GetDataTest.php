<?php

namespace Verja\Test\Gate;

use Verja\Gate;
use Verja\Test\TestCase;

class GetDataTest extends TestCase
{
    /** @test */
    public function returnsNoValues()
    {
        $gate = new Gate([ 'key' => 'value' ]);

        $data = $gate->getData();

        self::assertSame([], $data);
    }

    /** @test */
    public function returnsAccepted()
    {
        $gate = new Gate([ 'username' => 'john', 'password' => 'abc123' ]);
        $gate->accept('username');

        $data = $gate->getData();

        self::assertSame([ 'username' => 'john' ], $data);
    }
}
