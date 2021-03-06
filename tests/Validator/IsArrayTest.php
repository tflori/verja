<?php

namespace Verja\Test\Validator;

use Verja\Error;
use Verja\Test\TestCase;
use Verja\Validator\IsArray;

class IsArrayTest extends TestCase
{
    /** @dataProvider provideValidArrays
     * @param $type
     * @param $array
     * @test */
    public function acceptsTypesOfArray($type, $array)
    {
        $validator = new IsArray($type);

        $result = $validator->validate($array);

        self::assertTrue($result);
    }

    /** @dataProvider provideInvalidArrays
     * @param $type
     * @param $array
     * @test */
    public function rejectsInvalidArrays($type, $array)
    {
        $validator = new IsArray($type);

        $result = $validator->validate($array);

        self::assertFalse($result);
    }

    /** @dataProvider provideErroneousArrays
     * @param $type
     * @param $array
     * @param $eKey
     * @param $eMessage
     * @test */
    public function storesErrors($type, $array, $eKey, $eMessage)
    {
        $validator = new IsArray($type);

        $validator->validate($array);

        self::assertEquals(new Error($eKey, $array, $eMessage, ['type' => $type]), $validator->getError());
    }

    /** @test */
    public function returnsAnInverseError()
    {
        $validator = new IsArray();

        self::assertEquals(new Error(
            'IS_ARRAY',
            ['foo', 'bar'],
            'value should not be an array'
        ), $validator->getInverseError(['foo', 'bar']));
    }

    public function provideValidArrays()
    {
        return [
            [IsArray::TYPE_ANY, ['a','b','c']],
            [IsArray::TYPE_INDEX, ['a','b','c']],
            [IsArray::TYPE_ANY, ['a' => 0.23, 'b' => 0.42, 'c' => 1]],
            [IsArray::TYPE_ASSOC, ['a' => 0.23, 'b' => 0.42, 'c' => 1]],
        ];
    }

    public function provideInvalidArrays()
    {
        $indexedArray = ['a','b','c'];
        unset($indexedArray[0]); // now it is not indexed anymore the keys are 1 and 2

        return [
            [IsArray::TYPE_ASSOC, ['a','b','c']],
            [IsArray::TYPE_INDEX, ['a' => 0.23, 'b' => 0.42, 'c' => 1]],
            [IsArray::TYPE_INDEX, [2 => 23, 1 => 42]],
            [IsArray::TYPE_INDEX, $indexedArray], // see above
            [IsArray::TYPE_ANY, 'a,b,c'],
        ];
    }

    public function provideErroneousArrays()
    {
        return [
            ['any', 'a,b,c', 'NO_ARRAY', 'value should be an array'],
            ['assoc', ['a','b','c'], 'NO_ASSOC_ARRAY', 'value should be an associative array'],
            ['index', ['a' => 42], 'NO_INDEX_ARRAY', 'value should be an indexed array'],
        ];
    }
}
