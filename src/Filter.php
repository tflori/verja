<?php

namespace Verja;

use Verja\Exception\FilterNotFound;

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
     * @param string $str
     * @return FilterInterface
     * @throws FilterNotFound
     * @see Parser::parseClassNameWithParameters() to learn how to pass parameters
     */
    public static function fromString(string $str): FilterInterface
    {
        // we check for a basic filter when the validator is negated
        if (strlen($str) > 0 && $str[0] === '!') {
            $str = substr($str, 1);
        }

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
     * @return ValidatorInterface
     */
    public function getValidatedBy()
    {
        return $this->validatedBy;
    }

    /**
     * Set the validator for this filter
     *
     * @param ValidatorInterface|string|callable $validator
     * @throws \InvalidArgumentException
     */
    protected function setValidatedBy($validator)
    {
        $this->validatedBy = Validator::getValidator($validator);
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
