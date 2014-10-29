<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Cloudinary\Credentials;
use Cloudinary\Credentials\Key;
use Cloudinary\Credentials\Secret;
use Cloudinary\Image;
use MageTest\Manager\FixtureManager;
use MageTest\Manager\Attributes\Provider\YamlProvider;
use SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;

/**
 * Defines application features from the specific context.
 */
class AdminCredentialsContext extends PageObjectContext implements Context, SnippetAcceptingContext
{
    private $imageProvider;
    private $key;
    private $secret;
    private $_fixtureManager;


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
     * @Given I have an image :anImage
     */
    public function iHaveAnImage($anImage)
    {
    }

    /**
     * @Given the image provider is aware of credentials with the API key :aKey and the secret :aSecret
     */
    public function theImageProviderIsAwareOfCredentialsWithTheApiKeyAndTheSecret(Key $aKey,Secret $aSecret)
    {
        $this->key = $aKey;
        $this->secret = $aSecret;

        $this->imageProvider = new DummyImageProvider($this->key, $this->secret);
    }

    /**
     * @When I upload the image :anImage using the correct credentials
     */
    public function iUploadTheImageUsingTheCorrectCredentials($anImage)
    {
        $this->saveCredentialsToMagentoConfiguration();

        $configuration = Mage::helper('cloudinary_cloudinary/configuration');

        $apiKey = Key::fromString($configuration->getApiKey());
        $apiSecret = Secret::fromString($configuration->getApiSecret());

        $this->imageProvider->upload(new Image($anImage), new Credentials($apiKey, $apiSecret));
    }

    /**
     * @Then the image should be available through the image provider
     */
    public function theImageShouldBeAvailableThroughTheImageProvider()
    {
        expect($this->imageProvider->wasUploadSuccessful())->toBe(true);
    }

    public function saveCredentialsToMagentoConfiguration()
    {
        $loginPage = $this->getPage('AdminLogin');
        $loginPage->open();
        $loginPage->login('testadmin', 'testadmin123');

        $cloudinarySystemConfigurationPage = $this->getPage('CloudinaryAdminSystemConfiguration');
        $cloudinarySystemConfigurationPage->open();
        $cloudinarySystemConfigurationPage->saveCredentials($this->key, $this->secret);
    }
}
