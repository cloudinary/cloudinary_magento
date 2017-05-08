<?php

namespace Domain;

use Behat\Behat\Context\Context;
use CloudinaryExtension\Image;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\Security\CloudinaryEnvironmentVariable;
use ImageProviders\FakeImageProvider;

require_once 'PHPUnit/Framework/Assert/Functions.php';

/**
 * Defines application features from the specific context.
 */
class DeleteImageDomainContext implements Context
{
    const IMAGE_PROVIDER_ENVIRONMENT_VARIABLE = 'CLOUDINARY_URL=cloudinary://ABC123:DEF456@session-digital';

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
        $environmentVariable = CloudinaryEnvironmentVariable::fromString(self::IMAGE_PROVIDER_ENVIRONMENT_VARIABLE);
        $this->imageProvider = new FakeImageProvider($environmentVariable);

        $this->imageProvider->upload($anImage);
    }

    /**
     * @When I delete the :anImage image
     */
    public function iDeleteTheImage($anImage)
    {
        $this->imageProvider->delete($anImage);
    }

    /**
     * @Then the image :anImage should no longer be available in the image provider
     */
    public function theImageShouldNoLongerBeAvailableInTheImageProvider($anImage)
    {
        expect($this->imageProvider->retrieveTransformed($anImage, Transformation::builder()))->toBe('');
    }

}
