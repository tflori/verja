<?php

namespace Verja\Test\Validator;

use Verja\Test\TestCase;
use Verja\Validator;

class FromStringTest extends TestCase
{
    /** @dataProvider provideInvalidDefinitions
     * @param string $definition
     * @test */
    public function throwsWhenFilterSpecificationIsInvalid($definition)
    {
        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('is not a valid string for Verja\Parser::parseClassNameWithParameters');

        Validator::fromString($definition);
    }

    public function provideInvalidDefinitions()
    {
        return [
            [ ':something' ],
            [ '' ],
            [ ' ' ],
        ];
    }

    /** @test */
    public function parametersAreNotRequired()
    {
        $validator = Validator::fromString('notEmpty');

        self::assertEquals(new Validator\NotEmpty(), $validator);
    }

    /** @test */
    public function parametersFollowColon()
    {
        $validator = Validator::fromString('strLen:2');

        self::assertEquals(new Validator\StrLen(2), $validator);
    }

    /** @test */
    public function parametersCanHaveSpaces()
    {
        $validator = Validator::fromString('contains: ');

        self::assertEquals(new Validator\Contains(' '), $validator);
    }

    /** @test */
    public function invertsWithExclamationMark()
    {
        $validator = Validator::fromString('!contains: ');

        self::assertEquals(new Validator\Not(new Validator\Contains(' ')), $validator);
    }

    /** @test */
    public function multipleParametersAllowed()
    {
        $validator = Validator::fromString('strLen:2:5');

        self::assertEquals(new Validator\StrLen(2, 5), $validator);
    }

    /** @test */
    public function throwsWhenValidatorIsUnknown()
    {
        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('Validator \'UnknownValidator\' not found');

        Validator::fromString('unknownValidator');
    }
}
