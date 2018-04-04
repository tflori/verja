<?php

namespace Verja\Test;

use Verja\Parser;

class ParserTest extends TestCase
{
    /** @test */
    public function returnsAnArray()
    {
        $result = Parser::parseParameters('');

        self::assertSame([], $result);
    }

    /** @test */
    public function returnsBooleanTrue()
    {
        $result = Parser::parseParameters('true');

        self::assertSame([true], $result);
    }

    /** @test */
    public function returnsBooleanFalse()
    {
        $result = Parser::parseParameters('false');

        self::assertSame([false], $result);
    }

    /** @test */
    public function returnsNull()
    {
        $result = Parser::parseParameters('null');

        self::assertSame([null], $result);
    }

    /** @test */
    public function worksTogether()
    {
        $result = Parser::parseParameters('true:null:false');

        self::assertSame([true, null, false], $result);
    }
}
