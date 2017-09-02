<?php

namespace Verja;

abstract class Filter implements FilterInterface
{
    /**
     * Create a Filter from $str
     *
     * This method uses Parser::parseClassNameWIthParameters. This method has some limitations for parameters - look
     * at it's description to learn more.
     *
     * @param string $str
     * @return FilterInterface
     * @see Parser::parseClassNameWithParameters() to learn how to pass parameters
     */
    public static function fromString(string $str): FilterInterface
    {
        list($shortName, $parameters) = Parser::parseClassNameWithParameters($str);
        $class = '\\Verja\\Filter\\' . $shortName;

        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Filter \'%s\' not found', $shortName));
        }

        return new $class(...$parameters);
    }
}
