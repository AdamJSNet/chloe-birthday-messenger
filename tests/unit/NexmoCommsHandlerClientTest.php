<?php

use App\Clients\NexmoCommsHandlerClient;
use App\Exceptions\CommsHandlerClientException;

class NexmoCommsHandlerClientTest extends PHPUnit\Framework\TestCase
{
    public function test_it_throws_exception_when_client_send_fails()
    {
        $messageClient = $this->getMockNexmoMessage()
            ->shouldReceive('send')
            ->andThrow(new Exception('hello world'))
            ->mock();

        $nexmo = $this->getMockNexmoClient()
            ->shouldReceive('message')
            ->andReturn($messageClient)
            ->mock();

        $this->expectExceptionObject(CommsHandlerClientException::operationFailed('send SMS', 'hello world'));

        $client = new NexmoCommsHandlerClient($nexmo);
        $client->sendSms('to', 'from', 'message');
    }

    public function test_it_throws_exception_when_response_status_is_invalid()
    {
        $message = Mockery::mock(Nexmo\Message\Message::class)
            ->shouldReceive('getResponseData')
            ->andReturn([
                'messages' => [
                    [
                        'status' => 'borked'   // not 0, which means successful
                    ]
                ]
            ])
            ->mock();

        $messageClient = $this->getMockNexmoMessage()
            ->shouldReceive('send')
            ->andReturn($message)
            ->mock();

        $nexmo = $this->getMockNexmoClient()
            ->shouldReceive('message')
            ->andReturn($messageClient)
            ->mock();

        $this->expectExceptionObject(CommsHandlerClientException::operationFailed('send SMS', 'borked'));

        $client = new NexmoCommsHandlerClient($nexmo);
        $client->sendSms('to', 'from', 'message');
    }

    public function test_it_sends_sms()
    {
        $message = Mockery::mock(Nexmo\Message\Message::class)
            ->shouldReceive('getResponseData')
            ->andReturn([
                'messages' => [
                    [
                        'status' => '0'   // success
                    ]
                ]
            ])
            ->mock();

        $messageClient = $this->getMockNexmoMessage()
            ->shouldReceive('send')
            ->andReturn($message)
            ->mock();

        $nexmo = $this->getMockNexmoClient()
            ->shouldReceive('message')
            ->andReturn($messageClient)
            ->mock();

        $client = new NexmoCommsHandlerClient($nexmo);
        $this->assertTrue($client->sendSms('to', 'from', 'message'));
    }

    protected function getMockNexmoClient()
    {
        return Mockery::mock(Nexmo\Client::class);
    }

    protected function getMockNexmoMessage()
    {
        return Mockery::mock(Nexmo\Message\Client::class);
    }

    protected function tearDown()
    {
        Mockery::close();
    }
}