<?php

use swentel\nostr\Relay\Relay;
use swentel\nostr\Relay\RelaySet;
use swentel\nostr\Message\EventMessage;
use swentel\nostr\Sign\Sign;
use swentel\nostr\Event\Event;
use swentel\nostr\CommandResultInterface;

/**
 * Gets the original title and the author and constructs a d tag, which it returns.
 *
 * @param string $title
 * @param string $author # optional
 * @param string $version # optional
 * @return string $dTag
 */
function construct_d_tag($title, $author="unknown", $version="")
{
    $dTagAuthor = strval(preg_replace('/\s+/', '-', $author));
    $dTagTitle = strval(preg_replace('/\s+/', '-', $title));

    if(strlen($version) === 0){
        $dTag = $dTagTitle . "-by-" . $dTagAuthor;
    } else {
        $dTagVersion = strval(preg_replace('/\s+/', '-', $version));
        $dTag = $dTagTitle . "-by-" . $dTagAuthor . "-v-" . $dTagVersion;
    }

    return $dTag;
}

/**
 * Signs the note, reads the relays out of relays.yml and uses them to prepare a note
 *
 * @param Event $note
 * @return CommandResultInterface $result
 */
function prepare_event_data($note)
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
 * @param string $dTag
 * @return void
 */
function print_event_data($eventKind, $eventID, $dTag)
{
    $fullpath = getcwd()."/eventsCreated.yml";
    $fp = fopen($fullpath, "a");
        fwrite($fp, "event ID: ".$eventID.PHP_EOL);
        fwrite($fp, "  event kind: ".$eventKind.PHP_EOL);
        fwrite($fp, "  d Tag: ".$dTag.PHP_EOL);
    fclose($fp);
}