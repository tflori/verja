<?php

namespace Verja;

use Verja\Exception\InvalidValue;
use Verja\Exception\ValidatorNotFound;
use Verja\Validator\Not;

/**
 * Class Validator
 *
 * @package Verja
 * @author  Thomas Flori <thflori@gmail.com>
 *
 * @method static Validator\Boolean boolean(array $stringTrue = [], array $stringFalse = [], $overwrite = false)
 * @method static Validator\Callback callback(\Closure $callback)
 * @method static Validator\Contains contains(string $subString)
 * @method static Validator\EmailAddress emailAddress()
 * @method static Validator\Equals equals(string $opposite, bool $jsonEncode = true)
 * @method static Validator\Integer integer()
 * @method static Validator\IpAddress ipAddress(string $version = 'any', string $range = 'any')
 * @method static Validator\Not not($validator)
 * @method static Validator\NotEmpty notEmpty()
 * @method static Validator\Numeric numeric(string $decimalPoint = '.')
 * @method static Validator\PregMatch pregMatch(string $pattern)
 * @method static Validator\StrLen strLen(int $min, int $max = 0)
 * @method static Validator\Truthful truthful()
 */
abstract class Validator implements ValidatorInterface
{
    use WithAssignedField;

    /** @var string[] */
    protected static $namespaces = [ '\\Verja\\Validator' ];

    /** @var array */
    protected $error;

    /**
     * Assert that $validator validates $value within $context
     *
     * If not an InvalidValue exception is thrown.
     *
     * @param string|callable|ValidatorInterface $validator
     * @param mixed                              $value
     * @param array                              $context
     * @return mixed
     * @throws InvalidValue
     */
    public static function assert($validator, $value, array $context = [])
    {
        $validator = self::getValidator($validator);
        if (!$validator->validate($value, $context)) {
            if ($error = $validator->getError()) {
                throw new InvalidValue(sprintf('Assertion failed: %s', $error->message), $error);
            } else {
                $validatorReflection = new \ReflectionClass($validator);
                throw new InvalidValue(sprintf(
                    'Failed asserting that %s is %s',
                    json_encode($value),
                    $validatorReflection->getShortName()
                ));
            }
        }

        return $value;
    }

    /**
     * Get a validator instance
     *
     * @param string|callable|ValidatorInterface $validator
     * @return ValidatorInterface
     */
    public static function getValidator($validator)
    {
        if (is_string($validator)) {
            $validator = static::fromString($validator);
        } elseif (is_callable($validator)) {
            $validator = new Validator\Callback($validator);
        }

        if (!$validator instanceof ValidatorInterface) {
            throw new \InvalidArgumentException('$validator has to be an instance of ValidatorInterface');
        }

        return $validator;
    }

    /**
     * Create a Validator from $str
     *
     * This method uses Parser::parseClassNameWIthParameters. This method has some limitations for parameters - look
     * at it's description to learn more.
     *
     * @param string $definition
     * @return ValidatorInterface
     * @see Parser::parseClassNameWithParameters() to learn how to pass parameters
     */
    public static function fromString(string $definition): ValidatorInterface
    {
        if (strlen($definition) > 0 && $definition[0] === '!') {
            return new Not(substr($definition, 1));
        }

        return static::create(...Parser::parseClassNameWithParameters($definition));
    }

    /**
     * Create a validator dynamically
     *
     * @param string $name
     * @param array  $arguments
     * @return ValidatorInterface
     */
    public static function __callStatic($name, array $arguments)
    {
        return static::create(ucfirst($name), $arguments);
    }

    /**
     * Create a validator dynamically
     *
     * @param string $shortName
     * @param array  $parameters
     * @return ValidatorInterface
     * @throws ValidatorNotFound
     */
    public static function create(string $shortName, array $parameters = []): ValidatorInterface
    {
        foreach (self::$namespaces as $namespace) {
            $class = $namespace . '\\' . $shortName;
            if (!class_exists($class)) {
                continue;
            }
            $validator = new $class(...$parameters);
            if (!$validator instanceof ValidatorInterface) {
                continue;
            }
            return $validator;
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
