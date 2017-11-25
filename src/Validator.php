<?php

namespace Verja;

use Verja\Exception\ValidatorNotFound;
use Verja\Validator\Not;

abstract class Validator implements ValidatorInterface
{
    use WithAssignedField;

    /** @var string[] */
    protected static $namespaces = [ '\\Verja\\Validator' ];

    /** @var array */
    protected $error;

    /**
     * Create a Validator from $str
     *
     * This method uses Parser::parseClassNameWIthParameters. This method has some limitations for parameters - look
     * at it's description to learn more.
     *
     * @param string $definition
     * @return ValidatorInterface
     * @throws ValidatorNotFound
     * @see Parser::parseClassNameWithParameters() to learn how to pass parameters
     */
    public static function fromString(string $definition): ValidatorInterface
    {
        if (strlen($definition) > 0 && $definition[0] === '!') {
            return new Not(substr($definition, 1));
        }

        list($shortName, $parameters) = Parser::parseClassNameWithParameters($definition);
        foreach (self::$namespaces as $namespace) {
            $class = $namespace . '\\' . $shortName;
            if (class_exists($class)) {
                return new $class(...$parameters);
            }
        }

        throw new ValidatorNotFound($shortName);
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
        self::$namespaces = [ '\\Verja\\Validator' ];
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore trivial
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * The inverse error is not required to implement
     *
     * @param mixed $value
     * @return null
     * @codeCoverageIgnore trivial
     */
    public function getInverseError($value)
    {
        return null;
    }
}
