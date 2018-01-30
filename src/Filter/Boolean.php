<?php

namespace Verja\Filter;

use Verja\Filter;
use Verja\Validator;

class Boolean extends Filter
{
    /** @var string[] */
    protected $stringTrue = ['1', 'true', 't', 'yes', 'y'];

    /** @var string[] */
    protected $stringFalse = ['0', 'false', 'f', 'no', 'n'];

    /**
     * Boolean constructor.
     *
     * @param string[] $stringTrue
     * @param string[] $stringFalse
     * @param bool     $overwrite   Overwrite the arrays instead of merging
     */
    public function __construct(array $stringTrue = [], array $stringFalse = [], $overwrite = false)
    {
        if ($overwrite) {
            $this->stringTrue = $stringTrue;
            $this->stringFalse = $stringFalse;
        } else {
            $this->stringTrue  = array_merge($this->stringTrue, $stringTrue);
            $this->stringFalse = array_merge($this->stringFalse, $stringFalse);
        }
    }

    /**
     * Filter $value
     *
     * @param mixed $value
     * @param array $context
     * @return boolean
     */
    public function filter($value, array $context = [])
    {
        Validator::assert(new Validator\Boolean($this->stringTrue, $this->stringFalse, true), $value);

        // the validator says it's either true or false string
        if (is_string($value)) {
            return in_array(strtolower($value), $this->stringTrue);
        }

        return (bool)$value;
    }
}
