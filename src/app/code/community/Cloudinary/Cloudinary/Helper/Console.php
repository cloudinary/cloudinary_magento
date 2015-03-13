<?php

use CloudinaryExtension\Security\ConsoleUrl;
use CloudinaryExtension\Security\SignedConsoleUrl;

class Cloudinary_Cloudinary_Helper_Console extends Mage_Core_Helper_Abstract
{

    public function getMediaLibraryUrl()
    {
        $consoleUrl = ConsoleUrl::fromPath("media_library/cms");
        return (string)SignedConsoleUrl::fromConsoleUrlAndCredentials(
            $consoleUrl,
            Mage::helper('cloudinary_cloudinary/configuration')->buildCredentials()
        );

    }

}