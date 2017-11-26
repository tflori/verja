<?php

namespace Verja\Test;

use Verja\Error;
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
    public function errorsCanBeSerialized()
    {
        $error = new Error('ERROR_KEY', 'validated value', 'Error message from validator');

        $serialized = serialize($error);

        self::assertContains('ERROR_KEY', $serialized);
        self::assertContains('validated value', $serialized);
        self::assertContains('Error message from validator', $serialized);
    }

    /** @test */
    public function errorsCanBeUnserialized()
    {
        $error = new Error('ERROR_KEY', 'validated value', 'Error message from validator');
        $serialized = serialize($error);

        $result = unserialize($serialized);

        self::assertEquals($error, $result);
    }

    /** @test */
    public function errorsCanBeJsonEncoded()
    {
        $error = new Error('ERROR_KEY', 'validated value', 'Error message from validator');

        $json = json_encode($error);

        self::assertSame(json_encode([
            'key' => 'ERROR_KEY',
            'message' => 'Error message from validator',
            'parameters' => ['value' => 'validated value'],
        ]), $json);
    }
}
