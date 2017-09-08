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

    /** {@inheritdoc} */
    public function validate($value, array $context = []): bool
    {
        if (strpos($value, $this->subString) === false) {
            $this->error = $this->buildError(
                'NOT_CONTAINS',
                $value,
                ['subString' => $this->subString],
                sprintf('%s should contain %s', json_encode($value), json_encode($this->subString))
            );
            return false;
        }

        return true;
    }

    /** {@inheritdoc} */
    public function getInverseError($value)
    {
        return $this->buildError(
            'CONTAINS',
            $value,
            ['subString' => $this->subString],
            sprintf('%s should not contain %s', json_encode($value), json_encode($this->subString))
        );
    }
}
