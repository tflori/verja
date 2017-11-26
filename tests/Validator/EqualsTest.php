<?php

namespace Verja\Test\Validator;

use Verja\Error;
use Verja\Test\TestCase;
use Verja\Validator\Equals;

class EqualsTest extends TestCase
{
    /** @dataProvider provideNotEqualOpposites
     * @param $value
     * @param $context
     * @test */
    public function returnsFalseWhenNotEqual($value, $context)
    {
        $validator = new Equals('opposite');

        $result = $validator->validate($value, $context);

        self::assertFalse($result);
    }

    public function provideNotEqualOpposites()
    {
        return [
            ['foo', ['opposite' => 'bar']],
            [null, ['opposite' => 'foo']],
            ['foo', []],
        ];
    }

    /** @dataProvider provideEqualOpposites
     * @param $value
     * @param $context
     * @test */
    public function returnsTrueWhenEqual($value, $context)
    {
        $validator = new Equals('opposite');

        $result = $validator->validate($value, $context);

        self::assertTrue($result);
    }

    public function provideEqualOpposites()
    {
        return [
            ['foo', ['opposite' => 'foo']],
            [null, []],
            ['', ['opposite' => null]],
            [['foo','bar'], ['opposite' => ['foo','bar']]],
            [['foo' => 'bar'], ['opposite' => (object) ['foo' => 'bar']]],
            [['f1' => 'v1', 'f2' => 'v2'], ['opposite' => (object)['f2' => 'v2', 'f1' => 'v1']]],
        ];
    }

    /** @test */
    public function jsonEncodingCanBeDisabled()
    {
        $validator = new Equals('opposite', '0'); // from string parsing we get '0'

        $result = $validator->validate(['foo' => 'bar'], ['opposite' => (object) ['foo' => 'bar']]);

        self::assertFalse($result);
        self::assertEquals(
            new Error(
                'NOT_EQUAL',
                ['foo' => 'bar'],
                'value should be equal to contexts opposite',
                ['opposite' => 'opposite', 'jsonEncode' => false]
            ),
            $validator->getError()
        );
    }

    /** @test */
    public function returnsInverseError()
    {
        $validator = new Equals('opposite');

        $result = $validator->getInverseError('value');

        self::assertEquals(
            new Error(
                'EQUALS',
                'value',
                'value should not be equal to contexts opposite',
                ['opposite' => 'opposite', 'jsonEncode' => true]
            ),
            $result
        );
    }
}
