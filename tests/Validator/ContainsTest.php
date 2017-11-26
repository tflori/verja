<?php

namespace Verja\Test\Validator;

use Verja\Error;
use Verja\Test\TestCase;
use Verja\Validator\Contains;

class ContainsTest extends TestCase
{
    /** @dataProvider provideContainedStrings
     * @param $subString
     * @param $value
     * @test */
    public function returnsTrueForContainedStrings($subString, $value)
    {
        $validator = new Contains($subString);

        $result = $validator->validate($value);

        self::assertTrue($result);
    }

    public function provideContainedStrings()
    {
        return [
            ['b', 'beginning'],
            ['b', 'somewhere between'],
            ['d', 'at the end'],
        ];
    }

    /** @test */
    public function returnsFalseForNotContainedStrings()
    {
        $validator = new Contains(' ');

        $result = $validator->validate('noSpaces');

        self::assertFalse($result);
        self::assertEquals(
            new Error('NOT_CONTAINS', 'noSpaces', 'value should contain " "', ['subString' => ' ']),
            $validator->getError()
        );
    }

    public function provideNotContainedStrings()
    {
        return [
            [' ', 'noSpaces'],
        ];
    }

    /** @test */
    public function returnsContainsError()
    {
        $validator = new Contains(' ');

        $result = $validator->getInverseError('with space');

        self::assertEquals(
            new Error('CONTAINS', 'with space', 'value should not contain " "', ['subString' => ' ']),
            $result
        );
    }
}
