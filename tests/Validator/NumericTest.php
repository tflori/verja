<?php

namespace Verja\Test\Validator;

use Verja\Error;
use Verja\Test\TestCase;
use Verja\Validator\Numeric;

class NumericTest extends TestCase
{
    /** @test */
    public function allowsInteger()
    {
        $validator = new Numeric();

        self::assertTrue($validator->validate(42));
    }

    /** @test */
    public function allowsDouble()
    {
        $validator = new Numeric();

        self::assertTrue($validator->validate(0.42));
    }

    /** @dataProvider provideValidStrings
     * @test */
    public function allowsNumericStrings($decimalPoint, $string)
    {
        $validator = new Numeric($decimalPoint);

        self::assertTrue($validator->validate($string));
    }

    public function provideValidStrings()
    {
        return [
            ['.', '0.23'],
            [',', '0,23'],
            [',', '1.000,5E-3'],
        ];
    }

    /** @test */
    public function storesError()
    {
        $validator = new Numeric();

        self::assertFalse($validator->validate('0,23'));
        self::assertEquals(new Error('NOT_NUMERIC', '0,23', 'value should be numeric'), $validator->getError());
    }

    /** @test */
    public function buildsInverseError()
    {
        $validator = new Numeric();

        self::assertEquals(
            new Error('IS_NUMERIC', '0.23', 'value should not be numeric'),
            $validator->getInverseError('0.23')
        );
    }
}
