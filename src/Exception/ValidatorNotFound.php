<?php

namespace Verja\Exception;

use Verja\Exception;

class ValidatorNotFound extends Exception
{
    /** @var string */
    protected $validator;

    /**
     * FilterNotFound constructor.
     *
     * @param string $validator
     */
    public function __construct(string $validator)
    {
        $this->validator = $validator;
        parent::__construct(sprintf('Validator \'%s\' not found', $validator));
    }

    /**
     * @return string
     */
    public function getValidator(): string
    {
        return $this->validator;
    }
}
