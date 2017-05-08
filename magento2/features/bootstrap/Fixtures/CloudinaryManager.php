<?php

namespace Fixtures;

use Bex\Behat\Magento2InitExtension\Fixtures\BaseFixture;
use CloudinaryExtension\CloudinaryImageProvider;
use Cloudinary\Cloudinary\Model\Configuration;

class CloudinaryManager extends BaseFixture
{
    private $cloudinaryImageProvider;

    public function __construct()
    {
        parent::__construct();
        $this->cloudinaryImageProvider = CloudinaryImageProvider::fromConfiguration(
            $this->getMagentoObject(Configuration::class)
        );
    }

    public function deleteImageFromCloudinary($image)
    {
        $this->cloudinaryImageProvider->delete($image);
    }
}
