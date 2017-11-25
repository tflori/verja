<?php

namespace Verja\Validator;

use Verja\Error;
use Verja\Validator;

class StrLen extends Validator
{
    /** @var int */
    protected $min;

    /** @var int */
    protected $max;

    /**
     * StrLen constructor.
     *
     * @param int $min
     * @param int $max
     */
    public function __construct(int $min, int $max = 0)
    {
        $this->min = $min;
        $this->max = $max;
    }


    /** {@inheritdoc} */
    public function validate($value, array $context = []): bool
    {
        $strLen = strlen($value);
        if ($strLen < $this->min) {
            $this->error = new Error(
                'STRLEN_TOO_SHORT',
                $value,
                sprintf('value should be at least %d characters long', $this->min),
                [ 'min' => $this->min, 'max' => $this->max ]
            );
            return false;
        } elseif ($this->max > 0 && $strLen > $this->max) {
            $this->error = new Error(
                'STRLEN_TOO_LONG',
                $value,
                sprintf('value should be maximal %d characters long', $this->max),
                [ 'min' => $this->min, 'max' => $this->max ]
            );
            return false;
        }

        return true;
    }
}
