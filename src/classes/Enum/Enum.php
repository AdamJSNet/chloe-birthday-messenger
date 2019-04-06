<?php

namespace App\Enum;

class Enum
{
    public static function getAll()
    {
        $reflection = new \ReflectionClass(static::class);
        return $reflection->getConstants();
    }
}
