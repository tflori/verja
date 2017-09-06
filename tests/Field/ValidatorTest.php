<?php

namespace Verja\Test\Field;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Verja\Field;
use Verja\Test\Examples\NotSerializable;
use Verja\Test\TestCase;
use Verja\Validator\NotEmpty;
use Verja\Validator\StrLen;
use Verja\ValidatorInterface;

class ValidatorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

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
    public function allowsStringForFilters()
    {
        $field = new Field();

        $field->appendValidator('notEmpty');

        self::assertFalse($field->validate(''));
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
}
