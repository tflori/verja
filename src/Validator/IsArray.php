<?php

namespace Verja\Validator;

use Verja\Error;
use Verja\Validator;

class IsArray extends Validator
{
    const TYPE_ANY = 'any';
    const TYPE_ASSOC = 'assoc';
    const TYPE_INDEX = 'index';

    protected $type;

    /**
     * IsArray constructor.
     *
     * @param string $type 'any' (default), 'assoc' or 'index'
     */
    public function __construct(string $type = self::TYPE_ANY)
    {
        $this->type = $type;
    }

    /**
     * Validate $value
     *
     * @param mixed $value
     * @param array $context
     * @return bool
     */
    public function validate($value, array $context = []): bool
    {
        $parameters = ['type' => $this->type];

        if (!is_array($value)) {
            $this->error = new Error('NO_ARRAY', $value, 'value should be an array', $parameters);
            return false;
        } elseif ($this->type !== self::TYPE_ANY) {
            $isIndexed = empty($value) || array_keys($value) === range(0, count($value) -1);
            if ($this->type === self::TYPE_ASSOC && $isIndexed) {
                $this->error = new Error('NO_ASSOC_ARRAY', $value, 'value should be an associative array', $parameters);
                return false;
            } elseif ($this->type === self::TYPE_INDEX && !$isIndexed) {
                $this->error = new Error('NO_INDEX_ARRAY', $value, 'value should be an indexed array', $parameters);
                return false;
            }
        }

        return true;
    }
}
