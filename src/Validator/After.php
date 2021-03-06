<?php

namespace Verja\Validator;

/**
 * Validator\After
 *
 * Validate that a datetime is after the given datetime.
 *
 * @WARNING Both values can be strings and will then be parsed by DateTime constructor. Use the DateTime filter before
 *          is highly recommended.
 *
 * @package Verja\Validator
 * @author  Thomas Flori <thflori@gmail.com>
 */
class After extends TemporaAbstract
{
    protected $errorKey = 'NOT_AFTER';
    protected $errorMessage = 'value should be after %s';

    protected function validateDateTime(\DateTime $value)
    {
        return $this->floatDiff($value, $this->dateTime) >= 0;
    }
}
