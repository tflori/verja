<?php

namespace Verja\Validator;

use Verja\Validator;

class Contains extends Validator
{
    /** @var string */
    protected $subString;

    /**
     * Contains constructor.
     *
     * @param string $subString
     */
    public function __construct(string $subString)
    {
        $this->subString = $subString;
    }

    /**
     * Validate $value
     *
     * @param mixed $value
     * @return bool
     */
    public function validate($value): bool
    {
        return strpos($value, $this->subString) !== false;
    }
}