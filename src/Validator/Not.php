<?php

namespace Verja\Validator;

use Verja\Validator;
use Verja\ValidatorInterface;

class Not extends Validator
{
    /** @var ValidatorInterface */
    protected $validator;

    /**
     * Not constructor.
     *
     * @param ValidatorInterface|string $validator
     */
    public function __construct($validator)
    {
        if (!$validator instanceof ValidatorInterface) {
            $validator = Validator::fromString($validator);
        }

        $this->validator = $validator;
    }

    /**
     * Validate $value
     *
     * @param mixed $value
     * @param array $context
     * @return bool
     */
    public function validate($value, array $context = []): bool
    {
        if ($this->validator->validate($value, $context)) {
            $this->error = $this->validator->getInverseError($value);
            return false;
        }

        return true;
    }
}
