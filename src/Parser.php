<?php

namespace Verja;

class Parser
{

    public static function parseClassNameWithParameters($str)
    {
        $colonPos = strpos($str, ':');

        if (strlen($str) < 1 || $colonPos !== false && $colonPos < 1) {
            throw new \InvalidArgumentException('$str is not a valid string for ' . __METHOD__);
        }

        if ($colonPos === false) {
            return [$str, []];
        }

        return [substr($str, 0, $colonPos), self::parseParameters(substr($str, $colonPos+1))];
    }

    public static function parseParameters($str)
    {
        $parameters = array_filter(explode(':', $str));

        return array_map(function ($parameter) {
            if ($parameter[0] === '"' && strlen($parameter) > 1 && substr($parameter, -1) === '"') {
                return substr($parameter, 1, -1);
            }
            return $parameter;
        }, $parameters);
    }
}
