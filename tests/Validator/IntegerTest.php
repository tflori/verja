<?php

namespace Verja\Test\Validator;

use Verja\Error;
use Verja\Test\TestCase;
use Verja\Validator\Integer;

class IntegerTest extends TestCase
{
    /** @test */
    public function allowsInteger()
    {
        $validator = new Integer();

        self::assertTrue($validator->validate(42));
    }

    /** @dataProvider provideValidStrings
     * @test */
    public function allowsIntegerStrings($string)
    {
        $validator = new Integer();

        self::assertTrue($validator->validate($string));
    }

    public function provideValidStrings()
    {
        return [
            ['42'],
            ['1E3'],
            ['1.42E2'],
        ];
    }

    /** @test */
    public function storesError()
    {
        $validator = new Integer();

        self::assertFalse($validator->validate('0.42'));
        self::assertEquals(new Error('NO_INTEGER', '0.42', 'value should be an integer'), $validator->getError());
    }

    /** @test */
    public function buildsInverseError()
    {
        $validator = new Integer();

        self::assertEquals(
            new Error('IS_INTEGER', '42', 'value should not be an integer'),
            $validator->getInverseError('42')
        );
    }
}
