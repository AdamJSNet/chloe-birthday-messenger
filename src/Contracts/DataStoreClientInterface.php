<?php

namespace App\Contracts;

interface DataStoreClientInterface
{
    public function load();
    public function getContents();
    public function setContents($contents);
    public function save();
}
