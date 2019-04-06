<?php

namespace App\Exceptions;

class CommsHandlerClientException extends \Exception
{
    public static function operationFailed(string $operation, string $status = "")
    {
        $message = "Failed to $operation";

        if (!empty($status)) {
            $message .= ": $status";
        }

        return new self($message);
    }
}
