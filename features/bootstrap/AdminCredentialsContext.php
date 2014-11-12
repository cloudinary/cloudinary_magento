<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use CloudinaryExtension\Cloud;
use CloudinaryExtension\Credentials;
use CloudinaryExtension\Security\Key;
use CloudinaryExtension\Security\Secret;
use CloudinaryExtension\Image;
use MageTest\Manager\FixtureManager;
use MageTest\Manager\Attributes\Provider\YamlProvider;
use SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;


class AdminCredentialsContext extends PageObjectContext implements Context, SnippetAcceptingContext
{
    private $imageProvider;
    private $_fixtureManager;
    private $image;


    /**
     * @BeforeScenario
     */
    public function beforeScenario()
    {
      $this->_fixtureManager = new FixtureManager(new YamlProvider());
      $this->_fixtureManager->loadFixture('admin/user', __DIR__ . DS . 'Fixtures/Admin.yaml');
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

        $configuration = Mage::helper('cloudinary_cloudinary/configuration');
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
        $this->imageProvider->setMockCloud($aCloud);
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

    private function saveCredentialsAndCloudToMagentoConfiguration($key, $secret, $cloud)
    {
        $loginPage = $this->getPage('AdminLogin');
        $loginPage->open();
        $loginPage->login('testadmin', 'testadmin123');

        $cloudinarySystemConfigurationPage = $this->getPage('CloudinaryAdminSystemConfiguration');
        $cloudinarySystemConfigurationPage->open();
        $cloudinarySystemConfigurationPage->enterCredentials($key, $secret);
        $cloudinarySystemConfigurationPage->enterCloudName($cloud);
        $cloudinarySystemConfigurationPage->saveCloudinaryConfiguration();
    }
}
