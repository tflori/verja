<?php

namespace Verja\Validator;

use Verja\Error;
use Verja\Validator;

class Equals extends Validator
{
    /** @var string */
    protected $opposite;

    /** @var bool */
    protected $jsonEncode;

    /**
     * Equals constructor.
     *
     * @param string $opposite
     * @param bool   $jsonEncode
     */
    public function __construct(string $opposite, bool $jsonEncode = true)
    {
        $this->opposite = $opposite;
        $this->jsonEncode = $jsonEncode;
    }

    /** {@inheritdoc} */
    public function validate($value, array $context = []): bool
    {
        $opposite = isset($context[$this->opposite]) ? $context[$this->opposite] : null;

        // simple equality
        if ($value == $opposite) {
            return true;
        }

        // equality by json encode / decode
        if ($this->jsonEncode) {
            $valueJson = json_encode($value);
            $oppositeJson = json_encode($opposite);
            if ($valueJson === $oppositeJson || json_decode($valueJson) == json_decode($oppositeJson)) {
                return true;
            }
        }

        $this->error = new Error(
            'NOT_EQUAL',
            $value,
            sprintf('value should be equal to contexts %s', $this->opposite),
            [ 'opposite' => $this->opposite, 'jsonEncode' => $this->jsonEncode ]
        );
        return false;
    }

    /** {@inheritdoc} */
    public function getInverseError($value)
    {
        return new Error(
            'EQUALS',
            $value,
            sprintf('value should not be equal to contexts %s', $this->opposite),
            [ 'opposite' => $this->opposite, 'jsonEncode' => $this->jsonEncode ]
        );
    }
}
