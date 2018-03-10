<?php

namespace Verja\Test\Validator;

use Verja\Error;
use Verja\Test\TestCase;
use Verja\Validator\Alpha;

class AlphaTest extends TestCase
{
    /** @dataProvider provideAlphabeticalStrings
     * @param $value
     * @test */
    public function acceptsStringsWithAlphabeticalCharacters($value)
    {
        $validator = new Alpha();

        $result = $validator->validate($value);

        self::assertTrue($result);
    }

    /** @dataProvider provideNonAlphabeticalStrings
     * @param $value
     * @test */
    public function doesNotAcceptOtherCharacters($value)
    {
        $validator = new Alpha();

        $result = $validator->validate($value);

        self::assertFalse($result);
    }

    /** @test */
    public function storesAnErrorMessage()
    {
        $validator = new Alpha();

        $validator->validate('42');

        self::assertEquals(
            new Error('CONTAINS_NON_ALPHA', '42', 'value should not contain non alphabetical characters'),
            $validator->getError()
        );
    }

    /** @test */
    public function allowsSpaces()
    {
        $validator = new Alpha('1'); // works also with 'alpha:t'

        $result = $validator->validate('string with spaces');

        self::assertTrue($result);
    }

    public function provideAlphabeticalStrings()
    {
        return [
            ['foobar'],
            ['Müller'],
            ['Karīna'],
            ['名'],
            ['à'],
            [''], // yes, empty is also valid
        ];
    }

    public function provideNonAlphabeticalStrings()
    {
        return [
            ['42'],
            ['_special-chars_'],
            ['a`'],
            ['string with space'],
        ];
    }
}
