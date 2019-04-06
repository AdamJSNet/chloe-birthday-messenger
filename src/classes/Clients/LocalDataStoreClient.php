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

    /**
     * LocalDataStoreClient constructor.
     * @param \SplFileObject $file
     */
    public function __construct(\SplFileObject $file)
    {
        $this->file = $file;
    }

    /**
     * @throws DataStoreClientException
     */
    public function load(): self
    {
        if ($this->file->flock(LOCK_SH) === false) {
            throw DataStoreClientException::noLock();
        }

        $json = json_decode($this->getFileContents(), true);
        if ($json === null) {
            throw DataStoreClientException::invalidFormat();
        }

        $this->data = $json;

        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return LocalDataStoreClient
     */
    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return bool
     * @throws DataStoreClientException
     */
    public function save()
    {
        $contents = json_encode($this->data);
        $this->file->fseek(0);      // move pointer to beginning of file
        $this->file->ftruncate(0);  // clear existing contents
        $result = $this->file->fwrite($contents);
        if ($result === null || $result === 0) {
            throw DataStoreClientException::saveFailed();
        }

        return true;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * @return string
     */
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
