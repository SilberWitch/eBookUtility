<?php

use swentel\nostr\Event\Event;
include_once 'helperFunctions.php';
include_once 'SectionEvent.php';

class BookEvent{

// Properties

public $bookArguments;
public $bookDTag;
public $bookTitle;
public $bookAuthor;
public $bookVersion;
public $sectionEvents = array();

// Methods

function set_book_arguments($bookArguments) {
  $this->bookArguments = $bookArguments;
}

function get_book_arguments() {
  return $this->bookArguments;
}

function set_book_d_tag($bookDTag) {
  $this->bookDTag = $bookDTag;
}

function get_book_d_tag() {
  return $this->bookDTag;
}

function set_book_title($bookTitle) {
  $this->bookTitle = $bookTitle;
}

function get_book_title() {
  return $this->bookTitle;
}

function set_book_author($bookAuthor) {
  $this->bookAuthor = $bookAuthor;
}

function get_book_author() {
  return $this->bookAuthor;
}

function set_book_version($bookVersion) {
  $this->bookVersion = $bookVersion;
}

function get_book_version() {
  return $this->bookVersion;
}

function set_section_events($sectionEvents) {
  $this->sectionEvents[] = $sectionEvents;
}

function get_section_events() {
  return $this->sectionEvents;
}

/**
 * Create an index event and hang on the associated section events
 * Returns the eventID for the section event.
 *
 * @return void
 */
function publish_book()
{

    $markdown = "unset";
    if(!$markdown = file_get_contents($this->bookArguments[1])) throw new InvalidArgumentException('The file could not be found.');
    $markdown = file_get_contents($this->bookArguments[1]);
    $this->set_book_author($this->bookArguments[2]);
    $this->set_book_version($this->bookArguments[3]);
    
    // check if the file contains too many header levels
    (stripos($markdown,'=== ') !== false) ? throw new InvalidArgumentException('This markdown file contains too many header levels. Please correct down to 2 levels and retry.') : $markdown;

    // break the file into metadata and sections
    $markdownFormatted = explode("== ", $markdown);

    // check if the file contains too few header levels
    (count($markdownFormatted) === 1) ? throw new InvalidArgumentException('This markdown file contain no headers or only one level of headers. Please add a second level and retry.') : $markdownFormatted;

    $bookTitle= array_shift($markdownFormatted);
    $this->set_book_title(trim(trim($bookTitle, "= ")));

    $title = $this->get_book_title();
    $author = $this->get_book_author();
    $version = $this->get_book_version();
    $dTag = construct_d_tag($title, $author, $version); 
    $this->set_book_d_tag($dTag);

    echo PHP_EOL;

    // write the 30041s from the == sections and add the eventID to the section array
    
    $sectionNum = 0;
    foreach ($markdownFormatted as &$section) {
      $sectionNum++;
      $sectionTitle = trim(strstr($section, "\n", true));
      $nextSection = new SectionEvent();
        $nextSection->set_section_author($this->bookAuthor);
        $nextSection->set_section_version($this->bookVersion);
                $nextSection->set_section_title($sectionTitle);
        $nextSection->set_section_d_tag(construct_d_tag($this->get_book_title()."-".$nextSection->get_section_title()."-".$sectionNum, $nextSection->get_section_author(), $nextSection->get_section_version()));
        $nextSection->set_section_content(trim(trim(strval($section), $sectionTitle)));
      $this->set_section_events($nextSection->create_section());

      }

    // write the 30040 and add the new 30041s
    $indexID = $this->create_book();

    // print a njump hyperlink to the 30040
    print "https://njump.me/".$indexID.PHP_EOL;

    return;
}

/**
 * Create an index event and hang on the associated section events.
 * Returns the index event ID.
 *
 * @param BookEvent
 * @return string $resultID
 */
function create_book()
{
  $kind = "30040";

  $tags[] = ['d', $this->get_book_d_tag()];
  $tags[] = ['title', $this->get_book_title()];
  $tags[] = ['author', $this->get_book_author()];
  $tags[] = ['version', $this->get_book_version()];
  $tags[] = ['type', 'book'];
  foreach ($this->get_section_events() as &$etags) {
    $tags[] = ['e', $etags];
  }

  $note = new Event();
  $note->setKind($kind);
  $note->setTags($tags);
  $note->setContent("");

  prepare_event_data($note);

  // issue the eventID, pause to prevent the relay from balking, and retry on fail
  $i = 0;
  do {
    $eventID = $note->getId();
    $i++;
    sleep(5);
  } while (($i <= 10) && empty($eventID));

  (empty($eventID)) ? throw new InvalidArgumentException('The book eventID was not created') : $eventID;

  echo "Published ".$kind." event with ID ".$eventID.PHP_EOL.PHP_EOL;
  print_event_data($kind, $eventID, $this->get_book_d_tag());
  return $eventID;
}

}