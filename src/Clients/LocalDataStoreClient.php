<?php

namespace App\Clients;

use App\Contracts\DataStoreClientInterface;
use App\Exceptions\DataStoreClientException;

class LocalDataStoreClient implements DataStoreClientInterface
{
    /** @var \SplFileObject $file */
    protected $file;
    /** @var array $data */
    protected $data = [];

    public function __construct(\SplFileObject $file)
    {
        $this->file = $file;
    }

    public function load()
    {
        if ($this->file->flock(LOCK_SH) === false) {
            throw DataStoreClientException::noLock();
        }

        $json = json_decode($this->getFileContents());
        if ($json === null) {
            throw DataStoreClientException::invalidFormat();
        }

        $this->data = $json;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data)
    {
        $this->data = $data;
    }

    public function save()
    {
        $contents = json_encode($this->data);
        if ($this->file->fwrite($contents) === null) {
            throw DataStoreClientException::saveFailed();
        }

        return true;
    }

    public function count()
    {
        return count($this->data);
    }

    protected function getFileContents()
    {
        $this->file->rewind();

        $contents = "";
        foreach ($this->file as $line) {
            $contents .= $line;
        }

        return $contents;
    }
}
