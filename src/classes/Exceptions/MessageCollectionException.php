<?php

namespace App\Exceptions;

class MessageCollectionException extends \Exception
{
    public static function invalidMessage(MessageException $e)
    {
        return new self("Message could not be added to collection... " . $e->getMessage(), $e->getCode(), $e);
    }

    public static function messageExists(string $id)
    {
        return new self("Message with ID '$id' already exists");
    }

    public static function messageNotExists(string $id)
    {
        return new self("Message with ID '$id' does not already exist");
    }
}
