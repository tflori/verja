<?php

namespace Verja\Test\Gate;

use PHPUnit\Framework\TestCase;
use Verja\Gate;
use Verja\Test\Examples;

class MiscTest extends TestCase
{
    /** @test */
    public function hasNoDependencies()
    {
        $container = new Gate();

        self::assertInstanceOf(Gate::class, $container);
    }

    /** @test */
    public function storesDataFromConstructor()
    {
        $container = new Examples\Gate([ 'key' => 'value']);

        $data = $container->getRawData();

        self::assertSame(['key' => 'value'], $data);
    }
}
