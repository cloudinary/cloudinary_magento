<?php

namespace EndToEnd;

use Cloudinary;
use Behat\Behat\Context\Context as BehatContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Cloudinary\Api;
use MageTest\Manager\FixtureManager;
use Facade\CloudinaryConsole;
use Facade\Magento as MagentoFacade;

class Context implements BehatContext, SnippetAcceptingContext
{
    use Fixture;
    use CloudinaryConfig;

    /**
     * @var FixtureManager
     */
    private $fixtureManager;

    /**
     * @var CloudinaryConsole
     */
    private $cloudinaryConsole;

    /**
     * @var MagentoFacade
     */
    private $magentoFacade;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var string
     */
    private $productImagePathFromPreviousStep;

    /**
     * Context constructor.
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
        $this->cloudinaryConsole = new CloudinaryConsole($parameters['cloudinary_env']);
        $this->magentoFacade = new MagentoFacade();
    }

    /**
     * @param FixtureManager $fixtureManager
     */
    protected function setFixtureManager(FixtureManager $fixtureManager)
    {
        $this->fixtureManager = $fixtureManager;
    }

    /**
     * @return FixtureManager
     */
    protected function getFixtureManager()
    {
        return $this->fixtureManager;
    }

    /**
     * @return array
     */
    protected function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return CloudinaryConsole
     */
    protected function getCloudinaryConsole()
    {
        return $this->cloudinaryConsole;
    }

    /**
     * @return MagentoFacade
     */
    protected function getMagentoFacade()
    {
        return $this->magentoFacade;
    }

    /**
     * @When I wait for a keypress
     */
    public function iWaitForAKeypress()
    {
        fread(STDIN, 1);
    }

    /**
     * @When image :arg1 is added to product :arg2
     */
    public function imageIsAddedToProduct($imageName, $productSku)
    {
        $this->getMagentoFacade()->addImageToProductWithSku(
            $productSku,
            $this->getFixtureFilePath($imageName)
        );
    }

    /**
     * @When I delete the images from product :arg1
     */
    public function iDeleteTheImagesFromProduct($productSku)
    {
        $this->getMagentoFacade()->deleteImagesFromProductWithSku($productSku);
    }

    /**
     * @Then the image for product :arg1 can be seen in the image provider root folder
     */
    public function theImageForProductCanBeSeenInTheImageProviderRootFolder($sku)
    {
        $imagePath = $this->getMagentoFacade()->productWithSku($sku)->getImage();

        $details = $this->getCloudinaryConsole()->detailsForImagePath($this->nameWithoutExtensionFromPath($imagePath));

        expect($details['public_id'])->shouldBeEqualTo($this->nameWithoutExtensionFromPath($imagePath));
    }

    /**
     * @Then the image can be seen on the image provider in the correct folder for product :arg1
     */
    public function theImageCanBeSeenOnTheImageProviderInTheCorrectFolderForProduct($sku)
    {
        $imagePath = $this->getMagentoFacade()->productWithSku($sku)->getImage();

        $folderedPath = sprintf('media/catalog/product%s', $this->nameAndPathWithoutExtension($imagePath));

        $details = $this->getCloudinaryConsole()->detailsForImagePath($folderedPath);

        expect($details['public_id'])->shouldBeEqualTo($folderedPath);
    }

    /**
     * @Given the product :arg1 has an image :arg2 on the image provider
     */
    public function theProductHasAnImageOnTheImageProvider($productSku, $imageName)
    {
        $this->imageIsAddedToProduct($imageName, $productSku);
        $this->productImagePathFromPreviousStep = $this->getMagentoFacade()->imagePathForProductWithSku($productSku);
        $this->theImageForProductCanBeSeenInTheImageProviderRootFolder($productSku);
    }

    /**
     * @Given the image provider has an image :arg1 in the correct folder for product :arg2
     */
    public function theImageProviderHasAnImageInTheCorrectFolderForProduct($imageName, $productSku)
    {
        $this->imageIsAddedToProduct($imageName, $productSku);
        $this->productImagePathFromPreviousStep = $this->getMagentoFacade()->imagePathForProductWithSku($productSku);
        $this->theImageCanBeSeenOnTheImageProviderInTheCorrectFolderForProduct($productSku);
    }

    /**
     * @Then there are no images for the :arg1 product in the image provider root folder
     */
    public function thereAreNoImagesForTheProductInTheImageProviderRootFolder($productSku)
    {
        try {
            $details = $this->getCloudinaryConsole()->detailsForImagePath(
                $this->nameWithoutExtensionFromPath($this->productImagePathFromPreviousStep)
            );

            throw new \Exception(
                sprintf(
                    'Expected nothing but found images for product image path: %s %s',
                    $this->productImagePathFromPreviousStep,
                    getenv('BEHAT_DEBUG') ? json_encode($details) : ''
                )
            );
        }

        catch (\Cloudinary\Api\NotFound $e) {}
    }

    /**
     * @Then the image can not be seen on the image provider in the correct folder for product :arg1
     */
    public function theImageCanNotBeSeenOnTheImageProviderInTheCorrectFolderForProduct($productSku)
    {
        $folderedPath = sprintf(
            'media/catalog/product%s',
            $this->nameAndPathWithoutExtension($this->productImagePathFromPreviousStep)
        );

        try {
            $details = $this->getCloudinaryConsole()->detailsForImagePath($folderedPath);

            throw new \Exception(
                sprintf(
                    'Expected nothing but found images for product image path: %s %s',
                    $folderedPath,
                    getenv('BEHAT_DEBUG') ? json_encode($details) : ''
                )
            );
        }

        catch (\Cloudinary\Api\NotFound $e) {}
    }

    /**
     * @Given the image provider has no images
     */
    public function theImageProviderHasNoImages()
    {
        $this->getCloudinaryConsole()->deleteAll();
    }

    /**
     * @param string $path
     * @return string
     */
    private function nameWithoutExtensionFromPath($path)
    {
        $info = pathinfo($path);
        return $info['filename'];
    }

    /**
     * @param string $path
     * @return string
     */
    private function nameAndPathWithoutExtension($path)
    {
        $info = pathinfo($path);
        return sprintf('%s%s%s', $info['dirname'], $info['dirname'] ? '/' : '', $info['filename']);
    }
}
