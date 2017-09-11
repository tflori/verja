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

    /** @var array */
    protected $filterCache = [];

    /** @var array */
    protected $validationCache = [];

    /** @var array */
    protected $errors;

    /** @var bool */
    protected $required = false;

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
        $p = array_search('required', $definitions);
        if ($p !== false) {
            array_splice($definitions, $p, 1);
            $this->required();
        }

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
     * Set required flag
     *
     * @param bool $required
     * @return $this
     */
    public function required($required = true)
    {
        $this->required = $required;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return $this->required;
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

        if (is_callable($filter)) {
            $filter = new Filter\Callback($filter);
        }

        if (!$filter instanceof FilterInterface) {
            return $this;
        }

        if (count($this->filterCache) > 0) {
            $this->filterCache = [];
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

        if (is_callable($validator)) {
            $validator = new Validator\Callback($validator);
        }

        if (!$validator instanceof ValidatorInterface) {
            return $this;
        }

        if (count($this->validationCache) > 0) {
            $this->validationCache = [];
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
        try {
            $hash = md5(serialize([$value, $context]));
            if (isset($this->filterCache[$hash])) {
                return $this->filterCache[$hash];
            }
        } catch (\Exception $exception) {
        }

        foreach ($this->filters as $filter) {
            $value = $filter->filter($value, $context);
        }

        if (isset($hash)) {
            $this->filterCache[$hash] = $value;
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
        try {
            $hash = md5(serialize([$value, $context]));
            if (isset($this->validationCache[$hash])) {
                return $this->validationCache[$hash];
            }
        } catch (\Exception $exception) {
        }


        $valid = true;
        $this->errors = [];
        foreach ($this->validators as $validator) {
            if (!$validator->validate($value, $context)) {
                $valid = false;
                if ($error = $validator->getError()) {
                    $this->errors[] = $error;
                }
            }
        }

        if (isset($hash)) {
            $this->validationCache[$hash] = $valid;
        }
        return $valid;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
