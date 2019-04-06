<?php

namespace App\Exceptions;

class DataStoreClientException extends \Exception
{
    public static function noLock()
    {
        return new self("Could not acquire file lock");
    }

    public static function saveFailed()
    {
        return new self("Save operation failed");
    }
}
