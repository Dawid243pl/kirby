<?php

namespace Kirby\Toolkit;

use InvalidArgumentException;

class Type
{

    static public function check(string $method, $value, bool $required = false, string $message = 'Please provide a valid number')
    {
        if ($required === true && $value === null) {
            throw new InvalidArgumentException($message);
        }


        if ($value !== null && $method($value) === false) {
            throw new InvalidArgumentException($message);
        }

        return $value;
    }

    static public function array($value, bool $required = false, string $message = 'Please provide an array'): ?array
    {
        return static::check('is_array', $value, $required, $message);
    }

    static public function bool($value, bool $required = false, string $message = 'Please provide a boolean'): ?bool
    {
        return static::check('is_bool', $value, $required, $message);
    }

    static public function float($value, bool $required = false, string $message = 'Please provide a float'): ?float
    {
        return static::check('is_float', $value, $required, $message);
    }

    static public function int($value, bool $required = false, string $message = 'Please provide an integer'): ?int
    {
        return static::check('is_int', $value, $required, $message);
    }

    static public function number($value, bool $required = false, string $message = 'Please provide a number')
    {
        return static::check('is_numeric', $value, $required, $message);
    }

    static public function string($value, bool $required = false, string $message = 'Please provide a string'): ?string
    {
        return static::check('is_string', $value, $required, $message);
    }

}
