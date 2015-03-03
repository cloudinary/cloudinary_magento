<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use CloudinaryExtension\Image;
use SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;


class AdminConfigurationContext extends PageObjectContext implements Context
{
    /**
     * @Given I have not set a default image gravity
     */
    public function iHaveNotSetADefaultImageGravity()
    {
        throw new PendingException();
    }

    /**
     * @When I go to the cloudinary configuration
     */
    public function iGoToTheCloudinaryConfiguration()
    {
        throw new PendingException();
    }

    /**
     * @Then no gravity should be selected yet
     */
    public function noGravityShouldBeSelectedYet()
    {
        throw new PendingException();
    }

    /**
     * @Given I have set a the default image gravity to :arg1
     */
    public function iHaveSetATheDefaultImageGravityTo($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then the default gravity should be set to :arg1
     */
    public function theDefaultGravityShouldBeSetTo($arg1)
    {
        throw new PendingException();
    }
}
