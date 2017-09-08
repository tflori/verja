<?php

namespace Verja\Test\Validator;

use Verja\Test\TestCase;
use Verja\Validator\NotEmpty;

class NotEmptyTest extends TestCase
{
    /** @dataProvider provideNonEmptyValues
     * @param mixed $value
     * @test */
    public function returnsTrueForNonEmptyValues($value)
    {
        $validator = new NotEmpty();

        $result = $validator->validate($value);

        self::assertTrue($result);
    }

    public function provideNonEmptyValues()
    {
        return [
            [ 'a' ],  // non empty string
            [ [ 0 ] ],  // non empty array
            [ true ], // boolean true
            [ 1 ],    // integer != 0
            [ 0.1 ],  // float != 0.0
        ];
    }

    /** @dataProvider provideEmptyValues
     * @param mixed $value
     * @test */
    public function returnsFalseForEmptyValues($value)
    {
        $validator = new NotEmpty();

        $result = $validator->validate($value);

        self::assertFalse($result);
    }

    public function provideEmptyValues()
    {
        return [
            [ '' ], // empty string
            [ '0' ], // string '0'
            [ [] ], // empty array
            [ false ], // boolean false
            [ 0 ], // integer 0
            [ 0.0 ], // float 0.0
            [ null ], // null
        ];
    }

    /** @test */
    public function setsNotEmptyError()
    {
        $validator = new NotEmpty();
        $validator->validate(0);

        $result = $validator->getError();

        self::assertSame([
            'key' => 'IS_EMPTY',
            'value' => 0,
            'message' => '0 should not be empty'
        ], $result);
    }

    /** @test */
    public function returnsInverseError()
    {
        $validator = new NotEmpty();

        $result = $validator->getInverseError('value');

        self::assertSame([
            'key' => 'IS_NOT_EMPTY',
            'value' => 'value',
            'message' => '"value" should be empty'
        ], $result);
    }
}
