<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
include_once 'helperFunctions.php';
include_once 'SectionEvent.php';
include_once 'BookEvent.php';

final class IntegrationTest extends TestCase
{
    public function testSourcefileHas_Atags(): void
    {
        $testFile =  getcwd()."/src/testdata/testfiles/AesopTest_a.yml";
        $return = shell_exec('php createBook.php '.$testFile);
        $this->assertStringContainsString('Published 30040 event with a tags', $return);
        $this->assertStringContainsString('The book has been written.', $return);
    }

    public function testSourcefileHas_Etags(): void
    {
        $testFile =  getcwd()."/src/testdata/testfiles/AesopTest_e.yml";
        $return = shell_exec('php createBook.php '.$testFile);
        $this->assertStringContainsString('Published 30040 event with e tags', $return);
        $this->assertStringContainsString('The book has been written.', $return);
    }

    
    public function testRelayListIsEmpty(): void
    {
        // save the relay list, to return it back to normal after the test
        $relaysFile = getcwd()."/user/relays.yml";
        $relaysRead = [];
        $relaysRead = file($relaysFile, FILE_IGNORE_NEW_LINES);

        // delete the contents of the file
        file_put_contents($relaysFile, "");

        // make sure that book can still be printed using the default Citadel relay.
        $testFile =  getcwd()."/src/testdata/testfiles/AesopTest_a.yml";
        $return = shell_exec('php createBook.php '.$testFile);
        $this->assertStringContainsString('The book has been written.', $return);
    
        // put the original contents of the file back.
        foreach ($relaysRead as &$relay) {
            file_put_contents($relaysFile, $relay, FILE_APPEND);
        }

    }
}