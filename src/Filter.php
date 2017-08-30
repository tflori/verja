<?php

namespace Verja;

abstract class Filter implements FilterInterface
{
    public static function fromString($str)
    {
        if (empty($str)) {
            return null;
        }

        list($shortName, $parameters) = Parser::parseClassNameWithParameters($str);
        $class = '\\Verja\\Filter\\' . ucfirst($shortName);
        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Filter \'%s\' not found', $shortName));
        }
        return new $class(...$parameters);
    }
}
