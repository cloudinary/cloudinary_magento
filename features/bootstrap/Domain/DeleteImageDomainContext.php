<?php

namespace Domain;

use Behat\Behat\Context\Context;
use CloudinaryExtension\Cloud;
use CloudinaryExtension\Configuration;
use CloudinaryExtension\Credentials;
use CloudinaryExtension\Image;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\ImageProviderFactory;
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

    private $imageProvider;

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
        $configuration = Configuration::fromCloudAndCredentials($credentials, $cloud);

        $this->imageProvider = ImageProviderFactory::fromProviderNameAndConfiguration(
            'imageProviders\Fake',
            $configuration
        );

        $this->imageProvider->setMockCredentials($key, $secret);
        $this->imageProvider->setMockCloud($cloud);

        $this->imageProvider->upload($anImage);
    }

    /**
     * @When I delete the :anImage image
     */
    public function iDeleteTheImage($anImage)
    {
        $this->imageProvider->deleteImage($anImage);
    }

    /**
     * @Then the image :anImage should no longer be available in the image provider
     */
    public function theImageShouldNoLongerBeAvailableInTheImageProvider($anImage)
    {
        expect($this->imageProvider->transformImage($anImage, Transformation::builder()))->toBe('');
    }

}
