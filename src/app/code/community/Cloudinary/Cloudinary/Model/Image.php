<?php

use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Credentials;
use CloudinaryExtension\Security\Key;
use CloudinaryExtension\Security\Secret;
use CloudinaryExtension\Image;

class Cloudinary_Cloudinary_Model_Image extends Mage_Core_Model_Abstract
{


    public function upload($srcPath)
    {

        $cloudinary = new CloudinaryImageProvider();
        $cloudinary->upload(Image::fromPath($srcPath), $this->getCredentials());

        return $cloudinary->wasUploadSuccessful();
    }

    protected  function getCredentials()
    {
        $configuration = Mage::helper('cloudinary_cloudinary/configuration');

        return new Credentials(
            Key::fromString($configuration->getApiKey()),
            Secret::fromString($configuration->getApiSecret())
        );
    }

}