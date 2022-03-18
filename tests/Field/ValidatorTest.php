<?php

namespace Verja\Test\Field;

use Verja\Field;
use Verja\Gate;
use Verja\Test\Examples\NotSerializable;
use Verja\Test\TestCase;
use Verja\Validator\NotEmpty;
use Verja\Validator\StrLen;
use Verja\ValidatorInterface;

class ValidatorTest extends TestCase
{
    /** @dataProvider provideValidatorsValueExpected
     * @param ValidatorInterface[] $validators
     * @param mixed $value
     * @param bool $expected
     * @test */
    public function executesAllValidators($validators, $value, $expected)
    {
        $field = new Field();
        foreach ($validators as $validator) {
            $field->addValidator($validator);
        }

        $result = $field->validate($value);

        self::assertSame($expected, $result);
    }

    public function provideValidatorsValueExpected()
    {
        return [
            [
                [new NotEmpty(), new StrLen(0, 5)],
                'too long',
                false
            ],
            [
                [new NotEmpty(), new StrLen(0, 5)],
                'ok',
                true
            ],
        ];
    }

    /** @test */
    public function executesInOrder()
    {
        $v1 = \Mockery::mock(NotEmpty::class)->makePartial();
        $v2 = \Mockery::mock(StrLen::class)->makePartial();
        $field = new Field();
        $field->appendValidator($v2);
        $field->prependValidator($v1);

        $v1->shouldReceive('validate')->with('str', [])->globally()->once()->ordered();
        $v2->shouldReceive('validate')->with('str', [])->globally()->once()->ordered();

        $field->validate('str');
    }

    /** @test */
    public function allowsStringForValidators()
    {
        $field = new Field();

        $field->appendValidator('notEmpty');

        self::assertFalse($field->validate(''));
    }

    /** @test */
    public function acceptsFunctionForValidators()
    {
        $field = new Field();

        $field->addValidator(function () {
            return false;
        });

        self::assertFalse($field->validate('value'));
    }

    /** @test */
    public function throwsWhenNoValidatorGiven()
    {
        $field = new Field();

        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('$validator has to be an instance of ValidatorInterface');

        $field->addValidator(new Gate()); // something that is not a filter
    }

    /** @test */
    public function assignsTheField()
    {
        $field = new Field();
        $validator = \Mockery::mock(NotEmpty::class)->makePartial();
        $validator->shouldReceive('assign')->with($field)->once()->andReturnSelf();

        $field->addValidator($validator);
    }

    /** @test */
    public function storesValidation()
    {
        $field = new Field();
        $validator = \Mockery::mock(NotEmpty::class)->makePartial();
        $field->addValidator($validator);
        $validator->shouldReceive('validate')->with('body', [])->once()->andReturn(true);

        $field->validate('body');
        $field->validate('body');
    }

    /** @test */
    public function basedOnValueHash()
    {
        $field = new Field();
        $validator = \Mockery::mock(NotEmpty::class)->makePartial();
        $field->addValidator($validator);
        $validator->shouldReceive('validate')->with('v1', [])->once()->andReturn(true);
        $validator->shouldReceive('validate')->with('v2', [])->once()->andReturn(true);

        $field->validate('v1');
        $field->validate('v2');
    }

    /** @test */
    public function catchesSerializeExceptions()
    {
        $unserializeableValue = new NotSerializable();
        $field = new Field();
        $validator = \Mockery::mock(NotEmpty::class)->makePartial();
        $field->addValidator($validator);
        $validator->shouldReceive('validate')->with($unserializeableValue, [])->twice()->andReturn(true);

        $field->validate($unserializeableValue);
        $field->validate($unserializeableValue);
    }

    /** @test */
    public function resetsCacheWhenFiltersChange()
    {
        $field = new Field();
        $validator = \Mockery::mock(NotEmpty::class)->makePartial();
        $field->addValidator($validator);
        $validator->shouldReceive('validate')->with(' body ', [])->twice()->andReturn(true);

        $field->validate(' body ');
        $field->addValidator('notEmpty');
        $field->validate(' body ');
    }

    /** @test */
    public function executesFilterBeforeValidation()
    {
        $field = \Mockery::mock(Field::class)->makePartial();
        $validator = \Mockery::mock(NotEmpty::class)->makePartial();
        $field->addValidator($validator);

        $field->shouldReceive('filter')
            ->with('value', [])
            ->once()->andReturn(42);
        $validator->shouldReceive('validate')
            ->with(42, [])
            ->once()->andReturn(true);

        $field->validate('value');
    }
}
