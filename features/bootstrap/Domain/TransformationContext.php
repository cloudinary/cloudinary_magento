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
use ImageProviders\FakeImageProvider;

require_once 'PHPUnit/Framework/Assert/Functions.php';

/**
 * Defines application features from the specific context.
 */
class TransformationContext implements Context
{

    private $imageProvider;
    private $image;

    private $imageUrl;

    public function __construct()
    {
        $credentials = new Credentials(Key::fromString("aKey"), Secret::fromString("aSecret"));
        $this->imageProvider = new FakeImageProvider($credentials, Cloud::fromName("aCloudName"));
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
        $this->imageUrl = $this->imageProvider->getImageUrlByName((string)$this->image);
    }

    /**
     * @Then I should get an optimised image from the image provider
     */
    public function iShouldGetAnOptimisedImageFromTheImageProvider()
    {
        expect($this->urlIsOptimised())->toBe(true);
    }

    /**
     * @Given image optimisation is disabled
     */
    public function imageOptimisationIsDisabled()
    {
        //$transformation = Transformation::build()->withOptimisationEnabled();
    }

    /**
     * @Then I should get the original image from the image provider
     */
    public function iShouldGetTheOriginalImageFromTheImageProvider()
    {
        throw new PendingException();
    }

    /**
     * @return bool
     */
    private function urlIsOptimised()
    {
        return strpos($this->imageUrl, 'f_auto') !== -1;
    }
}