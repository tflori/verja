<?php

namespace Verja\Test\Field;

use PHPUnit\Framework\TestCase;
use Verja\Field;
use Verja\Validator\NotEmpty;
use Verja\Validator\StrLen;
use Verja\ValidatorInterface;

class ValidatorTest extends TestCase
{
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
}
