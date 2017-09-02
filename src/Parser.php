<?php

namespace Verja;

class Parser
{
    /**
     * Returns a className and an array of parameters from $str
     *
     * The parameters start with and are divided by a colon.
     *
     * There are several limitations for parameters:
     *   1. They are all string - no type casting here
     *   2. They can not have colons - these increases the performance
     *
     * To avoid these limitations you can use json array notation. The json decode uses assoc = true - so we get an
     * array for objects. Please make sure to start and end with brackets. Here is an example how to pass an array:
     * `'equals:[ { "key": "value" } ]'`
     *
     * @param string $str
     * @return array
     */
    public static function parseClassNameWithParameters(string $str)
    {
        $colonPos = strpos($str, ':');
        $className = ucfirst($colonPos === false ? trim($str) : trim(substr($str, 0, $colonPos)));

        if (empty($className)) {
            throw new \InvalidArgumentException(sprintf(
                '%s is not a valid string for ' . __METHOD__,
                empty($str) ? '$str' : $str
            ));
        }

        if ($colonPos === false) {
            return [$className, []];
        }

        return [$className, self::parseParameters(substr($str, $colonPos+1))];
    }

    /**
     * Return an array of parameters from $str
     *
     * @see Parser::parseClassNameWithParameters() for description and limitations of parameters
     * @param string $str
     * @return array
     */
    public static function parseParameters($str)
    {
        if (strlen($str) > 1 && $str[0] === '[' && substr($str, -1) === ']') {
            return (array) json_decode($str, true);
        }

        $parameters = array_filter(explode(':', $str), function ($value) {
            return strlen($value) > 0;
        });

        return array_map(function ($parameter) {
            if (strlen($parameter) > 1 && (
                    $parameter[0] === '"' && substr($parameter, -1) === '"' ||
                    $parameter[0] === "'" && substr($parameter, -1) === "'"
                )
            ) {
                return substr($parameter, 1, -1);
            }
            return $parameter;
        }, $parameters);
    }
}
