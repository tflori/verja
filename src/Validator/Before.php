<?php

namespace Verja\Validator;

/**
 * Validator\Before
 *
 * Validate that a datetime is before the given datetime.
 *
 * @WARNING Both values can be strings and will then be parsed by DateTime constructor. Use the DateTime filter before
 *          is highly recommended.
 *
 * @package Verja\Validator
 * @author  Thomas Flori <thflori@gmail.com>
 */
class Before extends TemporaAbstract
{
    protected $errorKey = 'NOT_BEFORE';
    protected $errorMessage = 'value should be before %s';

    protected function validateDateTime(\DateTime $value)
    {
        return $this->floatDiff($value, $this->dateTime) <= 0;
    }
}
