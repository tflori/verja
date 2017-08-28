<?php

namespace Verja\Test\Gate;

use PHPUnit\Framework\TestCase;
use Verja\Field;
use Verja\Test\Examples;

class AcceptTest extends TestCase
{
    /** @test */
    public function acceptsNothingByDefault()
    {
        $gate = new Examples\Gate();

        self::assertSame([], $gate->getFields());
    }

    /** @test */
    public function acceptsAdded()
    {
        $gate = new Examples\Gate();

        $gate->accept('username');

        self::assertEquals(['username' => new Field()], $gate->getFields());
    }

    /** @test */
    public function acceptsAllAdded()
    {
        $gate = new Examples\Gate();

        $gate->accepts(['username', 'password']);

        self::assertEquals([
            'username' => new Field(),
            'password' => new Field(),
        ], $gate->getFields());
    }

    /** @test */
    public function acceptsWithPredefinedField()
    {
        $gate = new Examples\Gate();
        $fieldUsername = new Field();
        $fieldPassword = new Field();

        $gate->accepts([
            'username' => $fieldUsername,
            'password' => $fieldPassword,
        ]);

        self::assertSame([
            'username' => $fieldUsername,
            'password' => $fieldPassword,
        ], $gate->getFields());
    }
}
