<?php

namespace Verja\Test\Field;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Verja\Field;
use Verja\Test\TestCase;
use Verja\Validator\NotEmpty;
use Verja\Validator\StrLen;
use Verja\ValidatorInterface;

class ValidatorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @dataProvider provideValidatorsValueExpected
     * @param ValidatorInterface[] $validators
     * @param mixed $value
     * @param bool $expected
     * @test */
    public function executesAllValidators($validators, $value, $expected)
    {
        $field = new Field();
        foreach ($validators as $validator) {
            $field->addValidator($validator);
        }

        $result = $field->validate($value);

        self::assertSame($expected, $result);
    }

    public function provideValidatorsValueExpected()
    {
        return [
            [
                [new NotEmpty(), new StrLen(0, 5)],
                'too long',
                false
            ],
            [
                [new NotEmpty(), new StrLen(0, 5)],
                'ok',
                true
            ],
        ];
    }

    /** @test */
    public function executesInOrder()
    {
        $v1 = \Mockery::mock(NotEmpty::class);
        $v2 = \Mockery::mock(StrLen::class);
        $field = new Field();
        $field->appendValidator($v2);
        $field->prependValidator($v1);

        $v1->shouldReceive('validate')->with('str')->globally()->once()->ordered();
        $v2->shouldReceive('validate')->with('str')->globally()->once()->ordered();

        $field->validate('str');
    }
}
