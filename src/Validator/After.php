<?php

namespace Verja\Validator;

use Verja\Error;
use Verja\Validator;

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
class After extends Validator
{
    /** @var \DateTime */
    protected $dateTime;

    /**
     * After constructor.
     *
     * @param \DateTime|string $dateTime
     * @throws \InvalidArgumentException
     */
    public function __construct($dateTime)
    {
        if (!$dateTime instanceof \DateTime) {
            if (is_string($dateTime)) {
                $dateTime = new \DateTime($dateTime);
            } else {
                throw new \InvalidArgumentException('$dateTime has to be a DateTime object or time string');
            }
        }

        $this->dateTime = $dateTime;
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
        if (is_string($value)) {
            try {
                $original = $value;
                $value = new \DateTime($value);
            } catch (\Exception $e) {
                // results in false after next if
            }
        }

        if ($value instanceof \DateTime) {
            if ($this->floatDiff($value, $this->dateTime) > 0) {
                return true;
            }
            $this->error = new Error(
                'NOT_AFTER',
                $original ?? $value,
                sprintf('value should be after %s', $this->dateTime->format('c')),
                ['dateTime' => $this->dateTime]
            );
            return false;
        }

        $this->error = new Error('NO_DATE', $value, 'value should be a valid date', ['dateTime' => $this->dateTime]);
        return false;
    }

    protected function floatDiff(\DateTime $dt1, \DateTime $dt2)
    {
        return (float)$dt1->format('U.u') - (float)$dt2->format('U.u');
    }
}
