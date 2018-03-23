<?php

namespace Verja\Test\Validator;

use Verja\Error;
use Verja\Test\TestCase;
use Verja\Validator\CreditCard;

/**
 * Class CreditCardTest
 *
 * Credit card validation is done using the [Luhn algorithm](https://en.wikipedia.org/wiki/Luhn_algorithm). To sum it
 * up: every second digit (from right) gets multiplied by two and the checksum over all digits has to be divisible by
 * ten.
 *
 * For these tests we will always use pairs that result in the checksum 10. Examples:
 * 91 = (9*2) + 1 = 1 + 8 + 1 = 10
 * 83 = (8*2) + 3 = 1 + 6 + 3 = 10
 * 42 = (4*2) + 2 =     8 + 2 = 10
 *
 * Exception is the double 0 that results in 0 - what is valid too
 * 00 = (0*2) + 0 =     0 + 0 =  0
 */
class CreditCardTest extends TestCase
{
    /** @test */
    public function nonStringValueIsInvalid()
    {
        $validator = new CreditCard();

        $result = $validator->validate(596775839142);

        self::assertFalse($result);
    }

    /** @test */
    public function minLengthIsTwelve()
    {
        $validator = new CreditCard();

        $result = $validator->validate('5967758391');

        self::assertFalse($result);
    }

    /** @test */
    public function spacesGetErased()
    {
        $validator = new CreditCard();

        $result = $validator->validate('5967 7583 9142');

        self::assertTrue($result);
    }

    /** @test */
    public function otherCharactersAreInvalid()
    {
        $validator = new CreditCard();

        $result = $validator->validate('5967-7583-9142');

        self::assertFalse($result);
    }

    /** @test */
    public function checkSumHasToBeDivisibleByTen()
    {
        $validator = new CreditCard();

        $result = $validator->validate('596775839105'); // last pair results in 5

        self::assertFalse($result);
    }

    /** @test */
    public function storesAnError()
    {
        $validator = new CreditCard();

        $validator->validate('596775839105');

        self::assertEquals(new Error(
            'NO_CREDIT_CARD',
            '596775839105',
            'value should be a valid credit card number'
        ), $validator->getError());
    }

    /** @dataProvider provideCreditCardTypes
     * @param $types
     * @param $value
     * @param $expected
     * @test */
    public function validatesCardTypes($types, $value, $expected)
    {
        $types = is_string($types) ? [$types] : $types;
        $validator = new CreditCard($types);

        $result = $validator->validate($value);

        self::assertSame($expected, $result);
        if (!$expected) {
            self::assertEquals(new Error(
                'WRONG_CREDIT_CARD',
                $value,
                'value should be a credit card of type ' . implode(' or ', $types),
                ['types' => $types]
            ), $validator->getError());
        }
    }

    public function provideCreditCardTypes()
    {
        // visa: len 13|16|19, begins with 4
        // mastercard: len 16, begins with 51-55 or 2221-2720
        // amex: len 15, begins with 34 or 37
        // dinersclub: len 14, begins with 30[0-5] or 36[0-9] or 38[0-9]
        // discover: len 16, begins with 6011 or 65

        return [
            // visa tests
            ['visa', $this->appendChecksum('3234 5678 9012 '), false],
            ['visa', $this->appendChecksum('4234 5678 9012 '), true],
            ['visa', $this->appendChecksum('5234 5678 9012 '), false],
            ['visa', $this->appendChecksum('4234 5678 9012 3'), false],
            ['visa', $this->appendChecksum('4234 5678 9012 34'), false],
            ['visa', $this->appendChecksum('4234 5678 9012 345'), true],
            ['visa', $this->appendChecksum('4234 5678 9012 3456 '), false],
            ['visa', $this->appendChecksum('4234 5678 9012 3456 7'), false],
            ['visa', $this->appendChecksum('4234 5678 9012 3456 78'), true],

            // master card tests
            ['mastercard', $this->appendChecksum('5034 5678 9012 345'), false],
            ['mastercard', $this->appendChecksum('5134 5678 9012 345'), true],
            ['mastercard', $this->appendChecksum('5234 5678 9012 345'), true],
            ['mastercard', $this->appendChecksum('5334 5678 9012 345'), true],
            ['mastercard', $this->appendChecksum('5434 5678 9012 345'), true],
            ['mastercard', $this->appendChecksum('5534 5678 9012 345'), true],
            ['mastercard', $this->appendChecksum('5634 5678 9012 345'), false],
            ['mastercard', $this->appendChecksum('2220 5678 9012 345'), false],
            ['mastercard', $this->appendChecksum('2221 5678 9012 345'), true],
            ['mastercard', $this->appendChecksum('2400 5678 9012 345'), true],
            ['mastercard', $this->appendChecksum('2720 5678 9012 345'), true],
            ['mastercard', $this->appendChecksum('2721 5678 9012 345'), false],
            ['mastercard', $this->appendChecksum('5134 5678 9012 34'), false],
            ['mastercard', $this->appendChecksum('5134 5678 9012 3456 '), false],

            // amex tests
            ['amex', $this->appendChecksum('3334 5678 9012 34'), false],
            ['amex', $this->appendChecksum('3434 5678 9012 34'), true],
            ['amex', $this->appendChecksum('3534 5678 9012 34'), false],
            ['amex', $this->appendChecksum('3634 5678 9012 34'), false],
            ['amex', $this->appendChecksum('3734 5678 9012 34'), true],
            ['amex', $this->appendChecksum('3834 5678 9012 34'), false],
            ['amex', $this->appendChecksum('3434 5678 9012 3'), false],
            ['amex', $this->appendChecksum('3434 5678 9012 345'), false],

            // multiple types allowed
            [['mastercard', 'visa'], '5108 5967 7583 9142', true], // master card
            [['mastercard', 'visa'], '4267 7583 9159 6718', true], // visa
            [['mastercard', 'visa'], '3467 7583 9159 6718', false], // amex not allowed

            // unknown types are valid
            ['amazon', '5967 7583 9142 6718', true], // amazon is a visa card...
        ];
    }

    /**
     * Calculates the last digit (checksum) of a credit card and appends it
     *
     * @param $number
     * @return string
     */
    protected function appendChecksum(string $number): string
    {
        $sum = '';
        $revNumber = '0' . strrev(str_replace(' ', '', $number));
        $len = strlen($revNumber);

        for ($i = 0; $i < $len; $i++) {
            $sum .= $i & 1 ? $revNumber[$i] * 2 : $revNumber[$i];
        }

        return $number . ((10 - array_sum(str_split($sum)) % 10) % 10);
    }
}
