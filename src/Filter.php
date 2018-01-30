<?php

namespace Verja;

use Verja\Exception\FilterNotFound;
use Verja\Exception\InvalidValue;

/**
 * Class Filter
 *
 * @package Verja
 * @author  Thomas Flori <thflori@gmail.com>
 *
 * @method static Filter\Boolean boolean(array $stringTrue = [], array $stringFalse = [], $overwrite = false)
 * @method static Filter\Callback callback($filter)
 * @method static Filter\Integer integer()
 * @method static Filter\Numeric numeric(string $decimalPoint = '.')
 * @method static Filter\Replace replace($search, $replace)
 * @method static Filter\Trim trim(string $characterMask = " \t\n\r\0\x0B")
 */
abstract class Filter implements FilterInterface
{
    use WithAssignedField;

    /** @var string[] */
    protected static $namespaces = [ '\\Verja\\Filter' ];

    /** @var ValidatorInterface */
    protected $validatedBy;

    /**
     * Get a filter instance
     *
     * @param string|callable|FilterInterface $filter
     * @return Filter\Callback|FilterInterface
     */
    public static function getFilter($filter)
    {
        if (is_string($filter)) {
            $filter = Filter::fromString($filter);
        } elseif (is_callable($filter)) {
            $filter = new Filter\Callback($filter);
        }

        if (!$filter instanceof FilterInterface) {
            throw new \InvalidArgumentException('$filter has to be an instance of FilterInterface');
        }

        return $filter;
    }

    /**
     * Create a Filter from $str
     *
     * This method uses Parser::parseClassNameWIthParameters. This method has some limitations for parameters - look
     * at it's description to learn more.
     *
     * @param string $definition
     * @return FilterInterface
     * @see Parser::parseClassNameWithParameters() to learn how to pass parameters
     */
    public static function fromString(string $definition): FilterInterface
    {
        // we check for a basic filter when the validator is negated
        if (strlen($definition) > 0 && $definition[0] === '!') {
            $definition = substr($definition, 1);
        }

        return static::create(...Parser::parseClassNameWithParameters($definition));
    }

    /**
     * Create a filter dynamically
     *
     * @param string $name
     * @param array  $arguments
     * @return FilterInterface
     */
    public static function __callStatic($name, array $arguments)
    {
        return static::create(ucfirst($name), $arguments);
    }

    /**
     * Create a filter dynamically
     *
     * @param string $shortName
     * @param array  $parameters
     * @return FilterInterface
     * @throws FilterNotFound
     */
    public static function create(string $shortName, array $parameters = []): FilterInterface
    {
        foreach (self::$namespaces as $namespace) {
            $class = $namespace . '\\' . $shortName;
            if (!class_exists($class)) {
                continue;
            }
            $filter = new $class(...$parameters);
            if (!$filter instanceof FilterInterface) {
                continue;
            }
            return $filter;
        }

        throw new FilterNotFound($shortName);
    }

    /**
     * Register an additional namespace
     *
     * @param string $namespace
     */
    public static function registerNamespace(string $namespace)
    {
        array_unshift(self::$namespaces, $namespace);

        // Untestable - required to reduce performance impact
        // @codeCoverageIgnoreStart
        if (count(self::$namespaces) > 2) {
            self::$namespaces = array_unique(self::$namespaces);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Reset namespaces to defaults
     */
    public static function resetNamespaces()
    {
        self::$namespaces = [ '\\Verja\\Filter' ];
    }
}
