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
                'text' => $content,
                'type' => 'unicode'
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

    /**
     * @param $to
     * @param $from
     * @param $content
     * @return bool
     * @throws CommsHandlerClientException
     */
    public function sendVoice($to, $from, $content): bool
    {
        $params = [
            'to' => [[
                'type' => 'phone',
                'number' => $to
            ]],
            'from' => [
                'type' => 'phone',
                'number' => $from
            ],
        ];

        $json = json_decode($content, true);
        if ($json !== null) {
            $params['ncco'] = $json;            // content should be considered a ncco object
        } else {
            $params['answer_url'] = [$content]; // content should be considered an answer URL
        }

        try {
            $this->client->calls()->create($params);
        } catch (\Exception $e) {
            throw CommsHandlerClientException::operationFailed("send Voice", $e->getMessage());
        }

        return true;
    }
}
