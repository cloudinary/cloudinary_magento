<?php

namespace Ui;

use Behat\Behat\Context\Context;
use CloudinaryExtension\Cloud;
use CloudinaryExtension\Credentials;
use CloudinaryExtension\ImageProvider;
use CloudinaryExtension\ImageProviderFactory;
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

    /** @var  ImageProvider */
    private $imageProvider;
    private $_fixtureManager;
    private $image;
    private $areCredentialsValid;

    /**
     * @var CloudinaryAdminSystemConfiguration
     */
    private $adminConfigPage;

    /**
     * @var AdminLogin
     */
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
     * @Transform :aKey
     */
    public function transformStringToAKey($string)
    {
        return Key::fromString($string);
    }

    /**
     * @Transform :aSecret
     */
    public function transformStringToASecret($string)
    {
        return Secret::fromString($string);
    }

    /**
     * @Transform :aCloud
     */
    public function transformStringToACloud($string)
    {
        return Cloud::fromName($string);
    }

    /**
     * @Given I have an image :anImage
     */
    public function iHaveAnImage($anImage)
    {
        $this->image = $anImage;
    }

    /**
     * @When I upload the image :anImage to the :aCloud cloud using the credentials with the API key :aKey and the secret :aSecret
     */
    public function iUploadTheImageToTheCloudUsingTheCredentialsWithTheApiKeyAndTheSecret(Image $anImage, Cloud $aCloud, Key $aKey, Secret $aSecret)
    {
        $this->saveCredentialsAndCloudToMagentoConfiguration($aKey, $aSecret, $aCloud);

        $configuration = \Mage::helper('cloudinary_cloudinary/configuration');
        $apiKey = Key::fromString($configuration->getApiKey());
        $apiSecret = Secret::fromString($configuration->getApiSecret());
        $cloudName = Cloud::fromName($configuration->getCloudName());

        $this->imageProvider = new FakeImageProvider(new Credentials($apiKey, $apiSecret), $cloudName);
        $this->imageProvider->upload($anImage);
    }

    /**
     * @When the image provider has a :aCloud cloud
     */
    public function theImageProviderHasACloud($aCloud)
    {
        $key = Key::fromString('ABC123');
        $secret = Secret::fromString('DEF456');

        $this->imageProvider->setMockCloud($aCloud);
        $this->imageProvider->setMockCredentials($key, $secret);

    }

    /**
     * @When the image provider is aware of the credentials with the API key :aKey and the secret :aSecret
     */
    public function theImageProviderIsAwareOfTheCredentialsWithTheApiKeyAndTheSecret(Key $aKey, Secret $aSecret)
    {
        $this->imageProvider->setMockCredentials($aKey, $aSecret);
    }

    /**
     * @Then the image should be available through the image provider
     */
    public function theImageShouldBeAvailableThroughTheImageProvider()
    {
        expect($this->imageProvider->getImageUrlByName((string)$this->image))->notToBe('');
    }

    /**
     * @Given I have configured the :aCloud cloud using valid credentials
     */
    public function iHaveConfiguredTheCloudUsingValidCredentials(Cloud $aCloud)
    {
        $key = Key::fromString('ABC123');
        $secret = Secret::fromString('DEF456');

        $this->imageProvider = ImageProviderFactory::fromProviderName(
            'fake',
            new Credentials($key, $secret),
            $aCloud
        );
    }

    /**
     * @When I ask the provider to validate my credentials
     */
    public function iAskTheProviderToValidateMyCredentials()
    {
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
     * @Given I have configured the :aCloud cloud using using invalid credentials
     */
    public function iHaveConfiguredTheCloudUsingUsingInvalidCredentials(Cloud $aCloud)
    {
        $key = Key::fromString('UVW789');
        $secret = Secret::fromString('XYZ123');

        $this->imageProvider = ImageProviderFactory::fromProviderName(
            'fake',
            new Credentials($key, $secret),
            $aCloud
        );
    }

    /**
     * @Then I should be informed that my credentials are not valid
     */
    public function iShouldBeInformedThatMyCredentialsAreNotValid()
    {
        expect($this->areCredentialsValid)->toBe(false);
    }

    private function saveCredentialsAndCloudToMagentoConfiguration($key, $secret, $cloud)
    {
        $this->adminLoginPage->sessionLogin('testadmin', 'testadmin123', $this->getSessionService());

        $this->adminConfigPage->open();

        $this->adminConfigPage->enterCredentials($key, $secret);
        $this->adminConfigPage->enterCloudName($cloud);
        $this->adminConfigPage->saveCloudinaryConfiguration();
    }

}
