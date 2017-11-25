<?php

namespace Verja\Validator;

use Verja\Error;
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

    /** {@inheritdoc} */
    public function validate($value, array $context = []): bool
    {
        if (strpos($value, $this->subString) === false) {
            $this->error = new Error(
                'NOT_CONTAINS',
                $value,
                sprintf('value should contain "%s"', $this->subString),
                [ 'subString' => $this->subString ]
            );
            return false;
        }

        return true;
    }

    /** {@inheritdoc} */
    public function getInverseError($value)
    {
        return new Error(
            'CONTAINS',
            $value,
            sprintf('value should not contain "%s"', $this->subString),
            [ 'subString' => $this->subString ]
        );
    }
}
