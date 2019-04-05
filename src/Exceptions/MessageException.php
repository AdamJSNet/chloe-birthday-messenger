<?php

namespace App\Exceptions;

class MessageException extends \Exception
{
    public static function invalidProperties(array $props)
    {
        return new self("Invalid properties: " . implode(", ", $props));
    }

    public static function invalidDateFormat()
    {
        return new self("Invalid date format");
    }

    public static function invalidType(array $types)
    {
        return new self("Invalid value of 'type'. Expected " . implode(' / ', $types));
    }
}