<?php

namespace Verja\Test\Validator;

use Verja\Error;
use Verja\Test\TestCase;
use Verja\Validator\Slug;

class SlugTest extends TestCase
{
    /** @dataProvider provideSlugs
     * @param $value
     * @test */
    public function acceptsStringsWithSlugs($value)
    {
        $validator = new Slug();

        $result = $validator->validate($value);

        self::assertTrue($result);
    }

    /** @dataProvider provideNonSlugs
     * @param $value
     * @test */
    public function doesNotAcceptOtherCharacters($value)
    {
        $validator = new Slug();

        $result = $validator->validate($value);

        self::assertFalse($result);
    }


    /** @test */
    public function storesAnErrorMessage()
    {
        $validator = new Slug();

        $validator->validate('foo?');

        self::assertEquals(
            new Error(
                'NO_SLUG',
                'foo?',
                'value should be a valid slug'
            ),
            $validator->getError()
        );
    }

    public function provideSlugs()
    {
        return [
            ['42'],
            ['foobar'],
            ['foo-bar'],
            ['foo_bar'],
        ];
    }

    public function provideNonSlugs()
    {
        return [
            ['۴۲'], // Arabic 42
            ['名'],
            ['à'],
            ['Müller'],
            ['Karīna'],
            ['string with space'],
            [''], // empty is not valid
        ];
    }
}
