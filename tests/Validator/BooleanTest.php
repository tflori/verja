<?php

namespace Verja\Test\Validator;

use Verja\Error;
use Verja\Test\TestCase;
use Verja\Validator\Boolean;

class BooleanTest extends TestCase
{
    /** @test */
    public function allowsBoolean()
    {
        $validator = new Boolean();

        self::assertTrue($validator->validate(true));
    }

    /** @test */
    public function allowsInteger()
    {
        $validator = new Boolean();

        self::assertTrue($validator->validate(0));
    }

    /** @dataProvider provideBasicBooleanStrings
     * @test */
    public function allowsBooleanStringsByDefault($string)
    {
        $validator = new Boolean();

        self::assertTrue($validator->validate($string));
    }

    public function provideBasicBooleanStrings()
    {
        return [
            ['1'],
            ['0'],
            ['true'],
            ['false'],
            ['t'],
            ['f'],
            ['yes'],
            ['no'],
            ['y'],
            ['n'],
        ];
    }

    /** @dataProvider provideInvalidBooleans
     * @test */
    public function rejectsOtherValues($value)
    {
        $validator = new Boolean();

        self::assertFalse($validator->validate($value));
    }

    public function provideInvalidBooleans()
    {
        return [
            [null],
            [''],
            ['anything'],
            [0.23],
        ];
    }

    /** @test */
    public function storesError()
    {
        $validator = new Boolean();

        self::assertFalse($validator->validate('anything'));
        self::assertEquals(new Error('NOT_BOOLEAN', 'anything', 'value should be a boolean'), $validator->getError());
    }

    /** @test */
    public function buildsInverseError()
    {
        $validator = new Boolean();

        self::assertEquals(
            new Error('IS_BOOLEAN', true, 'value should not be a boolean'),
            $validator->getInverseError(true)
        );
    }

    /** @test */
    public function allowsCustomBooleanStrings()
    {
        $validator = new Boolean(['ja'], ['nein']);

        self::assertTrue($validator->validate('ja'));
        self::assertTrue($validator->validate('nein'));
    }
}
