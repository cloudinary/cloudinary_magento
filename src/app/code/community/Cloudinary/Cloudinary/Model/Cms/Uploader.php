<?php

use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Image;

class Cloudinary_Cloudinary_Model_Cms_Uploader extends Mage_Core_Model_File_Uploader
{
    protected function _afterSave($result)
    {
        parent::_afterSave($result);

        $configuration = Mage::getModel('cloudinary_cloudinary/configuration');

        if ($configuration->isEnabled() && !empty($result['path']) && !empty($result['file'])) {
            $imageProvider = CloudinaryImageProvider::fromConfiguration($configuration);

            $fullPath = rtrim($result['path'], '/') . DIRECTORY_SEPARATOR . $result['file'];
            $relativePath = $configuration->isFolderedMigration() ? $configuration->getMigratedPath($fullPath) : '';

            $image = Image::fromPath($fullPath, $relativePath);
            $imageProvider->upload($image);

            $this->_trackSynchronisation((string)$image);
        }

        return $this;
    }

    private function _trackSynchronisation($fileName)
    {
        Mage::getModel('cloudinary_cloudinary/cms_synchronisation')
            ->setFilename($fileName)
            ->tagAsSynchronized();
    }
}
