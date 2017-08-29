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
        array_push($this->filters, $filter);
        return $this;
    }

    /**
     * Prepend $filter to the list of filters
     *
     * @param FilterInterface $filter
     * @return $this
     */
    public function prependFilter(FilterInterface $filter)
    {
        array_unshift($this->filters, $filter);
        return $this;
    }

    /**
     * Alias for appendFilter
     *
     * @param FilterInterface $filter
     * @return Field
     * @see Field::appendFilter()
     */
    public function addFilter(FilterInterface $filter)
    {
        return $this->appendFilter($filter);
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
