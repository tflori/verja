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
     * Quick assertion with filters and validators
     *
     * Asserts that $value in $context can be filtered and validated by $field.
     *
     * If not an InvalidValue exception is thrown.
     *
     * Field can be defined as for addField.
     *
     * @param mixed $field
     * @param mixed $value
     * @param array $context
     * @return mixed
     * @throws InvalidValue
     * @see Gate::addField()
     */
    public static function assert($field, $value, array $context = [])
    {
        if (!$field instanceof Field) {
            $definitions = [];
            if (is_array($field)) {
                $definitions = $field;
            } elseif (is_string($field) ||
                      $field instanceof ValidatorInterface ||
                      $field instanceof FilterInterface ||
                      is_callable($field)
            ) {
                $definitions = [$field];
            }
            $field = new Field($definitions);
        }

        $filtered = $field->filter($value, $context);
        if (!$field->validate($filtered, $context)) {
            $errors = $field->getErrors();
            if (count($errors) === 1) {
                throw new InvalidValue(sprintf('Assertion failed: %s', $errors[0]->message), ...$errors);
            } elseif (count($errors) > 1) {
                // Ignoring coverage because of error in coverage analysis
                // @codeCoverageIgnoreStart
                throw new InvalidValue(sprintf(
                    'Assertion failed: %s',
                    implode('; ', array_map(function (Error $error) {
                        return $error->message;
                    }, $errors))
                ), ...$errors);
                // @codeCoverageIgnoreEnd
            } else {
                throw new InvalidValue(sprintf(
                    'Failed asserting that %s is valid (unknown error)',
                    json_encode($value)
                ));
            }
        }

        return $filtered;
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
            $filtered = $field->filter($this->rawData[$k] ?? null, $this->rawData);

            if ($validate && !$field->validate($this->rawData[$k] ?? null, $this->rawData)) {
                if ($field->isRequired()) {
                    $errors = $field->getErrors();
                    if (count($errors) > 0) {
                        throw new InvalidValue(sprintf('Invalid %s: %s', $k, $errors[0]->message), ...$errors);
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
     * @see                Gate::getData()
     * @codeCoverageIgnore trivial
     * @throws InvalidValue
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
     * @see                Gate::getData()
     * @codeCoverageIgnore trivial
     * @throws InvalidValue
     */
    public function __get(string $key)
    {
        return $this->getData($key);
    }

    /**
     * Validate $data or previously stored data
     *
     * @param array $data
     * @return bool
     */
    public function validate(array $data = null)
    {
        if ($data) {
            $this->setData($data);
        }

        $valid = true;
        $this->errors = [];
        foreach ($this->fields as $key => $field) {
            if (empty($this->rawData[$key]) && !$field->isRequired()) {
                continue;
            }

            if (!$field->validate($this->rawData[$key] ?? null, $this->rawData)) {
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
