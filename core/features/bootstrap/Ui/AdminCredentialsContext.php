<?php

namespace Ui;

use Behat\Behat\Context\Context;
use CloudinaryExtension\Cloud;
use CloudinaryExtension\Credentials;
use CloudinaryExtension\Security\CloudinaryEnvironmentVariable;
use CloudinaryExtension\Security\Key;
use CloudinaryExtension\Security\Secret;
use CloudinaryExtension\Image;
use ImageProviders\FakeImageProvider;
use MageTest\MagentoExtension\Context\RawMagentoContext;
use MageTest\Manager\FixtureManager;
use MageTest\Manager\Attributes\Provider\YamlProvider;
use Page\AdminLogin;
use Page\CloudinaryAdminSystemConfiguration;

class AdminCredentialsContext extends RawMagentoContext implements Context
{

    private $imageProvider;
    private $_fixtureManager;
    private $image;
    private $areCredentialsValid;
    private $adminConfigPage;
    private $adminLoginPage;

    public function __construct(CloudinaryAdminSystemConfiguration $adminSystemConfiguration, AdminLogin $adminLoginPage)
    {
        $this->adminConfigPage = $adminSystemConfiguration;
        $this->adminLoginPage = $adminLoginPage;
    }

    /**
     * @BeforeScenario
     */
    public function beforeScenario()
    {
      $this->_fixtureManager = new FixtureManager(new YamlProvider());
      $this->_fixtureManager->loadFixture('admin/user', __DIR__ . DS . '../Fixtures/Admin.yaml');
    }

    /**
     * @AfterScenario
     */
    public function afterScenario()
    {
        $this->_fixtureManager->clear();
    }

    /**
     * @Transform :anImage
     */
    public function transformStringToAnImage($string)
    {
        return Image::fromPath($string);
    }

    /**
     * @Given I have an image :anImage
     */
    public function iHaveAnImage($anImage)
    {
        $this->image = $anImage;
    }

    /**
     * @When I upload the image :anImage
     */
    public function iUploadTheImage(Image $anImage)
    {
        $environmentVariable = CloudinaryEnvironmentVariable::fromString('CLOUDINARY_URL=cloudinary://ABC123:DEF456@session-digital');
        $this->saveEnvironmentVariableToMagentoConfiguration($environmentVariable);

        $this->imageProvider = new FakeImageProvider($environmentVariable);

        $this->imageProvider->setMockCloud(Cloud::fromName('session-digital'));
        $this->imageProvider->setMockCredentials(Key::fromString('ABC123'), Secret::fromString('DEF456'));

        $this->imageProvider->upload($anImage);
    }

    /**
     * @Then the image should be available through the image provider
     */
    public function theImageShouldBeAvailableThroughTheImageProvider()
    {
        expect($this->imageProvider->getImageUrlByName((string)$this->image))->notToBe('');
    }

    /**
     * @Given I have used a valid environment variable in the configuration
     */
    public function iHaveUsedAValidEnvironmentVariableInTheConfiguration()
    {
        $environmentVariable = CloudinaryEnvironmentVariable::fromString('CLOUDINARY_URL=cloudinary://ABC123:DEF456@session-digital');
        $this->imageProvider = new FakeImageProvider($environmentVariable);
    }

    /**
     * @Given I have used an invalid environment variable in the configuration
     */
    public function iHaveUsedAnInvalidEnvironmentVariableInTheConfiguration()
    {
        $environmentVariable = CloudinaryEnvironmentVariable::fromString('CLOUDINARY_URL=cloudinary://UVW789:XYZ123@session-digital');
        $this->imageProvider = new FakeImageProvider($environmentVariable);
    }

    /**
     * @Given I have not configured my environment variable
     */
    public function iHaveNotConfiguredMyEnvironmentVariable()
    {
        $this->saveEnvironmentVariableToMagentoConfiguration('');
    }

    /**
     * @Given I have configured my environment variable
     */
    public function iHaveConfiguredMyEnvironmentVariable()
    {
        $this->saveEnvironmentVariableToMagentoConfiguration('anEnvironmentVariable');
    }

    /**
     * @When I ask the provider to validate my credentials
     */
    public function iAskTheProviderToValidateMyCredentials()
    {
        $this->imageProvider->setMockCloud(Cloud::fromName('session-digital'));
        $this->imageProvider->setMockCredentials(Key::fromString('ABC123'), Secret::fromString('DEF456'));

        $this->areCredentialsValid = $this->imageProvider->validateCredentials();
    }

    /**
     * @Then I should be informed my credentials are valid
     */
    public function iShouldBeInformedMyCredentialsAreValid()
    {
        expect($this->areCredentialsValid)->toBe(true);
    }

    /**
     * @Then I should be informed that my credentials are not valid
     */
    public function iShouldBeInformedThatMyCredentialsAreNotValid()
    {
        expect($this->areCredentialsValid)->toBe(false);
    }

    /**
     * @Given I have not configured my cloud and credentials
     */
    public function iHaveNotConfiguredMyCloudAndCredentials()
    {
        $this->saveCredentialsAndCloudToMagentoConfiguration('', '', '');
    }

    /**
     * @When I go to the Cloudinary configuration
     */
    public function iGoToTheCloudinaryConfiguration()
    {
        $this->adminConfigPage->open();
    }

    /**
     * @Then I should be prompted to sign up to Cloudinary
     */
    public function iShouldBePromptedToSignUpToCloudinary()
    {
        expect($this->adminConfigPage->containsSignUpPrompt())->toBe(true);
    }

    /**
     * @Then I should not be prompted to sign up to Cloudinary
     */
    public function iShouldNotBePromptedToSignUpToCloudinary()
    {
        expect($this->adminConfigPage->containsSignUpPrompt())->toBe(false);
    }

    private function saveEnvironmentVariableToMagentoConfiguration($environmentVariable)
    {
        $this->adminLoginPage->sessionLogin('testadmin', 'testadmin123', $this->getSessionService());

        $this->adminConfigPage->open();

        $this->adminConfigPage->enterEnvironmentVariable($environmentVariable);
        $this->adminConfigPage->saveCloudinaryConfiguration();

    }

}
