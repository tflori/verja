<?php

namespace Verja\Test\Validator;

use Verja\Error;
use Verja\Test\TestCase;
use Verja\Validator\Truthful;

class TruthfulTest extends TestCase
{
    /** @dataProvider provideTruthfulValues
     * @param $value
     * @test */
    public function allowsTruthfulValues($value)
    {
        $validator = new Truthful();

        self::assertTrue($validator->validate($value));
    }

    public function provideTruthfulValues()
    {
        return [
            ['1'],
            [true],
            ['anything'],
            [1],
            [new \stdClass()],
            [['']],
        ];
    }

    /** @dataProvider provideNonTruthfulValues
     * @param $value
     * @test */
    public function failsWithNonTruthfulValues($value)
    {
        $validator = new Truthful();

        self::assertFalse($validator->validate($value));
    }

    public function provideNonTruthfulValues()
    {
        return [
            ['0'],
            [false],
            [''],
            [0],
            [[]],
        ];
    }

    /** @test */
    public function storesError()
    {
        $validator = new Truthful();

        self::assertFalse($validator->validate('0'));
        self::assertEquals(new Error('NOT_TRUTHFUL', '0', 'value should be truthful'), $validator->getError());
    }

    /** @test */
    public function buildsInverseError()
    {
        $validator = new Truthful();

        self::assertEquals(
            new Error('IS_TRUTHFUL', '1', 'value should not be truthful'),
            $validator->getInverseError('1')
        );
    }
}
