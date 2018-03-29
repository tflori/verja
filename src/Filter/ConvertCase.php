<?php

namespace Verja\Filter;

use Verja\Filter;

class ConvertCase extends Filter
{
    /** @var int */
    protected $mode;

    /**
     * ConvertCase constructor.
     *
     * @param int|string $mode
     */
    public function __construct($mode)
    {
        if (is_int($mode)) {
            $this->mode = $mode;
        }

        switch ($mode) {
            case 'upper':
                $this->mode = MB_CASE_UPPER;
                break;
            case 'lower':
                $this->mode = MB_CASE_LOWER;
                break;
            case 'title':
                $this->mode = MB_CASE_TITLE;
        }
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

        return mb_convert_case($value, $this->mode);
    }
}
