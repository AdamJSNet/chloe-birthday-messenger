<?php

namespace App\Exceptions;

class MessageServiceException extends \Exception
{
    public static function unrecognisedMessageType(string $type)
    {
        return new self("Unrecognised message type '$type'");
    }

    public static function rethrow(\Exception $e)
    {
        return new self($e->getMessage(), $e->getCode(), $e->getTraceAsString());
    }
}
