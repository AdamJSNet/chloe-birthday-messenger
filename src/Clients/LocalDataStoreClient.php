<?php

namespace App\Clients;

use App\Contracts\DataStoreClientInterface;
use App\Exceptions\DataStoreClientException;

class LocalDataStoreClient implements DataStoreClientInterface
{
    /** @var \SplFileObject $file */
    protected $file;
    /** @var string $contents */
    protected $contents = "";

    public function __construct(\SplFileObject $file)
    {
        $this->file = $file;
    }

    public function load()
    {
        if ($this->file->flock(LOCK_SH) === false) {
            throw DataStoreClientException::noLock();
        }

        $this->file->rewind();

        foreach ($this->file as $line) {
            $this->contents .= $line;
        }
    }

    public function getContents()
    {
        return $this->contents;
    }

    public function setContents($contents)
    {
        $this->contents = (string) $contents;
        return $this;
    }

    public function save()
    {
        if ($this->file->fwrite($this->contents) === null) {
            throw DataStoreClientException::saveFailed();
        }

        return true;
    }
}
