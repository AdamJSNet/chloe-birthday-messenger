<?php

namespace App\Contracts;

interface DataStoreClientInterface
{
    public function load();
    public function getData(): array;
    public function setData(array $data);
    public function save();
}
