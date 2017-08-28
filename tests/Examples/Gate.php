<?php

namespace Verja\Test\Examples;

/**
 * Test Gate with diagnostic functions
 *
 * @package Verja\Test\Examples
 * @author  Thomas Flori <thflori@gmail.com>
 */
class Gate extends \Verja\Gate
{
    public function getFields()
    {
        return $this->fields;
    }

    public function getRawData()
    {
        return $this->rawData;
    }
}
