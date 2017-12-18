<?php

namespace Verja\Validator;

use Verja\Error;
use Verja\Validator;

class Boolean extends Validator
{
    /** @var string[] */
    protected $stringTrue;

    /** @var string[] */
    protected $stringFalse;

    /**
     * Boolean constructor.
     *
     * @param string[] $stringTrue
     * @param string[] $stringFalse
     */
    public function __construct(
        array $stringTrue = ['1', 'true', 't', 'yes', 'y'],
        array $stringFalse = ['0', 'false', 'f', 'no', 'n']
    ) {
        $this->stringTrue  = $stringTrue;
        $this->stringFalse = $stringFalse;
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
        if (!is_bool($value) && !is_int($value) &&
            (!is_string($value) || !in_array($value, $this->stringTrue) && !in_array($value, $this->stringFalse))
        ) {
            $this->error = new Error('NOT_BOOLEAN', $value, 'value should be a boolean');
            return false;
        }

        return true;
    }

    public function getInverseError($value)
    {
        return new Error('IS_BOOLEAN', $value, 'value should not be a boolean');
    }
}
