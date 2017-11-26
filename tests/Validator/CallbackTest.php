<?php

namespace Verja\Test\Validator;

use Verja\Error;
use Verja\Test\TestCase;
use Verja\Validator\Callback;

class CallbackTest extends TestCase
{
    /** @test */
    public function callsCallbackToValidate()
    {
        $calls = [];
        $validator = new Callback(function () use (&$calls) {
            $calls[] = func_get_args();
            return true;
        });

        $validator->validate('value');

        self::assertSame([
            [ 'value', [] ]
        ], $calls);
    }

    /** @test */
    public function usesArrayReturnValueAsError()
    {
        $validator = new Callback(function ($value) {
            return new Error('KEY', $value);
        });

        $validator->validate('value');

        self::assertEquals(new Error('KEY', 'value', '"value" KEY'), $validator->getError());
    }

    /** @test */
    public function expectsKeyValueAndMessage()
    {
        $validator = new Callback(function () {
            return [];
        });

        $validator->validate('value');

        self::assertNull($validator->getError());
    }
}
