<?php

namespace Verja;

class Error
{
    /** @var string */
    public $key;

    /** @var  string */
    public $message;

    /** @var array */
    public $parameters;

    /**
     * Error constructor.
     *
     * @param string $key
     * @param mixed  $value
     * @param string $message
     * @param array  $parameters
     */
    public function __construct(string $key, $value, string $message = null, array $parameters = null)
    {
        $this->key   = $key;

        if ($message !== null) {
            $this->message = $message;
        } else {
            $this->message = sprintf('%s %s', json_encode($value), $key);
        }

        if ($parameters !== null) {
            $this->parameters = $parameters;
        }
        $this->parameters['value'] = $value;
    }
}
