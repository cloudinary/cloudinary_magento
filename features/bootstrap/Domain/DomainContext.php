<?php


namespace Domain;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use CloudinaryExtension\Credentials;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\Security\Key;
use CloudinaryExtension\Security\Secret;
use CloudinaryExtension\Image;
use CloudinaryExtension\Cloud;
use ImageProviders\FakeImageProvider;

require_once 'PHPUnit/Framework/Assert/Functions.php';

/**
 * Defines application features from the specific context.
 */
class DomainContext implements Context, SnippetAcceptingContext
{
    private $provider;
    private $image;
    private $areCredentialsValid;


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
    public function iHaveAnImage(Image $anImage)
    {
        $this->image = $anImage;
    }

    /**
     * @When I upload the image :anImage to the :aCloud cloud using the credentials with the API key :aKey and the secret :aSecret
     */
    public function iUploadTheImageToTheCloudUsingTheCredentialsWithTheApiKeyAndTheSecret(Image $anImage, Cloud $aCloud, Key $aKey, Secret $aSecret)
    {
        $credentials = new Credentials($aKey, $aSecret);
        $this->provider = new FakeImageProvider($credentials, $aCloud);

        $this->provider->upload($anImage);
    }

    /**
     * @When the image provider has a :aCloud cloud
     */
    public function theImageProviderHasACloud($aCloud)
    {
        $this->provider->setMockCloud($aCloud);

        $key = Key::fromString('ABC123');
        $secret = Secret::fromString('DEF456');
        $this->provider->setMockCredentials($key, $secret);
    }

    /**
     * @When the image provider is aware of the credentials with the API key :aKey and the secret :aSecret
     */
    public function theImageProviderAwareOfTheCredentialsWithTheApiKeyAndTheSecret($aKey, $aSecret)
    {
        $this->provider->setMockCredentials($aKey, $aSecret);
    }

    /**
     * @Then the image should be available through the image provider
     */
    public function theImageShouldBeAvailableThroughTheImageProvider()
    {
        expect($this->provider->getImageUrlByName($this->getImageName()))->notToBe('');
    }

    private function getImageName()
    {
        $imagePath = explode(DS, $this->image);
        return $imagePath[count($imagePath) - 1];
    }

    /**
     * @Given I have configured the :aCloud cloud using valid credentials
     */
    public function iHaveConfiguredTheCloudUsingValidCredentials(Cloud $aCloud)
    {
        $key = Key::fromString('ABC123');
        $secret = Secret::fromString('DEF456');
        $credentials = new Credentials($key, $secret);
        $this->provider = new FakeImageProvider($credentials, $aCloud);
    }

    /**
     * @Given I have configured the :aCloud cloud using using invalid credentials
     */
    public function iHaveConfiguredTheCloudUsingUsingInvalidCredentials(Cloud $aCloud)
    {
        $key = Key::fromString('UVW789');
        $secret = Secret::fromString('XYZ123');
        $credentials = new Credentials($key, $secret);
        $this->provider = new FakeImageProvider($credentials, $aCloud);
    }

    /**
     * @When I ask the provider to validate my credentials
     */
    public function iAskTheProviderToValidateMyCredentials()
    {
        $this->areCredentialsValid = $this->provider->validateCredentials();
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
}
