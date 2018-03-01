<?php

namespace Verja\Test\Validator;

use Verja\Error;
use Verja\Test\TestCase;
use Verja\Validator\InArray;

class InArrayTest extends TestCase
{
    /** @test */
    public function acceptsValueFromArray()
    {
        $validator = new InArray(['foo', 'bar']);

        $result = $validator->validate('foo');

        self::assertTrue($result);
    }

    /** @test */
    public function acceptsCommaSeparatedString()
    {
        $validator = new InArray('foo,bar');

        $result = $validator->validate('bar');

        self::assertTrue($result);
    }

    /** @test */
    public function acceptsTraversable()
    {
        $validator = new InArray(new \ArrayObject(['foo', 'bar']));

        $result = $validator->validate('foo');

        self::assertTrue($result);
    }

    /** @test */
    public function throwsInvalidArgumentOtherwise()
    {
        self::expectException(\InvalidArgumentException::class);

        new InArray(new \stdClass());
    }

    /** @dataProvider provideInvalidValues
     * @test */
    public function rejectsValuesNotInArray($array, $value)
    {
        $validator = new InArray($array);

        $result = $validator->validate($value);

        self::assertFalse($result);
    }

    /** @dataProvider provideInvalidValues
     * @test */
    public function storesErrors($array, $value)
    {
        $validator = new InArray($array);

        $validator->validate($value);

        self::assertEquals(new Error(
            'NOT_IN_ARRAY',
            $value,
            'value should be in array',
            ['array' => is_string($array) ? explode(',', $array) : $array]
        ), $validator->getError());
    }

    public function provideInvalidValues()
    {
        return [
            [['foo', 'bar'], 'baz'],
            [[], ''],
            //['', ''] CAREFUL! will result in true!
            ['foo', ''],
            [',foo,bar', 'baz'],
        ];
    }
}
