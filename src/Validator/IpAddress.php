<?php

namespace Verja\Validator;

use Verja\Error;
use Verja\Validator;

class IpAddress extends Validator
{
    const VERSION_ANY = 'any';
    const VERSION_4 = 'v4';
    const VERSION_6 = 'v6';

    const RANGE_ANY = 'any';
    const RANGE_PUBLIC = 'public';
    const RANGE_UNRESERVED = 'unreserved';
    const RANGE_PRIVATE = 'private';

    /** @var string */
    protected $version;

    /** @var string */
    protected $range;

    /**
     * IpAddress constructor.
     *
     * @param string $version 'any' (default), 'v4' or 'v6'
     * @param string $range 'any' (default), 'public', 'public,unreserved' or 'private'
     */
    public function __construct(string $version = self::VERSION_ANY, string $range = self::RANGE_ANY)
    {
        $this->version = $version;
        $this->range = $range;
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
        $flags = 0;

        $ipName = 'ip address';
        if ($this->version === self::VERSION_6) {
            $flags = $flags | FILTER_FLAG_IPV6;
            $ipName = 'ip-v6 address';
        } elseif ($this->version === self::VERSION_4) {
            $flags = $flags | FILTER_FLAG_IPV4;
            $ipName = 'ip-v4 address';
        }

        $parameters = [
            'version' => $this->version,
            'range' => $this->range,
        ];

        if (!filter_var($value, FILTER_VALIDATE_IP, $flags)) {
            // not a valid ip address
            $this->error = new Error(
                'NO_IP_ADDRESS',
                $value,
                sprintf('value should be an %s', $ipName),
                $parameters
            );
            return false;
        }

        if ($this->range !== self::RANGE_ANY) {
            if (strpos($this->range, self::RANGE_PUBLIC) !== false) {
                $flags = $flags | FILTER_FLAG_NO_PRIV_RANGE;
                if (!filter_var($value, FILTER_VALIDATE_IP, $flags)) {
                    // not a public ip address
                    $this->error = new Error(
                        'NO_PUBLIC_IP_ADDRESS',
                        $value,
                        sprintf('value should be a public %s', $ipName),
                        $parameters
                    );
                    return false;
                } elseif (strpos($this->range, self::RANGE_UNRESERVED) !== false) {
                    $flags = $flags | FILTER_FLAG_NO_RES_RANGE;
                    if (!filter_var($value, FILTER_VALIDATE_IP, $flags)) {
                        // reserved ip address
                        $this->error = new Error(
                            'IS_RESERVED_IP_ADDRESS',
                            $value,
                            sprintf('value should not be a reserved %s', $ipName),
                            $parameters
                        );
                        return false;
                    }
                }
            }

            if ($this->range === self::RANGE_PRIVATE) {
                $flags = $flags | FILTER_FLAG_NO_PRIV_RANGE;
                if (filter_var($value, FILTER_VALIDATE_IP, $flags)) {
                    // public ip address
                    $this->error = new Error(
                        'IS_PUBLIC_IP_ADDRESS',
                        $value,
                        sprintf('value should not be a public %s', $ipName),
                        $parameters
                    );
                    return false;
                }
            }
        }

        return true;
    }

    public function getInverseError($value)
    {
        return new Error('IS_IP_ADDRESS', $value, 'value should not be an ip address');
    }
}
