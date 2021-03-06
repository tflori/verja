<?php

namespace Verja\Test\Gate;

use Mockery\Mock;
use Verja\Error;
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
            ->atLeast()->once()->andReturn('john');

        $gate->getData();
    }

    /** @test> */
    public function executesValidateOnField()
    {
        $gate = new Gate([ 'username' => 'john@example', 'password' => 'abc123' ]);
        $field = \Mockery::mock(Field::class)->makePartial();
        $gate->addField('username', $field);

        $field->shouldReceive('validate')
            ->with('john@example', ['username' => 'john@example', 'password' => 'abc123'])
            ->once()->andReturn(true);

        $gate->getData();
    }

    /** @test */
    public function setsValueToNullWhenInvalid()
    {
        $gate = new Gate([ 'username' => 'john']);
        $field = \Mockery::mock(Field::class)->makePartial();
        $field->shouldReceive('validate')->andReturn(false);
        $gate->addField('username', $field);

        $data = $gate->getData();

        self::assertSame([ 'username' => null ], $data);
    }

    /** @test */
    public function throwsWhenDataIsInvalidButRequired()
    {
        $gate = new Gate([ 'username' => 'john']);
        /** @var Mock|Field $field */
        $field = \Mockery::mock(Field::class)->makePartial();
        $field->required();
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
        /** @var Mock|Field $field */
        $field = \Mockery::mock(Field::class)->makePartial();
        $field->required();
        $field->shouldReceive('validate')->andReturn(false);
        $field->shouldReceive('getErrors')->once()->andReturn([
            new Error('WHAT_EVER', 'john', 'error message from first validator')
        ]);
        $gate->addField('username', $field);

        self::expectException(InvalidValue::class);
        self::expectExceptionMessage('Invalid username: error message from first validator');

        $gate->getData();
    }

    /** @test
     * @throws InvalidValue */
    public function exceptionsContainTheErrors()
    {
        $gate = new Gate([ 'username' => 'john']);
        $error = new Error('WHAT_EVER', 'john', 'error message from first validator');
        /** @var Mock|Field $field */
        $field = \Mockery::mock(Field::class)->makePartial();
        $field->required();
        $field->shouldReceive('validate')->andReturn(false);
        $field->shouldReceive('getErrors')->once()->andReturn([$error]);
        $gate->addField('username', $field);

        self::expectException(InvalidValue::class);
        try {
            $gate->getData();
        } catch (InvalidValue $exception) {
            self::assertContains($error, $exception->errors);
            throw $exception;
        }
    }

    /** @test */
    public function returnsSingleField()
    {
        $gate = new Gate([ 'username' => 'john@example' ]);
        /** @var Mock|Field $field */
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
