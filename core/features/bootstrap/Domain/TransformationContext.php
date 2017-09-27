<?php

namespace Domain;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use CloudinaryExtension\Cloud;
use CloudinaryExtension\Configuration;
use CloudinaryExtension\Credentials;
use CloudinaryExtension\Image;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\Image\Transformation\Dimensions;
use CloudinaryExtension\Image\Transformation\Dpr;
use CloudinaryExtension\Image\Transformation\Quality;
use CloudinaryExtension\Security\Key;
use CloudinaryExtension\Security\Secret;
use ImageProviders\ConfigurationProvider;
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

    private $configuration;

    public function __construct()
    {
        $this->configuration = Doubles::getConfiguration();

        $defaultTransformation = (new Transformation())
            ->withQuality(Quality::fromString('80'))
            ->withDpr(Dpr::fromString('1.0'));

        Doubles::getConfigurationProphecy()->getDefaultTransformation()->willReturn($defaultTransformation);

        $this->imageProvider = new TransformingImageProvider($this->configuration);
    }

    /**
     * @Transform :aDpr
     */
    public function transformStringToDpr($string)
    {
        return Dpr::fromString($string);
    }

    /**
     * @Transform :aQuality
     */
    public function transformStringToQuality($string)
    {
        return Quality::fromString($string);
    }

    /**
     * @Transform :aDimension
     */
    public function transformStringToDimensions($string)
    {
        $dimensions = explode('x', $string);

        return Dimensions::fromWidthAndHeight($dimensions[0], $dimensions[1]);
    }

    /**
     * @Given there's an image :anImage in the image provider
     */
    public function thereIsAnImageInTheImageProvider(Image $anImage)
    {
        $this->image = $anImage;
        $this->imageProvider->upload($this->image);
    }

    /**
     * @When I request the image from the image provider
     */
    public function iRequestTheImageFromTheImageProvider()
    {
        $this->imageUrl = $this->imageProvider->retrieve($this->image);
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
        $this->configuration->getDefaultTransformation()->withOptimisationDisabled();
    }

    /**
     * @Then I should get the original image from the image provider
     */
    public function iShouldGetTheOriginalImageFromTheImageProvider()
    {
        expect($this->isOriginalImage())->toBe(true);
    }

    /**
     * @Then I should get an image with :aQuality percent quality from the image provider
     */
    public function iShouldGetAnImageWithPercentQualityFromTheImageProvider(Quality $aQuality)
    {
        expect($this->isPercentageQuality((string)$aQuality))->toBe(true);
    }

    /**
     * @Given I set image quality to :aQuality percent
     */
    public function iTransformTheImageToHavePercentQuality(Quality $aQuality)
    {
        $transformation = $this->configuration->getDefaultTransformation();

        Doubles::getConfigurationProphecy()
            ->getDefaultTransformation()
            ->willReturn($transformation->withQuality($aQuality));
    }

    /**
     * @When I ask the image provider for :imageName transformed to :aDimension
     */
    public function iRequestTheImageProvideForTransformedTo($imageName, Dimensions $aDimension)
    {
        $this->imageUrl = $this->imageProvider->retrieveTransformed(
            Image::fromPath($imageName),
            Transformation::builder()->withDimensions($aDimension)
        );
    }

    /**
     * @Then I should receive that image with the dimensions :aDimension
     */
    public function iShouldReceiveThatImageWithTheDimensions(Dimensions $aDimension)
    {
        expect($this->hasDimensions($aDimension))->toBe(true);
    }

    /**
     * @Then I should get the image :image with the default DPR
     */
    public function iShouldGetAnImageWithTheDefaultDpr($image)
    {
        expect(basename($this->imageUrl))->toBe($image);
        expect($this->hasDefaultDpr())->toBe(true);
    }

    /**
     * @Given my DPR is set to :aDpr in the configuration
     */
    public function myDprIsSetToInTheConfiguration(Dpr $aDpr)
    {
        $transformation = $this->configuration->getDefaultTransformation();

        Doubles::getConfigurationProphecy()
            ->getDefaultTransformation()
            ->willReturn($transformation->withDpr($aDpr));

    }

    /**
     * @Then I should get an image with DPR :aDpr
     */
    public function iShouldGetAnImageWithDpr(Dpr $aDpr)
    {
        expect($this->hasDpr($aDpr))->toBe(true);
    }

    private function urlIsOptimised()
    {
        return strpos($this->imageUrl, 'fetch_format=auto') !== false;
    }

    private function isPercentageQuality($percentage)
    {
        return strpos($this->imageUrl, "quality=$percentage") !== false;
    }

    private function hasDimensions(Dimensions $dimension)
    {
        $hasWidth = strpos($this->imageUrl, "width={$dimension->getWidth()}") !== false;
        $hasHeight = strpos($this->imageUrl, "height={$dimension->getHeight()}") !== false;
        return $hasWidth && $hasHeight;
    }

    private function hasDefaultDpr()
    {
        return $this->hasDpr('1.0');
    }

    private function hasDpr($dpr)
    {
        return strpos($this->imageUrl, "dpr=$dpr") !== false;
    }

    /**
     * @return bool
     */
    protected function isOriginalImage()
    {
        return strpos($this->imageUrl, $this->image->__toString()) !== false &&
            strpos($this->imageUrl, '&quality=80&') !== false &&
            strpos($this->imageUrl, '&dpr=1.0/') !== false;

    }
}