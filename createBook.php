<?php

include_once __DIR__.'/vendor/autoload.php';
include_once 'src/BookEvent.php';
include_once 'src/helperFunctions.php';

echo PHP_EOL;

// read in markdown file and arguments passed
// write book into events
$book = new BookEvent();
$book->set_book_arguments($argv);
try {
    $book->publish_book();
    echo "The book has been written.".PHP_EOL.PHP_EOL;
} catch (Exception $e) {
    echo $e->getMessage().PHP_EOL.PHP_EOL;
}