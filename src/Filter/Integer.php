<?php

namespace Verja\Filter;

use Verja\Filter;
use Verja\Gate;
use Verja\Validator;

class Integer extends Filter
{
    /**
     * Filter $value
     *
     * @param mixed $value
     * @param array $context
     * @return mixed
     */
    public function filter($value, array $context = [])
    {
        Gate::assert(new Validator\Integer(), $value);

        return (int)((double)$value);
    }
}
