<?php

namespace Verja\Test\Gate;

use Verja\Exception\InvalidValue;
use Verja\Field;
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

    /** @test */
    public function executesFilterOnField()
    {
        $gate = new Gate([ 'username' => 'john', 'password' => 'abc123' ]);
        $field = \Mockery::mock(Field::class)->makePartial();
        $gate->addField('username', $field);

        $field->shouldReceive('filter')->with('john', ['username' => 'john', 'password' => 'abc123'])
            ->once()->andReturn('john');

        $gate->getData();
    }

    /** @test> */
    public function executesValidateOnField()
    {
        $gate = new Gate([ 'username' => 'john@example', 'password' => 'abc123' ]);
        $field = \Mockery::mock(Field::class)->makePartial();
        $field->shouldReceive('filter')->andReturn('john');
        $gate->addField('username', $field);

        $field->shouldReceive('validate')->with('john', ['username' => 'john@example', 'password' => 'abc123'])
            ->once()->andReturn(true);

        $gate->getData();
    }

    /** @test */
    public function throwsWhenDataIsInvalid()
    {
        $gate = new Gate([ 'username' => 'john']);
        $field = \Mockery::mock(Field::class)->makePartial();
        $field->shouldReceive('validate')->andReturn(false);
        $field->shouldReceive('getErrors')->andReturn([]);
        $gate->addField('username', $field);

        self::expectException(InvalidValue::class);
        self::expectExceptionMessage('The value "john" is not valid for username');

        $gate->getData();
    }

    /** @test */
    public function throwsWithMessageFromValidator()
    {
        $gate = new Gate([ 'username' => 'john']);
        $field = \Mockery::mock(Field::class)->makePartial();
        $field->shouldReceive('validate')->andReturn(false);
        $field->shouldReceive('getErrors')->once()->andReturn([
            ['key' => 'WHAT_EVER', 'value' => 'john', 'message' => 'error message from first validator']
        ]);
        $gate->addField('username', $field);

        self::expectException(InvalidValue::class);
        self::expectExceptionMessage('Invalid username: error message from first validator');

        $gate->getData();
    }

    /** @test */
    public function returnsSingleField()
    {
        $gate = new Gate([ 'username' => 'john@example' ]);
        $field = \Mockery::mock(Field::class)->makePartial();
        $field->shouldReceive('filter')->andReturn('john');
        $field->shouldReceive('validate')->andReturn(true);
        $gate->addField('username', $field);

        $username = $gate->getData('username');

        self::assertSame('john', $username);
    }

    /** @test */
    public function returnsNullWhenFieldIsNotDefined()
    {
        $gate = new Gate();

        $result = $gate->getData('unknown');

        self::assertNull($result);
    }
}
