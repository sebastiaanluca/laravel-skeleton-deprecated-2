<?php

namespace App\Exceptions;

use Exception;

class JsonException extends Exception
{
    /**
     * @param string $error
     *
     * @return static
     */
    public static function invalidJson(string $error)
    {
        return new static('Unable to parse JSON: ' . strtolower($error));
    }
}
