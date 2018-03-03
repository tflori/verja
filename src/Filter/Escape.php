<?php

namespace Verja\Filter;

use Verja\Filter;

class Escape extends Filter
{
    /** @var bool */
    protected $doubleEncode = false;

    /** @var bool */
    protected $specialChars = true;

    /**
     * Escape constructor.
     *
     * @param bool $doubleEncode
     * @param bool $specialChars
     */
    public function __construct(bool $doubleEncode = false, bool $specialChars = true)
    {
        $this->doubleEncode = $doubleEncode;
        $this->specialChars = $specialChars;
    }

    /**
     * Filter $value
     *
     * @param mixed $value
     * @param array $context
     * @return mixed
     */
    public function filter($value, array $context = [])
    {
        if (!is_string($value)) {
            return $value;
        }

        return $this->specialChars ?
            htmlspecialchars($value, ENT_COMPAT, ini_get('default_charset'), $this->doubleEncode) :
            htmlentities($value, ENT_COMPAT, ini_get('default_charset'), $this->doubleEncode);
    }
}
