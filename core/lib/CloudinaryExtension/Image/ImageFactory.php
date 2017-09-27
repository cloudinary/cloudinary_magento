<?php

namespace CloudinaryExtension\Image;

use CloudinaryExtension\ConfigurationInterface;
use CloudinaryExtension\Image\SynchronizationChecker;
use CloudinaryExtension\Image;

class ImageFactory
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var SynchronizationChecker
     */
    private $synchronizationChecker;

    /**
     * ImageFactory constructor.
     * @param ConfigurationInterface $configuration
     * @param SynchronizationChecker $synchronizationChecker
     */
    public function __construct(ConfigurationInterface $configuration, SynchronizationChecker $synchronizationChecker)
    {
        $this->configuration = $configuration;
        $this->synchronizationChecker = $synchronizationChecker;
    }

    /**
     * @param $imagePath
     * @return Image
     */
    public function build($imagePath, callable $localPathGenerator)
    {
        $migratedPath = $this->configuration->getMigratedPath($imagePath);

        if ($this->configuration->isEnabled() && $this->synchronizationChecker->isSynchronized($migratedPath)) {
            return Image::fromPath($imagePath, $migratedPath);
        } else {
            return new LocalImage($localPathGenerator);
        }
    }
}
