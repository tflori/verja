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
     * @param FilterInterface $filter
     * @return $this
     */
    public function appendFilter(FilterInterface $filter)
    {
        return $this->addFilter($filter, false);
    }

    /**
     * Prepend $filter to the list of filters
     *
     * @param FilterInterface $filter
     * @return $this
     */
    public function prependFilter(FilterInterface $filter)
    {
        return $this->addFilter($filter, true);
    }

    /**
     * Add $filter to the list of filters
     *
     * Appends by default prepends when $prepend == true
     *
     * @param FilterInterface $filter
     * @param bool            $prepend
     * @return $this
     */
    public function addFilter(FilterInterface $filter, $prepend = false)
    {
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
//    public function addValidator(ValidatorInterface $validator)
//    {
//        return $this->appendValidator($validator);
//    }

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
}
