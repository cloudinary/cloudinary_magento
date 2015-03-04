<?php

namespace Ui;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use CloudinaryExtension\Image;
use MageTest\MagentoExtension\Context\RawMagentoContext;
use Page\AdminLogin;
use Page\CloudinaryAdminSystemConfiguration;
use SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;


class AdminConfigurationContext extends RawMagentoContext implements Context
{
    /**
     * @var AdminLogin
     */
    private $adminLoginPage;

    /**
     * @var CloudinaryAdminSystemConfiguration
     */
    private $adminConfigPage;

    public function __construct(CloudinaryAdminSystemConfiguration $adminConfigPage, AdminLogin $adminLoginPage)
    {
        $this->adminLoginPage = $adminLoginPage;
        $this->adminConfigPage = $adminConfigPage;
    }

    /**
     * @beforeScenario
     */
    public function login()
    {
        $this->adminLoginPage->sessionLogin('testadmin', 'testadmin123', $this->getSessionService());
    }

    /**
     * @Given the default gravity is not set
     */
    public function iHaveNotSetADefaultImageGravity()
    {
        $mageConfig = \Mage::helper('cloudinary_cloudinary/configuration');
        $mageConfig->setDefaultGravity('');
    }

    /**
     * @Given the default gravity is set to :gravity
     */
    public function theDefaultGravityIsSetTo($gravity)
    {
        $mageConfig = \Mage::helper('cloudinary_cloudinary/configuration');
        $mageConfig->setDefaultGravity($gravity);
    }

    /**
     * @When I go to the cloudinary configuration
     */
    public function iGoToTheCloudinaryConfiguration()
    {
        $this->adminConfigPage->open();
    }

    /**
     * @Then no gravity should be selected yet
     */
    public function noGravityShouldBeSelectedYet()
    {
        expect($this->adminConfigPage->getSelectedGravity())->toBe('Select gravity');
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
     * @Then the default gravity should be set to :gravity
     */
    public function theDefaultGravityShouldBeSetTo($gravity)
    {
        expect($this->adminConfigPage->getSelectedGravity())->toBe($gravity);
    }
}
