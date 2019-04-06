<?php

namespace App\Exceptions;

class MessageCollectionException extends \Exception
{
    public static function invalidMessage(MessageException $e)
    {
        return new self("Message could not be added to collection... " . $e->getMessage(), $e->getCode(), $e);
    }
}
