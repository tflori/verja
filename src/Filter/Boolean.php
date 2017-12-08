<?php

namespace Verja\Filter;

use Verja\Filter;

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
        switch (gettype($value)) {
            case 'boolean':
                return $value;

            case 'double':
            case 'integer':
                return $value > 0; // different to (bool)-1 this will return false

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
