<?php declare(strict_types=1);

include 'BookEvent.php';
include_once __DIR__.'/../vendor/autoload.php';
use PHPUnit\Framework\TestCase;

final class IntegrationTest extends TestCase
{
    public function testSourcefileHasTwoHeaderLevels(): void
    {
        $testFile =  getcwd()."/src/testdata/AesopsFables_2Headers.md";
        $testArgv = ["createBook.php", $testFile, "Æsop"];
        $book = new BookEvent();
        $book->set_book_arguments($testArgv);
        $book->publish_book($book->get_book_arguments());
        $this->assertTrue(true);
    }

    public function testSourcefileHasThreeHeaderLevels(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $testFile =  getcwd()."/src/testdata/AesopsFables_3Headers.md";
        $testArgv = ["createBook.php", $testFile, "Æsop"];
        $book = new BookEvent();
        $book->set_book_arguments($testArgv);
        $book->publish_book($book->get_book_arguments());
    }

    public function testSourcefileHasOneHeaderLevel(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $testFile =  getcwd()."/src/testdata/AesopsFables_1Header.md";
        $testArgv = ["createBook.php", $testFile, "Æsop"];
        $book = new BookEvent();
        $book->set_book_arguments($testArgv);
        $book->publish_book($book->get_book_arguments());
    }

    public function testSourcefileHasNoHeaders(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $testFile =  getcwd()."/src/testdata/AesopsFables_0Headers.md";
        $testArgv = ["createBook.php", $testFile, "Æsop"];
        $book = new BookEvent();
        $book->set_book_arguments($testArgv);
        $book->publish_book($book->get_book_arguments());
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
        $testFile =  getcwd()."/src/testdata/AesopsFables_2Headers.md";
        $testArgv = ["createBook.php", $testFile, "Æsop"];
        $book = new BookEvent();
        $book->set_book_arguments($testArgv);
        $book->publish_book($book->get_book_arguments());

        $this->assertTrue(true);

        // put the original contents of the file back.
        foreach ($relaysRead as &$relay) {
            file_put_contents($relaysFile, $relay.PHP_EOL, FILE_APPEND);
        }

    }
}