<?php

namespace Verja\Validator;

use Verja\Error;
use Verja\Validator;

class Date extends Validator
{
    /** @var string */
    protected $format;

    /** @var bool */
    protected $strict;

    /**
     * Date constructor.
     *
     * When strict is set to true the resulting date will be formatted back using the given format and the value
     * has to equal this value. This is helpful to prevent month and day interchanges but will fail for some natural
     * date formats like `'dS of F Y' => '21st of January 2016`.
     *
     * @param string $format
     * @param bool   $strict
     */
    public function __construct(string $format = null, bool $strict = false)
    {
        $this->format = $format;
        $this->strict = $strict;
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
        if ($value instanceof \DateTime) {
            return true;
        }

        if (is_null($this->format) && strtotime($value) === false) {
            $this->error = new Error('NO_DATE', $value, 'value should be a valid date');
            return false;
        }

        if (!is_null($this->format)) {
            $date = date_create_from_format($this->format, $value);
            if ($date === false || $this->strict && $date->format($this->format) !== $value) {
                $this->error = new Error(
                    'NO_FORMATTED_DATE',
                    $value,
                    sprintf('value is not a valid date in format %s', $this->format),
                    [ 'format' => $this->format, 'strict' => $this->strict ]
                );
                return false;
            }
        }

        return true;
    }

    public function getInverseError($value)
    {
        return is_null($this->format) ?
            new Error('IS_DATE', $value, 'value should not be a valid date') :
            new Error(
                'IS_FORMATTED_DATE',
                $value,
                sprintf(
                    'value should not be valid date in format %s',
                    $this->format
                ),
                [ 'format' => $this->format, 'strict' => $this->strict ]
            );
    }
}
