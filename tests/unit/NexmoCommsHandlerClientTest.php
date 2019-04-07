<?php

use App\Clients\NexmoCommsHandlerClient;
use App\Exceptions\CommsHandlerClientException;

class NexmoCommsHandlerClientTest extends PHPUnit\Framework\TestCase
{
    public function test_it_throws_exception_when_client_sms_send_fails()
    {
        $messageClient = $this->getMockNexmoMessage()
            ->shouldReceive('send')
            ->once()
            ->andThrow(new Exception('hello world'))
            ->mock();

        $nexmo = $this->getMockNexmoClient()
            ->shouldReceive('message')
            ->once()
            ->andReturn($messageClient)
            ->mock();

        $this->expectExceptionObject(CommsHandlerClientException::operationFailed('send SMS', 'hello world'));

        $client = new NexmoCommsHandlerClient($nexmo);
        $client->sendSms('to', 'from', 'content');
    }

    public function test_it_throws_exception_when_sms_response_status_is_invalid()
    {
        $message = Mockery::mock(Nexmo\Message\Message::class)
            ->shouldReceive('getResponseData')
            ->once()
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
            ->once()
            ->andReturn($message)
            ->mock();

        $nexmo = $this->getMockNexmoClient()
            ->shouldReceive('message')
            ->once()
            ->andReturn($messageClient)
            ->mock();

        $this->expectExceptionObject(CommsHandlerClientException::operationFailed('send SMS', 'borked'));

        $client = new NexmoCommsHandlerClient($nexmo);
        $client->sendSms('to', 'from', 'content');
    }

    public function test_it_sends_sms_message()
    {
        $message = Mockery::mock(Nexmo\Message\Message::class)
            ->shouldReceive('getResponseData')
            ->once()
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
            ->once()
            ->andReturn($message)
            ->mock();

        $nexmo = $this->getMockNexmoClient()
            ->shouldReceive('message')
            ->once()
            ->andReturn($messageClient)
            ->mock();

        $client = new NexmoCommsHandlerClient($nexmo);
        $this->assertTrue($client->sendSms('to', 'from', 'content'));
    }

    public function test_it_throws_exception_when_send_voice_message_fails()
    {
        $callsCollection = $this->getMockNexmoCalls()
            ->shouldReceive('create')
            ->once()
            ->andThrow(new Exception('hello world'))
            ->mock();

        $nexmo = $this->getMockNexmoClient()
            ->shouldReceive('calls')
            ->once()
            ->andReturn($callsCollection)
            ->mock();

        $this->expectExceptionObject(CommsHandlerClientException::operationFailed('send Voice', 'hello world'));

        $client = new NexmoCommsHandlerClient($nexmo);
        $this->assertTrue($client->sendVoice('to', 'from', 'content'));
    }

    public function test_it_sends_voice_message()
    {
        $callsCollection = $this->getMockNexmoCalls()
            ->shouldReceive('create')
            ->once()
            ->mock();

        $nexmo = $this->getMockNexmoClient()
            ->shouldReceive('calls')
            ->once()
            ->andReturn($callsCollection)
            ->mock();

        $client = new NexmoCommsHandlerClient($nexmo);
        $this->assertTrue($client->sendVoice('to', 'from', 'content'));
    }

    protected function getMockNexmoClient()
    {
        return Mockery::mock(Nexmo\Client::class);
    }

    protected function getMockNexmoMessage()
    {
        return Mockery::mock(Nexmo\Message\Client::class);
    }

    protected function getMockNexmoCalls()
    {
        return Mockery::mock(Nexmo\Call\Collection::class);
    }

    protected function tearDown()
    {
        Mockery::close();
    }
}