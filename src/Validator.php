<?php

namespace Verja;

use Verja\Exception\ValidatorNotFound;
use Verja\Validator\Not;

abstract class Validator implements ValidatorInterface
{
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
        $class = '\\Verja\\Validator\\' . $shortName;

        if (!class_exists($class)) {
            throw new ValidatorNotFound($shortName);
        }

        return new $class(...$parameters);
    }
}
