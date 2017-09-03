<?php

namespace Verja\Exception;

use Verja\Exception;

class FilterNotFound extends Exception
{
    /** @var string */
    protected $filter;

    /**
     * FilterNotFound constructor.
     *
     * @param string $filter
     */
    public function __construct(string $filter)
    {
        $this->filter = $filter;
        parent::__construct(sprintf('Filter \'%s\' not found', $filter));
    }

    /**
     * @return string
     */
    public function getFilter(): string
    {
        return $this->filter;
    }
}
