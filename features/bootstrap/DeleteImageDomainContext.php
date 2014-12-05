<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use CloudinaryExtension\Cloud;
use CloudinaryExtension\Credentials;
use CloudinaryExtension\Image;
use CloudinaryExtension\ImageManager;
use CloudinaryExtension\Security\Key;
use CloudinaryExtension\Security\Secret;


require_once 'PHPUnit/Framework/Assert/Functions.php';

/**
 * Defines application features from the specific context.
 */
class DeleteImageDomainContext implements Context
{
    const IMAGE_PROVIDER_KEY = 'some key';
    const IMAGE_PROVIDER_SECRET = 'some secret';
    const IMAGE_PROVIDER_CLOUD = 'some cloud';

    private $extension;

    /**
     * @Transform :anImage
     */
    public function transformStringToAnImage($string)
    {
        return Image::fromPath($string);
    }

    /**
     * @Given the image provider has an image :anImage
     */
    public function theImageProviderHasAnImage($anImage)
    {
        $cloud = Cloud::fromName(self::IMAGE_PROVIDER_CLOUD);
        $key = Key::fromString(self::IMAGE_PROVIDER_KEY);
        $secret = Secret::fromString(self::IMAGE_PROVIDER_SECRET);

        $credentials = new Credentials($key, $secret);
        $provider = new FakeImageProvider($credentials, $cloud);

        $provider->setMockCredentials($key, $secret);
        $provider->setMockCloud($cloud);

        $this->extension = new ImageManager($provider);
        $this->extension->uploadImage((string)$anImage);
    }

    /**
     * @When I delete the :anImage image
     */
    public function iDeleteTheImage($anImage)
    {
        $this->extension->deleteImage($anImage);
    }

    /**
     * @Then the image :anImage should no longer be available in the image provider
     */
    public function theImageShouldNoLongerBeAvailableInTheImageProvider($anImage)
    {
        expect($this->extension->getUrlForImage($anImage))->toBe('');
    }

}
