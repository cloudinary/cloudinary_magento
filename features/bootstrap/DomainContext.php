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
use Cloudinary\ImageManager;


require_once 'PHPUnit/Framework/Assert/Functions.php';

/**
 * Defines application features from the specific context.
 */
class DomainContext implements Context, SnippetAcceptingContext
{
    private $provider;
    private $configuration;
    private $uploadedImage;
    private $extension;
    private $key;
    private $secret;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $this->configuration = new FakeConfiguration();

        $this->extension = new ImageManager($this->provider, $this->configuration);
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
     * @Given I have an image :anImage
     */
    public function iHaveAnImage(Image $anImage)
    {
    }

    /**
     * @Given the image provider is aware of credentials with the API key :aKey and the secret :aSecret
     */
    public function theImageProviderIsAwareOfCredentialsWithTheApiKeyAndTheSecret(Key $aKey, Secret $aSecret)
    {
        $this->provider = new FakeImageProvider();
        $this->key = $aKey;
        $this->secret = $aSecret;
    }


    /**
     * @When I upload the image :anImage using the correct credentials
     */
    public function iUploadTheImageUsingTheCorrectCredentials(Image $anImage)
    {
        $credentials = new Credentials($this->key, $this->secret);
        $this->uploadedImage = $this->extension->uploadImage($anImage, $credentials);
    }

    /**
     * @Then the image should be available through the image provider
     */
    public function theImageShouldBeAvailableThroughTheImageProvider()
    {
        assertNotNull($this->uploadedImage->getUrl());
    }
}
