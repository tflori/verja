<?php

namespace Verja\Test\Validator;

use Verja\Error;
use Verja\Test\TestCase;
use Verja\Validator\AlphaNumeric;

class AlphaNumericTest extends TestCase
{
    /** @dataProvider provideAlphanumericStrings
     * @param $value
     * @test */
    public function acceptsStringsWithAlphabeticalCharacters($value)
    {
        $validator = new AlphaNumeric();

        $result = $validator->validate($value);

        self::assertTrue($result);
    }

    /** @dataProvider provideNonAlphanumericStrings
     * @param $value
     * @test */
    public function doesNotAcceptOtherCharacters($value)
    {
        $validator = new AlphaNumeric();

        $result = $validator->validate($value);

        self::assertFalse($result);
    }

    /** @test */
    public function storesAnErrorMessage()
    {
        $validator = new AlphaNumeric();

        $validator->validate('60%');

        self::assertEquals(
            new Error(
                'CONTAINS_NON_ALPHANUMERIC',
                '60%',
                'value should not contain non alphanumeric characters'
            ),
            $validator->getError()
        );
    }

    /** @test */
    public function allowsSpaces()
    {
        $validator = new AlphaNumeric(true);

        $result = $validator->validate('1 string with 4 spaces');

        self::assertTrue($result);
    }

    public function provideAlphanumericStrings()
    {
        return [
            ['42'],
            ['۴۲'], // Arabic 42
            ['foobar'],
            ['Müller'],
            ['Karīna'],
            ['名'],
            ['à'],
            [''], // yes, empty is also valid
        ];
    }

    public function provideNonAlphanumericStrings()
    {
        return [
            ['_special-chars_'],
            ['a`'],
            ['string with space'],
        ];
    }
}
