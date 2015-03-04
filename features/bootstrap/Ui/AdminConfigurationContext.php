<?php

namespace Ui;

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
        $mageConfig = \Mage::helper('cloudinary_cloudinary/configuration');
        $mageConfig->setDefaultGravity('');
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
     * @Given I have set a the default image gravity to :gravity
     */
    public function iHaveSetATheDefaultImageGravityTo($gravity)
    {
        $mageConfig = \Mage::helper('cloudinary_cloudinary/configuration');
        $mageConfig->setDefaultGravity($gravity);
    }

    /**
     * @Then the default gravity should be set to :arg1
     */
    public function theDefaultGravityShouldBeSetTo($arg1)
    {
        throw new PendingException();
    }
}
