<?php

use App\Clients\LocalDataStoreClient;
use App\Exceptions\DataStoreClientException;
use App\Wrappers\SplFileObjectWrapper;

class LocalDataStoreClientTest extends PHPUnit\Framework\TestCase
{
    public function test_it_throws_exception_when_unable_to_retrieve_file_lock()
    {
        $file = $this->getSplFileObjectMock()
            ->shouldReceive('flock')
            ->andReturn(false)
            ->mock();

        $this->expectExceptionObject(DataStoreClientException::noLock());

        $client = new LocalDataStoreClient($file);
        $client->load();
    }

    public function test_it_sets_and_gets_data()
    {
        $file = $this->getSplFileObjectMock();

        $data = ["hello" => "world"];

        $client = new LocalDataStoreClient($file);
        $client->setData($data);

        $this->assertEquals($data, $client->getData());
    }

    public function test_it_throws_exception_when_write_is_not_successful()
    {
        $file = $this->getSplFileObjectMock()
            ->shouldReceive('fwrite')
            ->andReturn(null)
            ->mock();

        $this->expectExceptionObject(DataStoreClientException::saveFailed());

        $client = new LocalDataStoreClient($file);
        $client->save();
    }

    public function test_it_returns_true_on_successful_write()
    {
        $file = $this->getSplFileObjectMock()
            ->shouldReceive('fwrite')
            ->andReturn(true)
            ->mock();

        $client = new LocalDataStoreClient($file);
        $this->assertTrue($client->save());
    }

    protected function getSplFileObjectMock()
    {
        // Internal class enforces parent constructor call before any other method call,
        // so pass in constructor argument to force Mockery to call this first.
        return Mockery::mock(\SplFileObject::class, ['php://memory']);
    }
}