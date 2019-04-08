<?php

use App\Clients\LocalDataStoreClient;
use App\Clients\NexmoCommsHandlerClient;
use App\Exceptions\MessageCollectionException;
use App\Exceptions\MessageServiceException;
use App\Functions;
use App\Services\MessageService;

require_once("src/bootstrap.php");

try {
    // In the absence of an IoC container, create our clients manually
    $nexmo = new NexmoCommsHandlerClient(Functions::getNexmoClient());
    $messageService = new MessageService($nexmo);

    // Get all messages
    $store = (new LocalDataStoreClient(
        new SplFileObject(ROOT_DIR . "/" . getenv("MESSAGES_FILENAME"), "r+")
    ))->load();
    $messageCollection = $messageService->getAllMessagesFromDataStore($store);

    // retrieve messages to be processed in this iteration only
    $filtered = $messageCollection->elapsed()->notSent();

    // summarise
    $count = $filtered->count();
    $info = "$count " . Functions::pluralise($count, "message", "messages") . " to process";
    $log->info($info);

    if ($count === 0) {
        die();
    }

    $success = [];
    $fail = [];

    /** @var App\Contracts\MessageInterface $message */
    foreach ($filtered as $message) {
        try {
            $messageService->sendMessage($message);
        } catch (MessageServiceException $e) {
            $log->error($e->getMessage());
            $fail[] = $message->getId();
            continue;
        }

        $message->setSent(true);

        try {
            $messageCollection->update($message);
        } catch (MessageCollectionException $e) {
            $log->error($e->getMessage());
            $fail[] = $message->getId();
            continue;
        }

        $log->info("Sent message ID = " . $message->getId());
        $success[] = $message->getId();
    }

    $store->setData($messageCollection->toArray())->save();

    $countSuccess = count($success);
    $countFail = count($fail);
    $info = "$countSuccess " . Functions::pluralise($countSuccess, "message", "messages")
        . " processed successfully. $countFail failed.";
    $log->info($info);
} catch (Exception $e) {
    $log->error("*** ABORTED *** " . $e->getMessage());
}
$log->info("--------------------");
