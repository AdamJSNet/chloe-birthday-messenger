<?php

use App\Clients\LocalDataStoreClient;
use App\Clients\NexmoCommsHandlerClient;
use App\Collections\MessageCollection;
use App\Enum\MessageType;
use App\Exceptions\CommsHandlerClientException;
use App\Exceptions\MessageCollectionException;
use App\Functions;

require_once("src/bootstrap.php");

try {
    $console->info("Running script...");

    // instantiate data store from local file system
    $store = (new LocalDataStoreClient(
        new SplFileObject(ROOT_DIR . "/" . getenv("MESSAGES_FILENAME"), "r+")
    ))->load();

    // create collection of all messages
    $messageCollection = MessageCollection::fromArray($store->getData());

    // retrieve messages to be processed in this iteration only
    $messages = $messageCollection->elapsed()->notSent();

    // summarise
    $count = $messages->count();
    $info = "$count " . Functions::pluralise($count, "message", "messages") . " to process";
    $console->info($info);

    if ($count === 0) {
        Functions::end($console);
    }

    $success = [];
    $fail = [];

    // instantiate our comms client
    $nexmo = new NexmoCommsHandlerClient(Functions::getNexmoClient());
    $sender = getenv("NEXMO_SENDER");

    /** @var App\Contracts\MessageInterface $message */
    foreach ($messages as $message) {
        try {
            switch ($message->getType()) {
                case MessageType::TYPE_SMS:
                    $nexmo->sendSms($message->getRecipient(), $sender, $message->getContent());
                    break;
                default:
                    throw new Exception("Unrecognised Message Type");
                    break;
            }
        } catch (CommsHandlerClientException $e) {
            $console->error($e->getMessage());
            $fail[] = $message->getId();
            continue;
        }

        $message->setSent(true);

        try {
            $messages->update($message);
        } catch (MessageCollectionException $e) {
            $console->error($e->getMessage());
            $fail[] = $message->getId();
            continue;
        }

        $console->info("Sent message ID = " . $message->getId());
        $success[] = $message->getId();
    }

    $store->setData($messages->toArray())->save();

    $countSuccess = count($success);
    $countFail = count($fail);
    $info = "$countSuccess " . Functions::pluralise($countSuccess, "message", "messages")
        . " processed successfully. $countFail failed.";
    $console->info($info);
} catch (Exception $e) {
    $console->error("*** ABORTED *** " . $e->getMessage());
}
