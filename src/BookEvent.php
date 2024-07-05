<?php

use swentel\nostr\Event\Event;
include 'helperFunctions.php';

class BookEvent{

// Properties

public $bookArguments;
public $shortTitle;
public $title;
public $author;
public $articles = array();

// Methods

function set_book_arguments($bookArguments) {
  $this->bookArguments = $bookArguments;
}
function get_book_arguments() {
  return $this->bookArguments;
}

  function set_short_title($shortTitle) {
    $this->shortTitle = $shortTitle;
  }
  function get_short_title() {
    return $this->shortTitle;
  }
  function set_title($title) {
    $this->title = $title;
  }
  function get_title() {
    return $this->title;
  }

  function set_author($author) {
    $this->author = $author;
  }
  function get_author() {
    return $this->author;
  }
  function set_articles($articles) {
    $this->articles[] = $articles;
  }
  function get_articles() {
    return $this->articles;
  }

/**
 * Create a header event and hang on the associated article events
 * Returns the eventID for the article event.
 *
 * @param string $title
 * @param string $sectionContent
 * @return string $resultID
 */
function create_articles($title, $sectionContent)
{
  $note = new Event();
  $note->setContent($sectionContent);
  $note->setKind(30041);
  $note->setTags([
    ['d', $this->title],
    ['title', $this->title],
  ]);

  $result = prepareEvent($note);
  $resultID = $result->getEventID();

  // check if the eventID was issued retry and then fail
  (empty($resultID)) ? ($resultID = $result->getEventID()) : $resultID;
  (empty($resultID)) ? throw new InvalidArgumentException('The article eventID was not created') : $resultID;
  

  echo "Published 30041 event with ID ".$resultID.PHP_EOL;
  printEventData("30041", $resultID, $this->title);
  return $resultID;
}

/**
 * Create a header event and hang on the associated article events.
 * Returns the header event ID.
 *
 * @param string $shortTitle
 * @param string $title
 * @param string $author
 * @param array $articles
 * @return string $resultID
 */
function create_book($shortTitle, $title, $author, $articles)
{
  $tags[] = ['d', $this->shortTitle];
  foreach ($this->articles as &$etags) {
    $tags[] = ['e', $etags];
  }

  $note = new Event();
  $note->setContent("{\"title\": \"$this->title\", \"author\": \"$this->author\"}");
  $note->setKind(30040);
  $note->setTags($tags);

  $result = prepareEvent($note);
  $resultID = $result->getEventID();
  
  // check if the eventID was issued retry and then fail
  (empty($resultID)) ? ($resultID = $result->getEventID()) : $resultID;
  (empty($resultID)) ? throw new InvalidArgumentException('The header eventID was not created') : $resultID;


  echo "Published 30040 event with ID ".$resultID.PHP_EOL.PHP_EOL;
  printEventData("30040", $resultID, $this->title, $this->shortTitle, $this->author);
  return $resultID;
}

/**
 * Create a header event and hang on the associated article events
 * Returns the eventID for the article event.
 *
 * @return void
 */
function publish_book($bookArguments)
{

    $markdown = "unset";
    $markdown = file_get_contents($this->bookArguments[1]);
    $this->set_author($this->bookArguments[2]);

    // check if the file contains too many header levels
    (stripos($markdown,'###') !== false) ? throw new InvalidArgumentException('This markdown file contains too many header levels. Please correct down to 2 levels and retry.') : $markdown;

    // break the file into metadata and sections
    $markdownFormatted = explode("##", $markdown);

    // check if the file contains too few header levels
    (count($markdownFormatted) === 1) ? throw new InvalidArgumentException('This markdown file contain no headers or only one level of headers. Please add a second level and retry.') : $markdownFormatted;

    $this->set_short_title(trim($this->bookArguments[1], ".md"));
    $bookTitle = array_shift($markdownFormatted);
    $this->set_title(trim(trim($bookTitle, "# ")));

    echo PHP_EOL;

    // write the 30041s from the ## sections and add the eventID to the articles array

    foreach ($markdownFormatted as &$section) {

        $sectionTitle = trim(substr($section, 0, strpos($section, PHP_EOL)));
        $sectionContent = trim($section, $sectionTitle);
        $sectionEventID = $this->create_articles($sectionTitle, $sectionContent);

        $this->set_articles($sectionEventID);
    }

    // write the 30040 and add the new 30041s
    $headerID = $this->create_book($this->get_short_title(), $this->get_title(), $this->get_author(), $this->get_articles());

    // print a njump hyperlink to the 30040
    print "https://njump.me/".$headerID.PHP_EOL;

    return;
}

}