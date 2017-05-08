<?php

namespace CloudinaryExtension;

use CloudinaryExtension\ImageInterface;
use CloudinaryExtension\Image\LocalImage;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\Image\Transformation\Dimensions;

class UrlGenerator
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var ImageProvider
     */
    private $imageProvider;

    /**
     * @param ConfigurationInterface $configuration
     * @param ImageProvider $imageProvider
     */
    public function __construct(ConfigurationInterface $configuration, ImageProvider $imageProvider)
    {
        $this->configuration = $configuration;
        $this->imageProvider = $imageProvider;
    }

    /**
     * @param ImageInterface $image
     * @param Transformation $transformation
     *
     * @return string
     */
    public function generateFor(ImageInterface $image, Transformation $transformation = null)
    {
        if ($image instanceof LocalImage) {
            return (string)$image;
        }

        $transformation = clone ($transformation ?:  $this->configuration->getDefaultTransformation());

        if (in_array($image->getExtension(), $this->configuration->getFormatsToPreserve())) {
            $transformation->withoutFormat();
        }

        return (string)$this->imageProvider->retrieveTransformed($image, $transformation);
    }

    /**
     * @param Image $image
     * @param Dimensions $dimensions
     *
     * @return string
     */
    public function generateWithDimensions(ImageInterface $image, Dimensions $dimensions)
    {
        $transformation = clone $this->configuration->getDefaultTransformation();

        return $this->generateFor($image, $transformation->withDimensions($dimensions));
    }
}
