<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
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
     * @Given I have an image :anImage
     */
    public function iHaveAnImage($anImage)
    {
    }

    /**
     * @Given the image provider is aware of credentials with the API key :aKey and the secret :aSecret
     */
    public function theImageProviderIsAwareOfCredentialsWithTheApiKeyAndTheSecret($aKey, $aSecret)
    {
        $this->key = $aKey;
        $this->secret = $aSecret;

        $this->imageProvider = new DummyImageProvider();
        $this->imageProvider->setCredentials($this->key, $this->secret);
    }

    /**
     * @Given the extension configuration is set to use the same credentials
     */
    public function theExtensionConfigurationIsSetToUseTheSameCredentials()
    {
        $loginPage = $this->getPage('AdminLogin');
        $loginPage->open();
        $loginPage->login('testadmin', 'testadmin123');

        $cloudinarySystemConfigurationPage = $this->getPage('CloudinaryAdminSystemConfiguration');
        $cloudinarySystemConfigurationPage->open();
        $cloudinarySystemConfigurationPage->saveCredentials($this->key, $this->secret);
    }

    /**
     * @When I upload the image :arg1
     */
    public function iUploadTheImage($anImage)
    {
        $configuration = Mage::helper('cloudinary_cloudinary/configuration');

        $this->imageProvider->upload($configuration->getApiKey(), $configuration->getApiSecret());
    }

    /**
     * @Then the image should be available through the provider
     */
    public function theImageShouldBeAvailableThroughTheProvider()
    {
        expect($this->imageProvider->wasUploadSuccessful())->toBe(true);
    }
}
