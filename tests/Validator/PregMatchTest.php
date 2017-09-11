<?php

namespace Verja\Test\Validator;

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
        self::assertSame([
            'key' => 'NO_MATCH',
            'value' => 'bar',
            'parameters' => ['pattern' => '#foo#'],
            'message' => 'value should match "#foo#"'
        ], $validator->getError());
    }

    /** @test */
    public function returnsInverseError()
    {
        $validator = new PregMatch('#foo#');

        self::assertSame([
            'key' => 'MATCHES',
            'value' => 'some foo for you',
            'parameters' => ['pattern' => '#foo#'],
            'message' => 'value should not match "#foo#"'
        ], $validator->getInverseError('some foo for you'));
    }
}
