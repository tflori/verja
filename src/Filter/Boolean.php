<?php

namespace Verja\Filter;

use Verja\Filter;
use Verja\Validator;

class Boolean extends Filter
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
     * Filter $value
     *
     * @param mixed $value
     * @param array $context
     * @return boolean
     */
    public function filter($value, array $context = [])
    {
        Validator::assert(new Validator\Boolean($this->stringTrue, $this->stringFalse), $value);
        switch (gettype($value)) {
            case 'boolean':
                return $value;

            case 'double':
            case 'integer':
                return (bool)$value;

            case 'string':
                return in_array(strtolower($value), $this->stringTrue) ?:
                       ( in_array(strtolower($value), $this->stringFalse) ? false :
                         $value
                       );

            default:
                return $value;
        }
    }
}
