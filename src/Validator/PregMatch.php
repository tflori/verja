<?php

namespace Verja\Validator;

use Verja\Validator;

class PregMatch extends Validator
{
    /** @var string */
    protected $pattern;

    /**
     * PregMatch constructor.
     *
     * @param string $pattern
     */
    public function __construct($pattern)
    {
        $this->pattern = $pattern;
    }

    /** {@inheritdoc} */
    public function validate($value, array $context = []): bool
    {
        if (preg_match($this->pattern, $value)) {
            return true;
        }

        $this->error = $this->buildError(
            'NO_MATCH',
            $value,
            sprintf('value should match "%s"', $this->pattern),
            [ 'pattern' => $this->pattern ]
        );
        return false;
    }

    /** {@inheritdoc} */
    public function getInverseError($value)
    {
        return $this->buildError(
            'MATCHES',
            $value,
            sprintf('value should not match "%s"', $this->pattern),
            [ 'pattern' => $this->pattern ]
        );
    }
}
