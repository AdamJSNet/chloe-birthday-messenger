<?php

namespace App\Contracts;

interface DataStoreClientInterface extends \Countable
{
    public function load();
    public function getData(): array;
    public function setData(array $data);
    public function save();
}
