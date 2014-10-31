<?php

use Cloudinary\CloudinaryImageProvider;
use Cloudinary\Credentials;
use Cloudinary\Credentials\Key;
use Cloudinary\Credentials\Secret;
use Cloudinary\Image;

class Cloudinary_Cloudinary_Model_Image extends Mage_Core_Model_Abstract
{


    public function upload($srcPath)
    {
        $cloudinary = new CloudinaryImageProvider();
        $cloudinary->upload(Image::fromPath($srcPath), $this->getCredentials());

        return $cloudinary->wasUploadSuccessful();
    }

    private function getCredentials()
    {
        $configuration = Mage::helper('cloudinary_cloudinary/configuration');

        return new Credentials(
            Key::fromString($configuration->getApiKey()),
            Secret::fromString($configuration->getApiSecret())
        );
    }

}