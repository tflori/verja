<?php

namespace Verja;

use Verja\Exception\FilterNotFound;
use Verja\Exception\NotFound;
use Verja\Exception\ValidatorNotFound;

class Field
{
    /** @var FilterInterface[] */
    protected $filters = [];

    /** @var ValidatorInterface[] */
    protected $validators = [];

    /**
     * Field constructor.
     *
     * Adds filters and validators given in $definitions in the exact order.
     *
     * @param array $definitions
     * @throws NotFound
     */
    public function __construct(array $definitions = [])
    {
        $notFound = array_intersect(
            $this->addFiltersFromArray($definitions),
            $this->addValidatorsFromArray($definitions)
        );

        if (count($notFound) > 0) {
            throw new NotFound(sprintf(
                'No filter or validator named \'%s\' found',
                reset($notFound)
            ));
        }
    }

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
     * @param bool $prepend
     * @return $this
     */
    public function addFilter($filter, $prepend = false)
    {
        if (is_string($filter)) {
            $filter = Filter::fromString($filter);
        }

        if (!$filter instanceof FilterInterface) {
            return $this;
        }

        if ($prepend) {
            array_unshift($this->filters, $filter->assign($this));
        } else {
            array_push($this->filters, $filter->assign($this));
        }

        return $this;
    }

    /**
     * Add Filters from $filterDefinitions
     *
     * Returns an array of Classes that where not found.
     *
     * @param array $filterDefinitions
     * @return array
     */
    public function addFiltersFromArray(array $filterDefinitions)
    {
        $notFound = [];

        foreach ($filterDefinitions as $filterDefinition) {
            try {
                $this->addFilter($filterDefinition);
            } catch (FilterNotFound $exception) {
                $notFound[] = $exception->getFilter();
            }
        }

        return $notFound;
    }

    /**
     * Append $validator to the list of validators
     *
     * @param ValidatorInterface|string $validator
     * @return $this
     */
    public function appendValidator($validator)
    {
        return $this->addValidator($validator, false);
    }

    /**
     * Prepend $validator to the list of validators
     *
     * @param ValidatorInterface|string $validator
     * @return $this
     */
    public function prependValidator($validator)
    {
        return $this->addValidator($validator, true);
    }

    /**
     * Add $validator to the list of filters
     *
     * Appends by default prepends when $prepend == true
     *
     * @param ValidatorInterface|string $validator
     * @param bool $prepend
     * @return $this
     */
    public function addValidator($validator, $prepend = false)
    {
        if (is_string($validator)) {
            $validator = Validator::fromString($validator);
        }

        if (!$validator instanceof ValidatorInterface) {
            return $this;
        }

        if ($prepend) {
            array_unshift($this->validators, $validator->assign($this));
        } else {
            array_push($this->validators, $validator->assign($this));
        }
        return $this;
    }

    /**
     * Add Filters from $validatorDefinitions
     *
     * Returns an array of Classes that where not found.
     *
     * @param array $validatorDefinitions
     * @return array
     */
    public function addValidatorsFromArray(array $validatorDefinitions)
    {
        $notFound = [];

        foreach ($validatorDefinitions as $validatorDefinition) {
            try {
                $this->addValidator($validatorDefinition, false);
            } catch (ValidatorNotFound $exception) {
                $notFound[] = $exception->getValidator();
            }
        }

        return $notFound;
    }

    /**
     * Filter $value with predefined filters
     *
     * Some filters may need context pass it with $context.
     *
     * @param mixed $value
     * @param array $context
     * @return mixed
     */
    public function filter($value, array $context = [])
    {
        foreach ($this->filters as $filter) {
            $value = $filter->filter($value, $context);
        }
        return $value;
    }

    /**
     * Validate $value with predefined validators
     *
     * Some validators may need context pass it with $context.
     *
     * @param mixed $value
     * @param array $context
     * @return bool
     */
    public function validate($value, array $context = [])
    {
        $valid = true;
        foreach ($this->validators as $validator) {
            if (!$validator->validate($value, $context)) {
                $valid = false;
            }
        }
        return $valid;
    }
}
