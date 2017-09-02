<?php

namespace Verja;

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
     * @see Parser::parseClassNameWithParameters() to learn how to pass parameters
     */
    public static function fromString(string $definition): ValidatorInterface
    {
        list($shortName, $parameters) = Parser::parseClassNameWithParameters($definition);
        $class = '\\Verja\\Validator\\' . $shortName;

        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Validator \'%s\' not found', $shortName));
        }

        return new $class(...$parameters);
    }
}
