<?php

namespace Verja\Test\Validator;

use Verja\Error;
use Verja\Test\TestCase;
use Verja\Validator\PregMatch;

class PregMatchTest extends TestCase
{
    /** @test */
    public function executesPregMatch()
    {
        $validator = new PregMatch('#foo#');

        $result = $validator->validate('some foo for you');

        self::assertTrue($result);
    }

    /** @test */
    public function setsError()
    {
        $validator = new PregMatch('#foo#');

        $result = $validator->validate('bar');

        self::assertFalse($result);
        self::assertEquals(
            new Error('NO_MATCH', 'bar', 'value should match "#foo#"', ['pattern' => '#foo#']),
            $validator->getError()
        );
    }

    /** @test */
    public function returnsInverseError()
    {
        $validator = new PregMatch('#foo#');

        self::assertEquals(
            new Error('MATCHES', 'some foo for you', 'value should not match "#foo#"', ['pattern' => '#foo#']),
            $validator->getInverseError('some foo for you')
        );
    }
}
