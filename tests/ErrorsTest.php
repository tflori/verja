<?php

namespace Verja\Test;

use Verja\Field;
use Verja\Gate;
use Verja\Test\Examples\CustomValidator\GeneratedMessage;
use Verja\Validator\NotEmpty;

class ErrorsTest extends TestCase
{
    /** @test */
    public function gateCallsGetErrorsWhenInvalid()
    {
        $gate = new Gate();
        $field = \Mockery::mock(Field::class)->makePartial();
        $gate->addField('f1', $field);

        $field->shouldReceive('validate')->with('value', ['f1' => 'value'])->andReturn(false);
        $field->shouldReceive('getErrors')->once()->andReturn([['key' => 'ERROR']]);

        $gate->validate(['f1' => 'value']);
    }

    /** @test */
    public function fieldCallsGetErrorWhenInvalid()
    {
        $field = new Field();
        $validator = \Mockery::mock(NotEmpty::class)->makePartial();
        $field->addValidator($validator);

        $validator->shouldReceive('validate')->with('value', [])->andReturn(false);
        $validator->shouldReceive('getError')->once()->andReturn(['key' => 'ERROR']);

        $field->validate('value');
    }

    /** @test */
    public function buildErrorGeneratesMessage()
    {
        $validator = new GeneratedMessage();
        $validator->validate('value');

        $result = $validator->getError();

        self::assertSame([
            'key' => 'GENERATED_MESSAGE',
            'value' => 'value',
            'message' => '"value" GENERATED_MESSAGE'
        ], $result);
    }
}
