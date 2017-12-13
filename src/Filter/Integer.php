<?php

namespace Verja\Filter;

use Verja\Filter;

class Integer extends Filter
{
    public function __construct()
    {
        $this->setValidatedBy(new \Verja\Validator\Integer());
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
        return (!is_numeric($value) || (int)$value != (double)$value) ? $value : (int)$value;
    }
}
