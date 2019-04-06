<?php

require_once("src/bootstrap.php");

try {
    $console->info("Running script...");

    // instantiate data store from local file system
    $store = (new App\Clients\LocalDataStoreClient(
        new SplFileObject(ROOT_DIR . "/" . getenv("MESSAGES_FILENAME"), "r+")
    ))->load();

    // create collection of all messages
    $messageCollection = App\Collections\MessageCollection::fromArray($store->getData());

    // retrieve messages to be processed in this iteration only
    $messages = $messageCollection->elapsed()->notSent();

    // summarise
    $count = $messages->count();
    $info = "$count " . App\Functions::pluralise($count, "message", "messages") . " to process";
    $console->info($info);

    if ($count === 0) {
        App\Functions::end($console);
    }

    $success = [];
    $fail = [];

    /** @var App\Contracts\MessageInterface $message */
    foreach ($messages as $message) {
        // @TODO process message...

        $message->setSent(true);

        try {
            $messages->update($message);
            $success[] = $message;
        } catch (App\Exceptions\MessageCollectionException $e) {
            $fail[] = $message;
        }
    }

    $store->setData($messages->toArray())->save();

    $countSuccess = count($success);
    $countFail = count($fail);
    $info = "$countSuccess " . App\Functions::pluralise($countSuccess, "message", "messages")
        . " processed successfully. $countFail failed.";
    $console->info($info);
} catch (Exception $e) {
    $console->error("*** ABORTED *** " . $e->getMessage());
}
