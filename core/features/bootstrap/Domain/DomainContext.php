<?php


namespace Domain;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use CloudinaryExtension\Credentials;
use CloudinaryExtension\Image\ImageFactory;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\Security\CloudinaryEnvironmentVariable;
use CloudinaryExtension\Security\Key;
use CloudinaryExtension\Security\Secret;
use CloudinaryExtension\Image;
use CloudinaryExtension\Cloud;
use CloudinaryExtension\UrlGenerator;
use ImageProviders\FakeImageProvider;
use Symfony\Component\Config\Definition\ConfigurationInterface;

require_once 'PHPUnit/Framework/Assert/Functions.php';

/**
 * Defines application features from the specific context.
 */
class DomainContext implements Context, SnippetAcceptingContext
{
    private $provider;
    private $image;
    private $areCredentialsValid;
    private $impageAlreadyUplaoded = false;

    public function __construct()
    {
        $environmentVariable = CloudinaryEnvironmentVariable::fromString('CLOUDINARY_URL=cloudinary://ABC123:DEF456@session-digital');
        $this->provider = new FakeImageProvider($environmentVariable);

        $cloud = Cloud::fromName('session-digital');
        $key = Key::fromString('ABC123');
        $secret = Secret::fromString('DEF456');
        $this->provider->setMockCloud($cloud);
        $this->provider->setMockCredentials($key, $secret);
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
    public function iHaveAnImage(Image $anImage)
    {
        $this->image = $anImage;
    }

    /**
     * @When I upload the image :anImage
     */
    public function iUploadTheImage(Image $anImage)
    {
        try {
            $this->provider->upload($anImage);
        } catch (\Exception $e) {
            $this->impageAlreadyUplaoded = true;
        }
    }

    /**
     * @Given the image :anImage does not exist on the provider
     */
    public function theImageDoesNotExistOnTheProvider(Image $anImage)
    {
        expect($this->provider->getImageUrlByName((string)$anImage))->toBe('');
    }

    /**
     * @Given the image :anImage has already been uploaded
     */
    public function theImageHasAlreadyBeenUploaded(Image $anImage)
    {
        try {
            $this->provider->upload($anImage);
        } catch (\Exception $e) {
            $this->impageAlreadyUplaoded = true;
        }
    }

    /**
     * @Then the image :anImage will be provided remotely
     */
    public function theImageWillBeProvidedRemotely(Image $anImage)
    {
        $this->setupStubs($anImage);

        $imageFactory = new ImageFactory(Doubles::getConfiguration(), Doubles::getSynchronizationChecker());

        $image = $imageFactory->build((string)$anImage, [$this, 'getLocalUrl']);

        $urlGenerator = new UrlGenerator(Doubles::getConfiguration(), $this->provider);

        expect($urlGenerator->generateFor($image))->toBe('uploaded image URL');
    }

    /**
     * @Then I should see an error image already exists
     */
    public function iShouldSeeAnErrorImageAlreadyExists()
    {
        expect($this->impageAlreadyUplaoded)->toBe(true);
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
        $imagePath = explode('/', $this->image);
        return $imagePath[count($imagePath) - 1];
    }

    /**
     * @Given I have used a valid environment variable in the configuration
     */
    public function iHaveUsedAValidEnvironmentVariableInTheConfiguration()
    {
        $environmentVariable = CloudinaryEnvironmentVariable::fromString('CLOUDINARY_URL=cloudinary://ABC123:DEF456@session-digital');
        $this->provider = new FakeImageProvider($environmentVariable);
    }

    /**
     * @Given I have used an invalid environment variable in the configuration
     */
    public function iHaveUsedAnInvalidEnvironmentVariableInTheConfiguration()
    {
        $environmentVariable = CloudinaryEnvironmentVariable::fromString('CLOUDINARY_URL=cloudinary://UVW789:XYZ123@session-digital');
        $this->provider = new FakeImageProvider($environmentVariable);
    }

    /**
     * @When I ask the provider to validate my credentials
     */
    public function iAskTheProviderToValidateMyCredentials()
    {
        $cloud = Cloud::fromName('session-digital');
        $key = Key::fromString('ABC123');
        $secret = Secret::fromString('DEF456');
        $this->provider->setMockCloud($cloud);
        $this->provider->setMockCredentials($key, $secret);

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

    /**
     * @Given I am logged in as an administrator
     */
    public function iAmLoggedInAsAnAdministrator()
    {
        // not required for domain suite
    }

    /**
     * @Then the image :anImage will be provided locally
     */
    public function theImageWillBeProvidedLocally(Image $anImage)
    {
        $this->setupStubs($anImage);

        $imageFactory = new ImageFactory(Doubles::getConfiguration(), Doubles::getSynchronizationChecker());

        $image = $imageFactory->build((string)$anImage, [$this, 'getLocalUrl']);

        $urlGenerator = new UrlGenerator(Doubles::getConfiguration(), $this->provider);

        expect($urlGenerator->generateFor($image))->toBe('local image path');
    }

    public function getLocalUrl()
    {
        return 'local image path';
    }

    /**
     * @param Image $anImage
     */
    private function setupStubs(Image $anImage)
    {
        Doubles::getConfigurationProphecy()->getMigratedPath((string)$anImage)->willReturn((string)$anImage);
        Doubles::getSynchronizationCheckerProphecy()->isSynchronized((string)$anImage)->willReturn(true);
    }
}
