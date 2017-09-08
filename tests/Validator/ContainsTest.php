<?php

namespace Verja\Test\Validator;

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
        self::assertSame([
            'key' => 'NOT_CONTAINS',
            'value' => 'noSpaces',
            'parameters' => ['subString' => ' '],
            'message' => '"noSpaces" should contain " "'
        ], $validator->getError());
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

        self::assertSame([
            'key' => 'CONTAINS',
            'value' => 'with space',
            'parameters' => ['subString' => ' '],
            'message' => '"with space" should not contain " "'
        ], $result);
    }
}
