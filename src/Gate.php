<?php

namespace Verja;

use Verja\Exception\InvalidValue;

class Gate
{
    /** @var Field[] */
    protected $fields = [];

    /** @var array */
    protected $rawData = [];

    /** @var string */
    protected $filteredDataHash;

    /** @var array */
    protected $filteredData = [];

    /** @var array */
    protected $errors = [];

    public function __construct(array $data = null)
    {
        if ($data) {
            $this->setData($data);
        }
    }

    /**
     * Alias for addFields
     *
     * @param array $fields
     * @return $this
     * @see Gate::addFields()
     */
    public function accepts(array $fields)
    {
        return $this->addFields($fields);
    }

    /**
     * Add an array of fields
     *
     * Definitions are defined as in addField.
     *
     * Accepts fields without filter and validator definitions as values. So the following examples are equal:
     *
     * `[ $key => [] ]`
     *
     * `[ $key => null ]`
     *
     * `[ $key ]`
     *
     * @param array $fields
     * @return $this
     * @see Gate::addField() how to pass definitions
     */
    public function addFields(array $fields)
    {
        foreach ($fields as $key => $field) {
            if (is_int($key)) {
                $this->addField($field); // field is the key in numeric arrays
            } else {
                $this->addField($key, $field);
            }
        }
        return $this;
    }

    /**
     * Alias for addField
     *
     * @param string $key
     * @param mixed  $field
     * @return $this
     * @see Gate::addField()
     */
    public function accept($key, $field = null)
    {
        return $this->addField($key, $field);
    }

    /**
     * Add an accepted field to this gate
     *
     * The definition can be a single validator or filter as string or object, an array of validators and filters
     * as string or object or an instance of Field.
     *
     * The following examples are equal:
     *
     * `(new Field)->addValidator('strLen:2:5')`
     *
     * `'strLen:2:5'`
     *
     * `['strLen:2:5']`
     *
     * `new Field(['strLen:2:5'])`
     *
     * @param string $key   The key in the data array
     * @param mixed  $field Definition of the field
     * @return $this
     */
    public function addField($key, $field = null)
    {
        if ($field instanceof Field) {
            $this->fields[$key] = $field;
        } else {
            $definitions = [];
            if (is_array($field)) {
                $definitions = $field;
            } elseif (is_string($field) || $field instanceof ValidatorInterface || $field instanceof FilterInterface) {
                $definitions = [$field];
            }
            $this->fields[$key] = new Field($definitions);
        }

        return $this;
    }

    /**
     * Set the data that should be covered (the context)
     *
     * @param array $data
     * @return $this
     */
    public function setData(array $data)
    {
        $this->rawData = $data;
        return $this;
    }

    /**
     * Get all data or the value for $key
     *
     * @param string $key
     * @param bool   $validate
     * @return array|mixed
     * @throws InvalidValue When value is invalid
     */
    public function getData(string $key = null, $validate = true)
    {
        if ($key !== null) {
            if (!isset($this->fields[$key])) {
                return null;
            }
            $fields = [$key => $this->fields[$key]];
        } else {
            $fields = $this->fields;
        }

        $result  = [];
        foreach ($fields as $k => $field) {
            $filtered = $field->filter(isset($this->rawData[$k]) ? $this->rawData[$k] : null, $this->rawData);

            if ($validate && !$field->validate($filtered, $this->rawData)) {
                if ($field->isRequired()) {
                    $errors = $field->getErrors();
                    if (count($errors) > 0) {
                        throw new InvalidValue(sprintf('Invalid %s: %s', $k, $errors[0]['message']));
                    }
                    throw new InvalidValue(sprintf('The value %s is not valid for %s', json_encode($filtered), $k));
                } else {
                    $filtered = null;
                }
            }

            if ($key !== null) {
                return $filtered;
            }

            $result[$k] = $filtered;
        }

        return $result;
    }

    /**
     * Alias for getData
     *
     * @param string $key
     * @return mixed
     * @see Gate::getData()
     * @codeCoverageIgnore trivial
     */
    public function get(string $key = null)
    {
        return $this->getData($key);
    }

    /**
     * Alias for getData
     *
     * @param string $key
     * @return mixed
     * @see Gate::getData()
     * @codeCoverageIgnore trivial
     */
    public function __get(string $key)
    {
        return $this->getData($key);
    }

    public function validate(array $data = null)
    {
        if ($data) {
            $this->setData($data);
        }

        $valid = true;
        $this->errors = [];
        $filtered = $this->getData(null, false);
        foreach ($this->fields as $key => $field) {
            if (empty($filtered[$key]) && !$field->isRequired()) {
                continue;
            }

            if (!$field->validate($filtered[$key], $this->rawData)) {
                $valid = false;
                $this->errors[$key] = $field->getErrors();
            }
        }
        return $valid;
    }

    /**
     * Get all reported errors
     *
     * @return array
     * @codeCoverageIgnore trivial
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
