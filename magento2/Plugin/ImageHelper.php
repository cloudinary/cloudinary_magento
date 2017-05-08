<?php

namespace Cloudinary\Cloudinary\Plugin;

use CloudinaryExtension\Image\ImageFactory;
use CloudinaryExtension\Image\Transformation\Dimensions;
use CloudinaryExtension\Image\Transformation\Crop;
use CloudinaryExtension\UrlGenerator;
use CloudinaryExtension\ConfigurationInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\Image as CatalogImageHelper;

class ImageHelper
{
    /**
     * @var ImageFactory
     */
    private $imageFactory;

    /**
     * @var UrlGenerator
     */
    private $urlGenerator;

    /**
     * @var ProductInterface
     */
    private $product;

    /**
     * @var Dimensions
     */
    private $dimensions;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var string
     */
    private $imageFile;

    /**
     * @var bool
     */
    private $keepFrame;

    /**
     * @param ImageFactory $imageFactory
     * @param UrlGenerator $urlGenerator
     * @param ConfigurationInterface $configuration
     */
    public function __construct(
        ImageFactory $imageFactory,
        UrlGenerator $urlGenerator,
        ConfigurationInterface $configuration
    ) {
        $this->imageFactory = $imageFactory;
        $this->urlGenerator = $urlGenerator;
        $this->configuration = $configuration;
        $this->dimensions = null;
        $this->imageFile = null;
    }

    /**
     * @param  CatalogImageHelper $helper
     * @param  ProductInterface   $product
     * @param  string             $imageId
     * @param  array              $attributes
     *
     * @return array
     */
    public function beforeInit(CatalogImageHelper $helper, $product, $imageId, $attributes = [])
    {
        $this->product = $product;
        $this->dimensions = null;
        $this->imageFile = null;
        $this->keepFrame = true;
        return [$product, $imageId, $attributes];
    }

    /**
     * @param  CatalogImageHelper $helper
     * @param  string             $file
     *
     * @return string[]
     */
    public function beforeSetImageFile(CatalogImageHelper $helper, $file)
    {
        $this->imageFile = $file;
        return [$file];
    }

    /**
     * @param  CatalogImageHelper $helper
     * @param  int                $width
     * @param  int                $height
     *
     * @return array
     */
    public function beforeResize(CatalogImageHelper $helper, $width, $height = null)
    {
        $this->dimensions = Dimensions::fromWidthAndHeight($width, $height);

        return [$width, $height];
    }

    /**
     * @param CatalogImageHelper $helper
     * @param bool $flag
     */
    public function beforeKeepFrame(CatalogImageHelper $helper, $flag)
    {
        $this->keepFrame = (bool)$flag;
    }

    /**
     * @param  CatalogImageHelper $helper
     * @param  \Closure           $originalMethod
     *
     * @return string
     */
    public function aroundGetUrl(CatalogImageHelper $helper, \Closure $originalMethod)
    {
        $image = $this->imageFactory->build(
            sprintf('catalog/product%s', $this->imageFile ?: $this->product->getData($helper->getType())),
            $originalMethod
        );

        $dimensions = $this->dimensions ?: Dimensions::fromWidthAndHeight($helper->getWidth(), $helper->getHeight());

        $transform = $this->configuration->getDefaultTransformation()->withDimensions($dimensions);

        if ($this->keepFrame) {
            $transform->withCrop(Crop::fromString('pad'))
                ->withDimensions(Dimensions::squareMissingDimension($dimensions));
        } else {
            $transform->withCrop(Crop::fromString('fit'));
        }

        return $this->urlGenerator->generateFor($image, $transform);
    }
}
