<?php

namespace App\Services;

use App\Collections\MessageCollection;
use App\Contracts\CommsHandlerClientInterface;
use App\Contracts\DataStoreClientInterface;
use App\Contracts\MessageInterface;
use App\Enum\MessageType;
use App\Exceptions\CommsHandlerClientException;
use App\Exceptions\MessageCollectionException;
use App\Exceptions\MessageServiceException;

/**
 * @todo write tests
 */
class MessageService
{
    /** @var CommsHandlerClientInterface $comms */
    protected $comms;
    /** @var string $senderSms */
    protected $senderSms;
    /** @var string $senderVoice */
    protected $senderVoice;

    public function __construct(CommsHandlerClientInterface $comms, string $senderSms = null, string $senderVoice = null)
    {
        $this->comms = $comms;
        $this->senderSms = $senderSms ?? getenv("NEXMO_SMS_SENDER");
        $this->senderVoice = $senderVoice ?? getenv("NEXMO_VOICE_SENDER");
    }

    /**
     * @param DataStoreClientInterface $store
     * @throw MessageServiceException
     * @return MessageCollection
     */
    public function getAllMessagesFromDataStore(DataStoreClientInterface $store): MessageCollection
    {
        try {
            $collection = MessageCollection::fromArray($store->getData());
        } catch (MessageCollectionException $e) {
            throw MessageServiceException::rethrow($e);
        }
        return $collection;
    }

    /**
     * @param MessageInterface $message
     * @return bool
     * @throws MessageServiceException
     */
    public function sendMessage(MessageInterface $message): bool
    {
        try {
            switch ($message->getType()) {
                case MessageType::TYPE_SMS:
                    $this->comms->sendSms($message->getRecipient(), $this->senderSms, $message->getContent());
                    break;
                case MessageType::TYPE_VOICE:
                    $this->comms->sendVoice($message->getRecipient(), $this->senderVoice, $message->getContent());
                    break;
                default:
                    throw MessageServiceException::unrecognisedMessageType($message->getType());
                    break;
            }
        } catch (CommsHandlerClientException $e) {
            throw MessageServiceException::rethrow($e);
        }

        return true;
    }
}
