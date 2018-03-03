<?php

namespace Verja\Test\Gate;

use Mockery\Mock;
use Verja\Field;
use Verja\Gate;
use Verja\Test\TestCase;

class ValidateTest extends TestCase
{
    /** @test */
    public function doesNotValidateEmptyOptionals()
    {
        $field = \Mockery::mock(Field::class)->makePartial();
        $field->shouldNotReceive('validate');

        $gate = new Gate([ 'f1' => '' ]);
        $gate->addField('f1', $field);

        $gate->validate();
    }

    /** @test */
    public function validatesEmptyRequired()
    {
        /** @var Mock|Field $field */
        $field = \Mockery::mock(Field::class)->makePartial();
        $field->required();
        $field->shouldReceive('validate')->with('', [ 'f1' => '' ])->once()->andReturn(true);

        $gate = new Gate([ 'f1' => '' ]);
        $gate->addField('f1', $field);

        $result = $gate->validate();

        // ATTENTION: it is still valid, cause the validators (none) allow empty values
        self::assertTrue($result);
    }

    /** @test */
    public function validatesEachField()
    {
        /** @var Mock|Field $field1 */
        $field1 = \Mockery::mock(Field::class)->makePartial();
        $field1->required();
        $field1->shouldReceive('validate')->with('value1', [ 'f1' => 'value1' ])->once()->andReturn(false);
        /** @var Mock|Field $field2 */
        $field2 = \Mockery::mock(Field::class)->makePartial();
        $field2->required();
        $field2->shouldReceive('validate')->with(null, [ 'f1' => 'value1' ])->once()->andReturn(true);

        $gate = new Gate([ 'f1' => 'value1' ]);
        $gate->addFields([ 'f1' => $field1, 'f2' => $field2]);

        $result = $gate->validate();

        self::assertFalse($result);
    }

    /** @test */
    public function usesFilteredValueForValidation()
    {
        $field = \Mockery::mock(Field::class)->makePartial();
        $field->shouldReceive('filter')->with('value', [ 'f' => 'value' ])->once()->andReturn(42);
        $field->shouldReceive('validate')->with(42, [ 'f' => 'value' ])->once()->andReturn(true);

        $gate = new Gate([ 'f' => 'value' ]);
        $gate->addField('f', $field);

        $result = $gate->validate();

        self::assertTrue($result);
    }

    /** @test */
    public function validatesGivenArray()
    {
        $field = \Mockery::mock(Field::class)->makePartial();
        $field->shouldReceive('filter')->with('validate', ['f' => 'validate'])->once()->andReturn('value');

        $gate = new Gate([ 'f' => 'constructor' ]);
        $gate->addField('f', $field);

        $gate->validate([ 'f' => 'validate' ]);
    }
}
