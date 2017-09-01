<?php

namespace Verja\Test\Filter;

use Verja\Filter\Trim;
use Verja\Test\TestCase;

class TrimTest extends TestCase
{
    /** @dataProvider provideStringsWithWhitespace
     * @param string $value
     * @param string $expected
     * @test */
    public function removesWhitespace($value, $expected)
    {
        $filter = new Trim();

        $result = $filter->filter($value);

        self::assertSame($expected, $result);
    }

    public function provideStringsWithWhitespace()
    {
        return [
          [ ' username ', 'username' ],
          [ "\ntext comment\n", "text comment" ],
          [ "\ttabs are whitespace too ", "tabs are whitespace too"],
          [ " \t\n\r\0\x0B ", ""], // from documentation
        ];
    }

    /** @dataProvider provideStringsWithChars
     * @param string $value
     * @param string $expected
     * @test */
    public function removesGivenCharacters($chars, $value, $expected)
    {
        $filter = new Trim($chars);

        $result = $filter->filter($value);

        self::assertSame($expected, $result);
    }

    public function provideStringsWithChars()
    {
        return [
            [ '/', '/path/to/file/', 'path/to/file' ],
            [ '#', '# untrimmed #', ' untrimmed ' ], // only slash is trimmed
            [ '$/', '/$/$/$/42$/$/$/$', '42' ], // multiple characters
        ];
    }
}
