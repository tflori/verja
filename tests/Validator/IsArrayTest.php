<?php

namespace Verja\Test\Validator;

use Verja\Error;
use Verja\Test\TestCase;
use Verja\Validator\IsArray;

class IsArrayTest extends TestCase
{
    /** @dataProvider provideValidArrays
     * @test */
    public function acceptsTypesOfArray($type, $array)
    {
        $validator = new IsArray($type);

        $result = $validator->validate($array);

        self::assertTrue($result);
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

    /** @dataProvider provideInvalidArrays
     * @test */
    public function rejectsInvalidArrays($type, $array)
    {
        $validator = new IsArray($type);

        $result = $validator->validate($array);

        self::assertFalse($result);
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

    /** @dataProvider provideErroneousArrays
     * @test */
    public function storesErrors($type, $array, $eKey, $eMessage)
    {
        $validator = new IsArray($type);

        $validator->validate($array);

        self::assertEquals(new Error($eKey, $array, $eMessage, ['type' => $type]), $validator->getError());
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
