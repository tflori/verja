<?php

namespace Verja\Test\Examples\CustomFilter;

use Verja\Filter;
use Verja\ValidatorInterface;

class Validated extends Filter
{
    /**
     * Validated constructor.
     *
     * @param ValidatorInterface|string|callable $validator
     */
    public function __construct($validator)
    {
        $this->setValidatedBy($validator);
    }

    /**
     * Filter $value
     *
     * @param mixed $value
     * @param array $context
     * @return mixed
     */
    public function filter($value, array $context = [])
    {
        return $value;
    }
}
