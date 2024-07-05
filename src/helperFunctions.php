<?php

use swentel\nostr\Relay\Relay;
use swentel\nostr\Relay\RelaySet;
use swentel\nostr\Message\EventMessage;
use swentel\nostr\Sign\Sign;
use swentel\nostr\Event\Event;
use swentel\nostr\CommandResultInterface;

/**
 * Signs the note, reads the relays out of relays.yml and uses them to prepare a note
 *
 * @param Event $note
 * @return CommandResultInterface $result
 */
function prepareEvent($note)
{
    $keyFile = getcwd()."/user/nostr-private.key";
    $privateKey = trim(file_get_contents($keyFile));

    // check to make sure that there is an nsec in the keyfile.
    (str_starts_with($privateKey, 'nsec') === false) ? throw new InvalidArgumentException('Please place your nsec in the nostr-private.key file.') : $privateKey;

    $relaysFile = getcwd()."/user/relays.yml";
    $relaysRead=array();
    $relaysRead = file($relaysFile, FILE_IGNORE_NEW_LINES);
    (empty($relaysRead)) ? ($relaysRead = ["wss://thecitadel.nostr1.com"]) : $relaysRead;

    $signer = new Sign();
    $signer->signEvent($note, $privateKey);
  
    $eventMessage = new EventMessage($note);

    foreach ($relaysRead as &$relay) {

        $relays[] = new Relay($relay);
    }

    $relaySet = new RelaySet();
    $relaySet->setRelays($relays);
    $relaySet->setMessage($eventMessage);
    $result = $relaySet->send();

    return $result;
}

/**
 * Prints the event data to a file.
 *
 * @param string $eventKind
 * @param string $eventID
 * @param string $title
 * @param string $shortTitle #optional
 * @param string $author #optional
 * @return void
 */
function printEventData($eventKind, $eventID, $title, $shortTitle="-----", $author="----")
{
    $fullpath = getcwd()."/eventsCreated.yml";
    $fp = fopen($fullpath, "a");
        fwrite($fp, "event ID: ".$eventID.PHP_EOL);
        fwrite($fp, "  event kind: ".$eventKind.PHP_EOL);
        fwrite($fp, "  event title: ".$title.PHP_EOL);
        fwrite($fp, "  short title: ".$shortTitle.PHP_EOL);
        fwrite($fp, "  author: ".$author.PHP_EOL.PHP_EOL);
    fclose($fp);
}