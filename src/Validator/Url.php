<?php

namespace Verja\Validator;

use Verja\Error;
use Verja\Validator;

class Url extends Validator
{
    const COVERAGE_VALID     = 'valid'; // check only that the url is valid (parse_url !== false)
    const COVERAGE_COMPLETE  = 'complete'; // check that host and scheme is given
    const COVERAGE_ACTIVE    = 'active'; // check that the host exists (active dns record)
    const COVERAGE_LISTENING = 'listening'; // check for an open http(s) or ftp port

    /** @var int[] */
    protected static $ports = [
        'https' => 443,
        'http' => 80,
        'ftp' => 21,
        'ssh' => 22,
    ];

    /** @var callable */
    protected static $socketTester;

    /** @var callable */
    protected static $dnsTester;

    /** @var string */
    protected $mode;

    /** @var array */
    protected $schemes = ['https', 'http', 'ftp'];

    /**
     * Url constructor.
     *
     * @param string       $coverage
     * @param array|string $schemes
     */
    public function __construct(string $coverage = self::COVERAGE_COMPLETE, $schemes = ['https', 'http', 'ftp'])
    {
        $this->mode    = $coverage;
        $this->schemes = is_string($schemes) ? array_slice(func_get_args(), 1) : $schemes;
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
        if (!is_string($value) || !$url = parse_url($value)) {
            // not a string or not a url
            $this->error = new Error(
                'NO_URL',
                $value,
                'value should be a valid url'
            );
            return false;
        }

        if ($this->mode !== self::COVERAGE_VALID) {
            // for scheme file we don't need a host
            if (!isset($url['scheme']) || !isset($url['host']) && $url['scheme'] !== 'file') {
                // no full url
                $this->error = new Error(
                    'NOT_FULL_URL',
                    $value,
                    'value should be a full url including host and scheme',
                    ['schemes' => $this->schemes]
                );
                return false;
            }

            if (!in_array($url['scheme'], $this->schemes)) {
                // scheme not allowed
                $this->error = new Error(
                    'SCHEME_NOT_ALLOWED',
                    $value,
                    'value should contain an allowed scheme',
                    ['schemes' => $this->schemes]
                );
                return false;
            }

            if (($this->mode === self::COVERAGE_ACTIVE || $this->mode === self::COVERAGE_LISTENING) &&
                !static::testDns($url['host'])
            ) {
                // no dns entry
                $this->error = new Error(
                    'NO_DNS_RECORD',
                    $value,
                    'value should contain a hostname with an active dns record'
                );
                return false;
            }

            if ($this->mode === self::COVERAGE_LISTENING) {
                if (!isset($url['port']) && !isset(self::$ports[$url['scheme']])) {
                    // no port defined
                    $this->error = new Error(
                        'NO_PORT',
                        $value,
                        'value should contain a port'
                    );
                    return false;
                }

                if (!static::testSocket($url['host'], $url['port'] ?? self::$ports[$url['scheme']])) {
                    // not listening to connections
                    $this->error = new Error(
                        'NOT_LISTENING',
                        $value,
                        'value should point to a server that is currently listening'
                    );
                    return false;
                }
            }
        }

        return true;
    }

    protected static function testSocket(string $host, int $port)
    {
        if (static::$socketTester !== null) {
            return call_user_func(static::$socketTester, $host, $port);
        }

        // @codeCoverageIgnoreStart
        // we can not test this during unit tests - so we mock this through $socketTester
        try {
            $socket = fsockopen($host, $port, $errno, $errstr, 0.5);
            fclose($socket);
            return true;
        } catch (\Throwable $e) {
            // ignore any errors (socket timeout for example)
        }
        return false;
        // @codeCoverageIgnoreEnd
    }

    protected static function testDns(string $host)
    {
        if (static::$dnsTester !== null) {
            return call_user_func(static::$dnsTester, $host);
        }

        // @codeCoverageIgnoreStart
        // we can not test this during unit tests - so we mock this through $dnsTester
        return checkdnsrr($host, 'A') ||
               checkdnsrr($host, 'AAAA') ||
               checkdnsrr($host, 'CNAME');
        // @codeCoverageIgnoreEnd
    }

    public static function setSocketTester(callable $socketTester)
    {
        static::$socketTester = $socketTester;
    }

    public static function setDnsTester(callable $dnsTester)
    {
        static::$dnsTester = $dnsTester;
    }
}
