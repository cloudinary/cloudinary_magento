<?php

namespace Domain;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use CloudinaryExtension\Cloud;
use CloudinaryExtension\Credentials;
use CloudinaryExtension\Image;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\Security\Key;
use CloudinaryExtension\Security\Secret;
use ImageProviders\TransformingImageProvider;

require_once 'PHPUnit/Framework/Assert/Functions.php';

/**
 * Defines application features from the specific context.
 */
class TransformationContext implements Context
{

    private $imageProvider;
    private $image;

    private $imageUrl;

    private $transformation;

    public function __construct()
    {
        $cloud = Cloud::fromName("aCloudName");
        $credentials = new Credentials(Key::fromString("aKey"), Secret::fromString("aSecret"));

        $this->imageProvider = new TransformingImageProvider($credentials, $cloud);


    }

    /**
     * @Given there's an image :anImage in the image provider
     */
    public function thereSAnImageInTheImageProvider(Image $anImage)
    {
        $this->image = $anImage;
        $this->imageProvider->upload($this->image);
    }

    /**
     * @When I request the image from the image provider
     */
    public function iRequestTheImageFromTheImageProvider()
    {
        $this->imageUrl = $this->imageProvider->transformImage($this->image, Transformation::builder());
    }

    /**
     * @Then I should get an optimised image from the image provider
     */
    public function iShouldGetAnOptimisedImageFromTheImageProvider()
    {
        expect($this->urlIsOptimised($this->imageUrl))->toBe(true);
    }

    /**
     * @Given image optimisation is disabled
     */
    public function imageOptimisationIsDisabled()
    {
        $this->transformation = Transformation::builder()->withOptimisationDisabled();
    }

    /**
     * @Then I should get the original image from the image provider
     */
    public function iShouldGetTheOriginalImageFromTheImageProvider()
    {
        $imageUrl = $this->imageProvider->transformImage($this->image, $this->transformation);
        expect($this->urlIsOptimised($imageUrl))->toBe(false);
    }

    /**
     * @Then I should get an image with :arg1 percent quality from the image provider
     */
    public function iShouldGetAnImageWithPercentQualityFromTheImageProvider($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given I transform the image to have :arg1 percent quality
     */
    public function iTransformTheImageToHavePercentQuality($arg1)
    {
        throw new PendingException();
    }

    private function urlIsOptimised($imageUrl)
    {
        return strpos($imageUrl, 'fetch_format=auto') !== false;
    }
}