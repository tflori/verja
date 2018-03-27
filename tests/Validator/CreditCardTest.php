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

    /** @test */
    public function providesAnInverseError()
    {
        $validator = new CreditCard('visa', 'amex');

        self::assertEquals(new Error(
            'CREDIT_CARD',
            '4234 5967 7583 9142',
            'value should not be a credit card of type visa or amex',
            ['types' => ['visa', 'amex']]
        ), $validator->getInverseError('4234 5967 7583 9142'));
    }

    public function provideCreditCardTypes()
    {
        // for a list of credit cards see https://en.wikipedia.org/wiki/Payment_card_number

        return [
            // visa tests
            'visa-a'       => ['visa', $this->appendChecksum('3234 5678 9012 '), false],
            'visa-b'       => ['visa', $this->appendChecksum('4234 5678 9012 '), true],
            'visa-c'       => ['visa', $this->appendChecksum('5234 5678 9012 '), false],
            'visa-d'       => ['visa', $this->appendChecksum('4234 5678 9012 3'), false],
            'visa-e'       => ['visa', $this->appendChecksum('4234 5678 9012 34'), false],
            'visa-f'       => ['visa', $this->appendChecksum('4234 5678 9012 345'), true],
            'visa-g'       => ['visa', $this->appendChecksum('4234 5678 9012 3456 '), false],
            'visa-h'       => ['visa', $this->appendChecksum('4234 5678 9012 3456 7'), false],
            'visa-i'       => ['visa', $this->appendChecksum('4234 5678 9012 3456 78'), true],

            // master card tests
            'mastercard-a' => ['mastercard', $this->appendChecksum('5034 5678 9012 345'), false],
            'mastercard-b' => ['mastercard', $this->appendChecksum('5134 5678 9012 345'), true],
            'mastercard-c' => ['mastercard', $this->appendChecksum('5234 5678 9012 345'), true],
            'mastercard-d' => ['mastercard', $this->appendChecksum('5334 5678 9012 345'), true],
            'mastercard-e' => ['mastercard', $this->appendChecksum('5434 5678 9012 345'), true],
            'mastercard-f' => ['mastercard', $this->appendChecksum('5534 5678 9012 345'), true],
            'mastercard-g' => ['mastercard', $this->appendChecksum('5634 5678 9012 345'), false],
            'mastercard-h' => ['mastercard', $this->appendChecksum('2220 5678 9012 345'), false],
            'mastercard-i' => ['mastercard', $this->appendChecksum('2221 5678 9012 345'), true],
            'mastercard-j' => ['mastercard', $this->appendChecksum('2400 5678 9012 345'), true],
            'mastercard-k' => ['mastercard', $this->appendChecksum('2720 5678 9012 345'), true],
            'mastercard-l' => ['mastercard', $this->appendChecksum('2721 5678 9012 345'), false],
            'mastercard-m' => ['mastercard', $this->appendChecksum('5134 5678 9012 34'), false],
            'mastercard-n' => ['mastercard', $this->appendChecksum('5134 5678 9012 3456 '), false],

            // amex tests
            'amex-a'       => ['amex', $this->appendChecksum('3334 5678 9012 34'), false],
            'amex-b'       => ['amex', $this->appendChecksum('3434 5678 9012 34'), true],
            'amex-c'       => ['amex', $this->appendChecksum('3534 5678 9012 34'), false],
            'amex-d'       => ['amex', $this->appendChecksum('3634 5678 9012 34'), false],
            'amex-e'       => ['amex', $this->appendChecksum('3734 5678 9012 34'), true],
            'amex-f'       => ['amex', $this->appendChecksum('3834 5678 9012 34'), false],
            'amex-g'       => ['amex', $this->appendChecksum('3434 5678 9012 3'), false],
            'amex-h'       => ['amex', $this->appendChecksum('3434 5678 9012 345'), false],

            // maestro check
            'maestro-a'    => ['maestro', $this->appendChecksum('5234 5678 901'), false],
            'maestro-b'    => ['maestro', $this->appendChecksum('6234 5678 901'), true],
            'maestro-c'    => ['maestro', $this->appendChecksum('7234 5678 901'), false],
            'maestro-d'    => ['maestro', $this->appendChecksum('5034 5678 901'), true],
            'maestro-e'    => ['maestro', $this->appendChecksum('5134 5678 901'), false],
            'maestro-f'    => ['maestro', $this->appendChecksum('5534 5678 901'), false],
            'maestro-g'    => ['maestro', $this->appendChecksum('5634 5678 901'), true],
            'maestro-h'    => ['maestro', $this->appendChecksum('5734 5678 901'), true],
            'maestro-i'    => ['maestro', $this->appendChecksum('5834 5678 901'), true],
            'maestro-j'    => ['maestro', $this->appendChecksum('5934 5678 901'), false],
            'maestro-l'    => ['maestro', $this->appendChecksum('5034 5678 9012 3456 78'), true],
            'maestro-m'    => ['maestro', $this->appendChecksum('5034 5678 9012 3456 789'), false],

            // diners club check
            'diner-a'      => ['dinersclub', $this->appendChecksum('3634 5678 9012 '), false],
            'diner-b'      => ['dinersclub', $this->appendChecksum('3634 5678 9012 3'), true],
            'diner-c'      => ['dinersclub', $this->appendChecksum('3634 5678 9012 3456 78'), true],
            'diner-d'      => ['dinersclub', $this->appendChecksum('3634 5678 9012 3456 789'), false],
            'diner-e'      => ['dinersclub', $this->appendChecksum('3834 5678 9012 34'), false],
            'diner-f'      => ['dinersclub', $this->appendChecksum('3834 5678 9012 345'), true],
            'diner-g'      => ['dinersclub', $this->appendChecksum('3934 5678 9012 3456 78'), true],
            'diner-h'      => ['dinersclub', $this->appendChecksum('3934 5678 9012 3456 789'), false],
            'diner-i'      => ['dinersclub', $this->appendChecksum('3004 5678 9012 34'), false],
            'diner-j'      => ['dinersclub', $this->appendChecksum('3004 5678 9012 345'), true],
            'diner-k'      => ['dinersclub', $this->appendChecksum('3014 5678 9012 345'), true],
            'diner-l'      => ['dinersclub', $this->appendChecksum('3044 5678 9012 345'), true],
            'diner-m'      => ['dinersclub', $this->appendChecksum('3054 5678 9012 345'), true],
            'diner-n'      => ['dinersclub', $this->appendChecksum('3064 5678 9012 345'), false],
            'diner-o'      => ['dinersclub', $this->appendChecksum('3054 5678 9012 3456 78'), true],
            'diner-p'      => ['dinersclub', $this->appendChecksum('3054 5678 9012 3456 789'), false],
            'diner-q'      => ['dinersclub', $this->appendChecksum('3095 5678 9012 34'), false],
            'diner-r'      => ['dinersclub', $this->appendChecksum('3095 5678 9012 345'), true],
            'diner-s'      => ['dinersclub', $this->appendChecksum('3095 5678 9012 3456 78'), true],
            'diner-t'      => ['dinersclub', $this->appendChecksum('3095 5678 9012 3456 789'), false],
            'diner-u'      => ['dinersclub', $this->appendChecksum('5434 5678 9012 345'), true],
            'diner-v'      => ['dinersclub', $this->appendChecksum('5534 5678 9012 345'), true],
            'diner-w'      => ['dinersclub', $this->appendChecksum('5434 5678 9012 3456 '), false],

            // multiple types allowed
            'multi-a'      => [['mastercard', 'visa'], $this->appendChecksum('5134 5678 9012 345'), true], // mastercard
            'multi-b'      => [['mastercard', 'visa'], $this->appendChecksum('4234 5678 9012 345'), true], // visa
            'multi-c'      => [['mastercard', 'visa'], $this->appendChecksum('3734 5678 9012 34'), false], // amex

            // unknown types are valid
            'unknown-a'    => ['amazon', $this->appendChecksum('5967 7583 9142 671'), true], // amazon is a visa card...
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
