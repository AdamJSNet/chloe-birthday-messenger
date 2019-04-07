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
     * @param $to
     * @param $from
     * @param $content
     * @return bool
     * @throws CommsHandlerClientException
     */
    public function sendSms($to, $from, $content): bool
    {
        try {
            $message = $this->client->message()->send([
                'to' => $to,
                'from' => $from,
                'text' => $content
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
