<?php


namespace Domain;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use CloudinaryExtension\Credentials;
use CloudinaryExtension\Image\Dimensions;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\Security\Key;
use CloudinaryExtension\Security\Secret;
use CloudinaryExtension\Image;
use CloudinaryExtension\Cloud;
use CloudinaryExtension\ImageManager;
use ImageProviders\FakeImageProvider;
use ImageProviders\TransformingImageProvider;

require_once 'PHPUnit/Framework/Assert/Functions.php';

/**
 * Defines application features from the specific context.
 */
class DomainContext implements Context, SnippetAcceptingContext
{
    private $provider;
    private $image;
    private $extension;
    private $receivedUrl;
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
     * @Transform :dimensions
     */
    public function transformStringToDimensions($string)
    {
        $dimensions = explode('x', $string);

        return Dimensions::fromWidthAndHeight($dimensions[0], $dimensions[1]);
    }

    /**
     * @Given I have an image :anImage
     */
    public function iHaveAnImage(Image $anImage)
    {
        $this->image = $anImage;
    }

    /**
     * @When I upload the image :anImageName to the :aCloud cloud using the credentials with the API key :aKey and the secret :aSecret
     */
    public function iUploadTheImageToTheCloudUsingTheCredentialsWithTheApiKeyAndTheSecret($anImageName, Cloud $aCloud, Key $aKey, Secret $aSecret)
    {
        $credentials = new Credentials($aKey, $aSecret);
        $this->provider = new FakeImageProvider($credentials, $aCloud);

        $this->extension = new ImageManager($this->provider, new Transformation());
        $this->extension->uploadImage($anImageName);
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

    /**
     * @Given my image provider has an image :anImageName
     */
    public function myImageProviderHasAnImage($anImageName)
    {
        $this->imageName = $anImageName;
        $this->provider = new TransformingImageProvider();

        $this->extension = new ImageManager($this->provider, new Transformation());
        $this->extension->uploadImage($anImageName);
    }

    /**
     * @When I ask the image provider for :imageName transformed to :dimensions
     */
    public function iRequestTheImageProvideForTransformedTo($imageName, Dimensions $dimensions)
    {
        $this->receivedUrl = $this->extension->getUrlForImageWithTransformation(
            Image::fromPath($imageName),
            Transformation::builder()->withDimensions($dimensions)
        );
    }

    /**
     * @Then I should receive that image with the dimensions :dimensions
     */
    public function iShouldReceiveThatImageWithTheDimensions(Dimensions $dimensions)
    {
        expect($this->receivedUrl)->toBe(
            sprintf('https://res.cloudinary.com/demo/image/upload/h_%s,w_%s/%s',
                $dimensions->getHeight(),
                $dimensions->getWidth(),
                $this->imageName
            )
        );
    }
}
