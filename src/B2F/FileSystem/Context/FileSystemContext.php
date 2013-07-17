<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

class FileSystemContext extends BehatContext {
 
    public static $fileSystemController;

    /**
     * @BeforeFeature
     */
    public static function prepare() {
        self::$fileSystemController = new FileSystemController('behat_stubs');
    }
 
    /**
     * @Given /^I am in a directory with subfolders$/
     */
    public function iAmInADirectoryWithSubfolders(TableNode $dirTable)
    {
        self::$fileSystemController->createDirectoryStructure($dirTable->getHash());
    }

    /**
     * @Given /^I have a "([^"]*)" file$/
     */
    public function iHaveAFile($filepath)
    {
        self::$fileSystemController->createFile($filepath);
    }

    /**
     * @Given /^I have a "([^"]*)" file with:$/
     */
    public function iHaveAFileWith($filepath, PyStringNode $text)
    {
        if (self::$fileSystemController->createFile($filepath, (string) $text) === FALSE) {
          throw new Exception('Failed to initialize the file ' . $filepath);
        }
    }

    /**
     * @When /^I run "([^"]*)" "([^"]*)"$/
     */
    public function iRun($command, $arguments)
    {
        $output = system($command . ' ' . escapeshellarg($arguments), $retval);
        if ($output === FALSE || $retval != 0) {
            throw new Exception();
        }
    }

    /**
     * @Then /^I should have a "([^"]*)" file$/
     */
    public function iShouldHaveAFile($filepath)
    {
        if (!file_exists($filepath)) {
          throw new Exception('The file ' . $filepath . ' is missing');
        }
    }

    /**
     * @Then /^I should have a file "([^"]*)" with content matching:$/
     */
    public function IShouldHaveAFileWithContentMatching($filepath, PyStringNode $text)
    {
        if (self::$fileSystemController->fileContains($filepath, $text) != 1) {
            throw new Exception($filepath . ' ' . 'does not contain ' . $text);
        }
    }

    /**
     * @AfterFeature
     */
    static public function cleanUp() {
      self::$fileSystemController->deleteDirectoryRecursively('behat_stubs');
    }

}
