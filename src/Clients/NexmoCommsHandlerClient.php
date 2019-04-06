<?php

namespace App\Clients;

use App\Contracts\CommsHandlerClientInterface;
use App\Exceptions\CommsHandlerClientException;
use Nexmo\Client;

class NexmoCommsHandlerClient implements CommsHandlerClientInterface
{
    /** @var Client $client */
    protected $client;

    /**
     * NexmoCommsHandlerClient constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $to
     * @param string $from
     * @param string $message
     * @return bool
     * @throws CommsHandlerClientException
     */
    public function sendSms(string $to, string $from, string $message): bool
    {
        try {
            $message = $this->client->message()->send([
                'to' => $to,
                'from' => $from,
                'text' => $message
            ]);
        } catch (\Exception $e) {
            throw CommsHandlerClientException::operationFailed("send SMS", $e->getMessage());
        }

        $response = $message->getResponseData();
        $status = $response['messages'][0]['status'];

        if ($status !== "0" && $status !== 0) {
            throw CommsHandlerClientException::operationFailed("send SMS", $response['messages'][0]['status']);
        }

        return true;
    }
}
