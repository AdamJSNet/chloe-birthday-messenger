<?php

use App\Clients\LocalDataStoreClient;
use App\Clients\NexmoCommsHandlerClient;
use App\Exceptions\MessageCollectionException;
use App\Exceptions\MessageServiceException;
use App\Functions;
use App\Services\MessageService;

require_once("src/bootstrap.php");

try {
    $console->info("Running script...");

    // In the absence of an IoC container, create our clients manually
    $nexmo = new NexmoCommsHandlerClient(Functions::getNexmoClient());
    $messageService = new MessageService($nexmo);

    // Get all messages
    $store = (new LocalDataStoreClient(
        new SplFileObject(ROOT_DIR . "/" . getenv("MESSAGES_FILENAME"), "r+")
    ))->load();
    $messageCollection = $messageService->getAllMessagesFromDataStore($store);

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

    /** @var App\Contracts\MessageInterface $message */
    foreach ($messages as $message) {
        try {
            $messageService->sendMessage($message);
        } catch (MessageServiceException $e) {
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
