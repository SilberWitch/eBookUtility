<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
include_once 'helperFunctions.php';
include_once 'SectionEvent.php';
include_once 'BookEvent.php';

final class IntegrationTest extends TestCase
{
    public function testSourcefileHasTwoHeaderLevels(): void
    {
        $testFile =  getcwd()."/src/testdata/testfiles/AesopsFables_2Headers.adoc";
        $testArgv = ['createBook.php', $testFile, 'Æsop', 'test version with e tags', 'e'];
        $book = new BookEvent();
        $book->set_book_arguments($testArgv);
        $book->publish_book();
        $this->assertTrue(true);
    }

    public function testSourcefileHasThreeHeaderLevels(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $testFile =  getcwd()."/src/testdata/testfiles/AesopsFables_3Headers.adoc";
        $testArgv = ['createBook.php', $testFile, 'Æsop', 'test version with e tags', 'e'];
        $book = new BookEvent();
        $book->set_book_arguments($testArgv);
        $book->publish_book();
    }

    public function testSourcefileHasOneHeaderLevel(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $testFile =  getcwd()."/src/testdata/testfiles/AesopsFables_1Header.adoc";
        $testArgv = ['createBook.php', $testFile, 'Æsop', 'test version with e tags', 'e'];
        $book = new BookEvent();
        $book->set_book_arguments($testArgv);
        $book->publish_book();
    }

    public function testSourcefileHasNoHeaders(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $testFile =  getcwd()."/src/testdata/testfiles/AesopsFables_0Headers.adoc";
        $testArgv = ['createBook.php', $testFile, 'Æsop', 'test version with e tags', 'e'];
        $book = new BookEvent();
        $book->set_book_arguments($testArgv);
        $book->publish_book();
    }

    public function testRelayListIsEmpty(): void
    {
        // save the relay list, to return it back to normal after the test
        $relaysFile = getcwd()."/user/relays.yml";
        $relaysRead=array();
        $relaysRead = file($relaysFile, FILE_IGNORE_NEW_LINES);

        // delete the contents of the file
        file_put_contents($relaysFile, "");

        // make sure that book can still be printed using the default Citadel relay.
        $testFile =  getcwd()."/src/testdata/testfiles/AesopsFables_2Headers.adoc";
        $testArgv = ['createBook.php', $testFile, 'Æsop', 'test version with a tags', 'a'];
        $book = new BookEvent();
        $book->set_book_arguments($testArgv);
        $book->publish_book();

        $this->assertTrue(true);

        // put the original contents of the file back.
        foreach ($relaysRead as &$relay) {
            file_put_contents($relaysFile, $relay.PHP_EOL, FILE_APPEND);
        }

    }
}