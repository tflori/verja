<?php

namespace Verja\Test\Container;

use PHPUnit\Framework\TestCase;
use Verja\Container;

class MiscTest extends TestCase
{
    /** @test */
    public function hasNoDependencies()
    {
        $container = new Container();

        self::assertInstanceOf(Container::class, $container);
    }
}
