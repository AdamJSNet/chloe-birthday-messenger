# Chloe's Birthday Assistant

#### v1.0.0

## Abooot

A small project built to drip-feed Chloe's Edinburgh-oriented birthday itinerary via SMS and Voice calls.

(Because pen and paper felt super lame for a 30th, and my GitHub was looking a bit sparse).

Integrated with Nexmo's SDK for issuing of comms because ~they're super easy to get going with~
I had 10EUR free credit to use up (which I successfully achieved within my first day of testing :thumbsup:)

Built from scratch with a simple bootstrap/some packages - no framework (because retro).

Requires PHP7+ which you're using anyway (because security).

## Start me up

From the project root...

```
composer install
cp .env.example .env
```

You'll need to register with [Nexmo](https://www.nexmo.com/) and amend the Nexmo-specific env variables in your amazing new .env file.

Also, download your Nexmo application's private key and save it as `private.key` in the `/keys` directory.

The entrypoint `scripts/send-pending-messages.php` is designed to run on a cron, every 15 mins.

```
*/15 * * * * /path/to/scripts/send-pending-messages.php >/dev/null 2>&1
```

This script mops up all "elapsed" messages (based on the current timestamp) which have not yet been sent, and... sends them.

The system user that executes the script will need to have write permissions on the `/logs` directory.

I've added the schedule of messages to the `/data` folder so that you can see how brilliantly diverse Edinburgh is as a birthday destination.
The recipient contact details are obfuscated, so please change these to valid numbers in that event that you might actually genuinely consider running this project IRL.

There is also a `/public` directory which you can use to serve HTTP requests.

The `event.php` file is meant to be a Nexmo webhook destination. There are also some files in the `/audio` directory, which are used as streaming audio

If you are planning to serve these assets, remember to use [ngrok](https://ngrok.com/) for tunnelling to your local environment (and update the paths in the `data/messages.json` manifest accordingly). 

## Tests

Run tests, run.

(From the project root...)

```
vendor/bin/phpunit
```

## The future

Maybe another data source `CommsHandlerClient` class (e.g. for *Google Sheets*),
meaning that I don't have to keep ssh'ing onto a server and modifying raw JSON
whenever the birthday itinerary changes due to Great British weather... (:thumbsup:)

Maybe an integration with the Thomas Cook VR Holiday API, so I don't have to leave my house for next year's 31st?
