<?php

include_once __DIR__.'/vendor/autoload.php';
include_once 'src/BookEvent.php';
include_once 'src/helperFunctions.php';

echo PHP_EOL;

// read in markdown file and arguments passed
// write book into events
$book = new BookEvent();
$book->set_book_arguments($argv);

if (empty($bookArguments[1])) {
    throw new InvalidArgumentException('The markdown file path is missing.');
}
if (empty($bookArguments[2])) {
    throw new InvalidArgumentException('The author is missing.');
}
if (empty($bookArguments[3])) {
    throw new InvalidArgumentException('The version is missing.');
}
if (empty($bookArguments[4]) || ($bookArguments[4] != 'e' || $bookArguments[4] != 'a')) {
    throw new InvalidArgumentException('The event type (e/a) is missing.');
}

try {
    $book->publish_book();
    echo "The book has been written.".PHP_EOL.PHP_EOL;
} catch (Exception $e) {
    echo $e->getMessage().PHP_EOL.PHP_EOL;
}