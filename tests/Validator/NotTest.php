<?php

namespace Verja\Test\Validator;

use Verja\Test\TestCase;
use Verja\Validator\Contains;
use Verja\Validator\Equals;
use Verja\Validator\Not;

class NotTest extends TestCase
{
    /** @test */
    public function invertsValidator()
    {
        $validator = new Not(new Contains(' '));

        $result = $validator->validate('with spaces');

        self::assertFalse($result);
    }

    /** @test */
    public function acceptsString()
    {
        $validator = new Not('contains: ');

        $result = $validator->validate('noSpaces');

        self::assertTrue($result);
    }

    /** @test */
    public function passesContextToValidator()
    {
        $validator = new Not('equals:pw-confirm');

        $result = $validator->validate('foo', ['pw-confirm' => 'foo']);

        self::assertFalse($result);
    }
}
