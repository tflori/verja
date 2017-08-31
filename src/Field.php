<?php

namespace Verja;

class Field
{
    /** @var FilterInterface[] */
    protected $filters = [];

    /** @var ValidatorInterface[] */
    protected $validators = [];

    /**
     * Append $filter to the list of filters
     *
     * @param FilterInterface|string $filter
     * @return $this
     */
    public function appendFilter($filter)
    {
        return $this->addFilter($filter, false);
    }

    /**
     * Prepend $filter to the list of filters
     *
     * @param FilterInterface|string $filter
     * @return $this
     */
    public function prependFilter($filter)
    {
        return $this->addFilter($filter, true);
    }

    /**
     * Add $filter to the list of filters
     *
     * Appends by default prepends when $prepend == true
     *
     * @param FilterInterface|string $filter
     * @param bool                   $prepend
     * @return $this
     */
    public function addFilter($filter, $prepend = false)
    {
        if (!$filter instanceof FilterInterface) {
            $filter = Filter::fromString($filter);
        }

        if ($prepend) {
            array_unshift($this->filters, $filter);
        } else {
            array_push($this->filters, $filter);
        }

        return $this;
    }

//    public function appendValidator(ValidatorInterface $validator)
//    {
//        array_push($this->validators, $validator);
//        return $this;
//    }
//
//    public function prependValidator(ValidatorInterface $validator)
//    {
//        array_unshift($this->validators, $validator);
//        return $this;
//    }
//
    public function addValidator(ValidatorInterface $validator)
    {
        array_push($this->validators, $validator);
        return $this;
    }

    /**
     * Filter $value with predefined filters
     *
     * @param mixed $value
     * @return mixed
     */
    public function filter($value)
    {
        foreach ($this->filters as $filter) {
            $value = $filter->filter($value);
        }
        return $value;
    }

    /**
     * Validate $value with predefined validators
     *
     * @param $value
     * @return bool
     */
    public function validate($value)
    {
        $valid = true;
        foreach ($this->validators as $validator) {
            if (!$validator->validate($value)) {
                $valid = false;
            }
        }
        return $valid;
    }
}
