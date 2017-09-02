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

    /** @dataProvider provideNotContainedStrings
     * @param $subString
     * @param $value
     * @test */
    public function returnsFalseForNotContainedStrings($subString, $value)
    {
        $validator = new Contains($subString);

        $result = $validator->validate($value);

        self::assertFalse($result);
    }

    public function provideNotContainedStrings()
    {
        return [
            [' ', 'noSpaces'],
        ];
    }
}
