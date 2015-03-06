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
    private $extension;
    private $imageName;


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
}
