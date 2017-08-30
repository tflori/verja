<?php

namespace Verja;

abstract class Filter implements FilterInterface
{
    public static function fromString($str)
    {
        $filters = [];

        foreach (explode('|', $str) as $f) {
            $f = trim($f);
            if (empty($f)) {
                continue;
            }

            list($shortName, $parameters) = Parser::parseClassNameWithParameters($f);
            $class = '\\Verja\\Filter\\' . ucfirst($shortName);
            $filters[] = new $class(...$parameters);
        }

        return $filters;
    }
}
