<?php

namespace Verja\Test\Gate;

use Verja\Field;
use Verja\Filter\Trim;
use Verja\Test\Examples;
use Verja\Test\TestCase;
use Verja\Validator\Contains;

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

    /** @test */
    public function acceptsArraysAsFieldDefinition()
    {
        $gate = new Examples\Gate();

        $gate->accepts([
            'username' => ['trim', 'notEmpty', 'strLen:3:20'],
            'password' => ['notEmpty', 'strLen:8'],
        ]);

        self::assertEquals([
            'username' => new Field(['trim', 'notEmpty', 'strLen:3:20']),
            'password' => new Field(['notEmpty', 'strLen:8']),
        ], $gate->getFields());
    }

    /** @test */
    public function acceptsStringAsFieldDefinition()
    {
        $gate = new Examples\Gate();

        $gate->accept('privacy-policy', 'notEmpty');

        self::assertEquals([
            'privacy-policy' => new Field(['notEmpty']),
        ], $gate->getFields());
    }

    /** @test */
    public function acceptsValidatorAsFieldDefinition()
    {
        $gate = new Examples\Gate();

        $gate->accept('email', new Contains('@'));

        self::assertEquals([
            'email' => new Field(['contains:@']),
        ], $gate->getFields());
    }

    /** @test */
    public function acceptsFiltersAsFieldDefinition()
    {
        $gate = new Examples\Gate();

        $gate->accept('comment', new Trim());

        self::assertEquals([
            'comment' => new Field(['trim'])
        ], $gate->getFields());
    }
}
