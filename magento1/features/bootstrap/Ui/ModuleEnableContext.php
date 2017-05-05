<?php

namespace Ui;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use MageTest\MagentoExtension\Context\RawMagentoContext;
use Page\AdminLogin;
use Page\CloudinaryManagement;

class ModuleEnableContext extends RawMagentoContext implements Context, SnippetAcceptingContext
{

    private $adminLogin;

    private $cloudinaryManagement;

    public function __construct(AdminLogin $adminLogin, CloudinaryManagement $cloudinaryManagement)
    {
        $this->adminLogin = $adminLogin;
        $this->cloudinaryManagement = $cloudinaryManagement;
    }

    /**
     * @Given I am logged in as an administrator
     */
    public function iAmLoggedInAsAnAdministrator()
    {
        $this->adminLogin->sessionLogin('testadmin', 'testadmin123', $this->getSessionService());
    }

    /**
     * @Given the Cloudinary module is disabled
     */
    public function theCloudinaryModuleIsDisabled()
    {
        \Mage::helper('cloudinary_cloudinary/configuration')->disable();
    }

    /**
     * @When I access the Cloudinary configuration
     */
    public function iAccessTheCloudinaryConfiguration()
    {
        $this->cloudinaryManagement->open();
    }

    /**
     * @Then I should be able to enable the module
     */
    public function iShouldBeAbleToEnableTheModule()
    {
        $this->cloudinaryManagement->enable();

        expect($this->cloudinaryManagement)->toHaveDisableButton();
    }

    /**
     * @Given the Cloudinary module is enabled
     */
    public function theCloudinaryModuleIsEnabled()
    {
        \Mage::helper('cloudinary_cloudinary/configuration')->enable();
    }

    /**
     * @Then I should be able to disable the module
     */
    public function iShouldBeAbleToDisableTheModule()
    {
        $this->cloudinaryManagement->disable();

        expect($this->cloudinaryManagement)->toHaveEnableButton();
    }
}
