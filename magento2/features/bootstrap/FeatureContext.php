<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use CloudinaryExtension\Exception\MigrationError;
use CloudinaryExtension\Image;
use Fixtures\CloudinaryConfig;
use Fixtures\CloudinaryManager;
use Fixtures\ProductManager;
use Page\Admin\Login;
use Page\Product as ProductPage;
use Cloudinary\Uploader;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    const IMAGE_RELATIVE_PATH = 'catalog/product/p/i/image';

    /**
     * @var Login
     */
    private $adminLogin;

    /**
     * @var ProductPage
     */
    private $productPage;

    /**
     * @var CloudinaryConfig
     */
    private $cloudinaryConfig;

    /**
     * @var CloudinaryManager
     */
    private $cloudinaryManager;

    /**
     * @var ProductManager
     */
    private $productManager;

    /**
     * @var MigrationError
     */
    private $uploadException;

    /**
     * @param Login $adminLogin
     */
    public function __construct(Login $adminLogin, ProductPage $productPage)
    {
        $this->adminLogin = $adminLogin;
        $this->productPage = $productPage;
        $this->cloudinaryConfig = new CloudinaryConfig();
        $this->cloudinaryManager = new CloudinaryManager();
        $this->productManager = new ProductManager();
    }

    /**
     * @Transform :anImage
     */
    public function transformStringToAnImage($string)
    {
        return Image::fromPath($string, self::IMAGE_RELATIVE_PATH);
    }

    /**
     * @BeforeScenario
     */
    public function beforeScenario()
    {
        $this->removeImageFromMediaFolder();
        $this->product = $this->productManager->createProduct();
    }

    /**
     * @AfterScenario
     */
    public function afterScenario()
    {
        $mediaGalleryData = $this->productManager
            ->createProduct()
            ->getMediaGallery();

        if (isset($mediaGalleryData['images']) && is_array($mediaGalleryData['images'])) {
            foreach ($mediaGalleryData['images'] as &$imageData) {
                $imageData['removed'] = 1;
            }
            $this->product->setData('media_gallery', $mediaGalleryData);
            $this->product->save();
        }

        $this->product = null;
        $this->uploadException = false;
    }

    /**
     * @Given I am logged in as an administrator
     */
    public function iAmLoggedInAsAnAdministrator()
    {
        try {
            $this->adminLogin->openPage();
            $this->adminLogin->login('admin', 'admin123');
        } catch (\Exception $e) {

        }
    }

    /**
     * @Given the cloudinary module is disabled
     */
    public function theCloudinaryModuleIsDisabled()
    {
        $this->cloudinaryConfig->disableCloudinary();
        exec('../../../bin/magento ca:cl config');
    }

    /**
     * @Given the image :anImage does not exist on the provider
     */
    public function theImageDoesNotExistOnTheProvider($anImage)
    {
        $this->cloudinaryManager->deleteImageFromCloudinary($anImage);
    }

    /**
     * @When I upload the image :anImage
     */
    public function iUploadTheImage(Image $anImage)
    {
        $imageFilename = (string)$anImage;

        try {
            $this->saveProductWithImage($imageFilename);
        } catch (MigrationError $e) {
            $this->uploadException = $e;
        }

        expect(file_exists('../../../pub/media/catalog/product/p/i/'.$imageFilename))->toBe(true);
    }

    /**
     * @Then the image :anImage will be provided locally
     */
    public function theImageWillBeProvidedLocally($anImage)
    {
        $this->productPage->openPage(['url_key' => $this->product->getUrlKey()]);

        expect($this->productPage)->toNotHaveCloudinaryImageUrl($anImage);
    }

    /**
     * @Given the cloudinary module is enabled
     */
    public function theCloudinaryModuleIsEnabled()
    {
        $this->cloudinaryConfig->enableCloudinary();
        exec('../../../bin/magento ca:cl config');
    }

    /**
     * @Then the image :anImage will be provided remotely
     */
    public function theImageWillBeProvidedRemotely($anImage)
    {
        $this->productPage->openPage(['url_key' => $this->product->getUrlKey()]);

        expect($this->uploadException instanceof MigrationError)->toBe(false);
        expect($this->productPage)->toHaveCloudinaryImageUrl($anImage);
    }

    /**
     * @Given the image :anImage has already been uploaded
     */
    public function theImageHasAlreadyBeenUploaded(Image $anImage)
    {
        Uploader::upload(
            __DIR__ . '/../fixtures/images/' . (string)$anImage,
            [
                "use_filename" => true,
                "unique_filename" => false,
                "overwrite" => false,
                "folder" => 'catalog/product/p/i/'
            ]
        );
    }

    /**
     * @Then I should see an error image already exists
     */
    public function iShouldSeeAnErrorImageAlreadyExists()
    {
        expect($this->uploadException instanceof MigrationError)->toBe(true);
    }

    private function removeImageFromMediaFolder()
    {
        exec("rm -rf ../../../pub/media/catalog/product/p/i");
    }

    /**
     * @param string $imageFilename
     */
    private function saveProductWithImage($imageFilename)
    {
        exec('cp features/fixtures/images/' . $imageFilename . ' /vagrant/pub/media/');
        $this->product->addImageToMediaGallery(
            '/vagrant/pub/media/' . $imageFilename,
            null,
            false,
            false
        )->save();
    }
}
