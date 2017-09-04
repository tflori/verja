<?php

namespace Verja;

class Gate
{
    /** @var Field[] */
    protected $fields = [];

    /** @var array */
    protected $rawData = [];

    public function __construct(array $data = null)
    {
        if ($data) {
            $this->setData($data);
        }
    }

    public function accepts(array $fields)
    {
        return $this->addFields($fields);
    }

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

    public function accept($key, $field = null)
    {
        return $this->addField($key, $field);
    }

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

    public function setData(array $data)
    {
        $this->rawData = $data;
        return $this;
    }

    public function getData(array $data = null)
    {
//        if ($data) {
//            $this->setData($data);
//        }

        $result  = [];
        $rawData = $this->rawData;
        foreach ($this->fields as $key => $field) {
            $result[$key] = isset($rawData[$key]) ? $rawData[$key] : null; // $field->filter();
        }

        return $result;
    }
}
