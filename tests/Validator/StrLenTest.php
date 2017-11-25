<?php

namespace Verja\Test\Validator;

use Verja\Error;
use Verja\Test\TestCase;
use Verja\Validator\StrLen;

class StrLenTest extends TestCase
{
    /** @test */
    public function requiresMinLengthInteger()
    {
        self::expectException(\TypeError::class);

        new StrLen('a');
    }

    /** @test */
    public function acceptsEmptyString()
    {
        $validator = new StrLen(0);

        $result = $validator->validate('');

        self::assertTrue($result);
    }

    /** @test */
    public function limitsLengthTest()
    {
        $validator = new StrLen(0, 2);

        $result = $validator->validate('long');

        self::assertFalse($result);
        self::assertEquals(
            new Error('STRLEN_TOO_LONG', 'long', 'value should be maximal 2 characters long', ['min' => 0, 'max' => 2]),
            $validator->getError()
        );
    }

    /** @test */
    public function requiresMinLength()
    {
        $validator = new StrLen(6);

        $result = $validator->validate('short');

        self::assertFalse($result);
        self::assertEquals(
            new Error(
                'STRLEN_TOO_SHORT',
                'short',
                'value should be at least 6 characters long',
                ['min' => 6, 'max' => 0]
            ),
            $validator->getError()
        );
    }

    /** @test */
    public function unlimitedLength()
    {
        $validator = new StrLen(2);

        $result = $validator->validate(str_repeat('long value', 200));

        self::assertTrue($result);
    }
}
