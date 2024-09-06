<?php

use swentel\nostr\Event\Event;
include_once 'helperFunctions.php';
include_once 'BookEvent.php';
class SectionEvent{

// Properties

public $sectionDTag;
public $sectionTitle;
public $sectionAuthor;
public $sectionContent;

// Methods

function set_section_d_tag($sectionDTag) {
  $this->sectionDTag = $sectionDTag;
}

function get_section_d_tag() {
  return $this->sectionDTag;
}

function set_section_title($sectionTitle) {
  $this->sectionTitle = $sectionTitle;
}

function get_section_title() {
  return $this->sectionTitle;
}

function set_section_author($sectionAuthor) {
  $this->sectionAuthor = $sectionAuthor;
}
function get_section_author() {
  return $this->sectionAuthor;
}
function set_section_content($sectionContent) {
  $this->sectionContent = $sectionContent;
}
function get_section_content() {
  return $this->sectionContent;
}

/**
 * Create a section event.
 * Returns the eventID for the section event.
 *
 * @return string $eventID
 */
function create_section()
{
  $kind = "30041";
  $note = new Event();
  $note->setContent($this->get_section_content());
  $note->setKind($kind);
  $note->setTags([
    ['d', $this->get_section_d_tag()],
    ['title', $this->get_section_title()],
    ['author', $this->get_section_author()],
  ]);

  $event = prepare_event_data($note);

  // issue the eventID, pause to prevent the relay from balking, and retry on fail
  $i = 0;
  do {
    $eventID = $event->getEventID();
    $i++;
    sleep(5);
  } while (($i <= 10) && empty($eventID));

  (empty($eventID)) ? throw new InvalidArgumentException('The section eventID was not created') : $eventID;
  
  echo "Published ".$kind." event with ID ".$eventID.PHP_EOL;
  print_event_data($kind, $eventID, $this->get_section_d_tag());
  return $eventID;

}

}