<?php

namespace Verja;

use Verja\Exception\FilterNotFound;

abstract class Filter implements FilterInterface
{
    use WithAssignedField;

    /** @var string[] */
    protected static $namespaces = [ '\\Verja\\Filter' ];

    /**
     * Create a Filter from $str
     *
     * This method uses Parser::parseClassNameWIthParameters. This method has some limitations for parameters - look
     * at it's description to learn more.
     *
     * @param string $str
     * @return FilterInterface
     * @throws FilterNotFound
     * @see Parser::parseClassNameWithParameters() to learn how to pass parameters
     */
    public static function fromString(string $str): FilterInterface
    {
        list($shortName, $parameters) = Parser::parseClassNameWithParameters($str);

        foreach (self::$namespaces as $namespace) {
            $class = $namespace . '\\' . $shortName;
            if (class_exists($class)) {
                return new $class(...$parameters);
            }
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
