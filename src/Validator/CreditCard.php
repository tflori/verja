<?php

namespace Verja\Validator;

use Verja\Error;
use Verja\Validator;

/**
 * Class CreditCard
 *
 * NOTE: Diners Club enRoute can not be validated with this class. They where not issued since 1992 so no card should
 * be valid anymore.
 *
 * You can extend these class and overwrite `protected static $cardTypes` to get more or different type validations.
 *
 * @package Verja\Validator
 * @author  Thomas Flori <thflori@gmail.com>
 */
class CreditCard extends Validator
{
    const TYPE_VISA             = 'visa';
    const TYPE_MASTER_CARD      = 'mastercard';
    const TYPE_AMERICAN_EXPRESS = 'amex';
    const TYPE_MAESTRO          = 'maestro';
    const TYPE_DINERSCLUB       = 'dinersclub';

    /** @var array  */
    protected $types = [];

    // each card has an array of definitions
    // each definition can be a regular expression or an array of length definition and range definition
    // length definition can be an array or a fix length
    /** @var array */
    protected static $cardTypes = [
        self::TYPE_VISA             => ['/^4\d{12}(\d{3}){0,2}$/'],
        self::TYPE_MASTER_CARD      => [[16, [51, 55]], [16, [2221, 2720]]],
        self::TYPE_AMERICAN_EXPRESS => ['/^3[47]\d{13}$/'],
        self::TYPE_MAESTRO          => ['/^6\d{11,18}$/', '/^50\d{10,17}$/', [[12, 19], [56, 58]]],
        self::TYPE_DINERSCLUB       => [
            '/^36\d{12,17}$/', '/^3095\d{12,15}$/',
            [[16, 19], [300, 305]], [16, [54, 55]], [[16, 19], [38, 39]]
        ],
    ];

    /**
     * CreditCard constructor.
     *
     * @param array|string $types
     */
    public function __construct($types = [])
    {
        $this->types = is_string($types) ? func_get_args() : $types;
    }


    /**
     * Validate $value
     *
     * @param mixed $value
     * @param array $context
     * @return bool
     */
    public function validate($value, array $context = []): bool
    {
        if (!is_string($value)) {
            return false;
        }

        // strip spaces
        $number = str_replace(' ', '', $value);

        if (!preg_match('/^\d{12,}$/', $number) || !$this->validateLuhn($number)) {
            $this->error = new Error('NO_CREDIT_CARD', $value, 'value should be a valid credit card number');
            return false;
        }

        if (!empty($this->types) && !$this->validateTypes($number)) {
            $this->error = new Error(
                'WRONG_CREDIT_CARD',
                $value,
                sprintf('value should be a credit card of type %s', implode(' or ', $this->types)),
                ['types' => $this->types]
            );
            return false;
        }

        return true;
    }

    public function getInverseError($value)
    {
        return new Error(
            'CREDIT_CARD',
            $value,
            sprintf('value should not be a credit card of type %s', implode(' or ', $this->types)),
            ['types' => $this->types]
        );
    }

    protected function validateLuhn(string $number): bool
    {
        $sum = '';
        $revNumber = strrev($number);
        $len = strlen($number);

        for ($i = 0; $i < $len; $i++) {
            $sum .= $i & 1 ? $revNumber[$i] * 2 : $revNumber[$i];
        }

        return array_sum(str_split($sum)) % 10 === 0;
    }

    protected function validateTypes(string $number): bool
    {
        foreach ($this->types as $type) {
            // types that we can't validate are valid
            if (!isset(static::$cardTypes[$type])) {
                return true;
            }

            // validate each card definition
            foreach (static::$cardTypes[$type] as $def) {
                if (is_string($def) && preg_match($def, $number)) {
                    return true;
                } elseif (is_array($def)) {
                    list($lenDef, $range) = $def;

                    // validate length
                    if (is_int($lenDef)) {
                        $lenDef = [$lenDef, $lenDef];
                    }
                    if (!Validator::strLen($lenDef[0], $lenDef[1])->validate($number)) {
                        continue;
                    }

                    // validate prefix
                    $prefix = (int)substr($number, 0, strlen($range[0]));
                    if (Validator::between($range[0], $range[1])->validate($prefix)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
