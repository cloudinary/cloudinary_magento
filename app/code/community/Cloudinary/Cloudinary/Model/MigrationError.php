<?php


class Cloudinary_Cloudinary_Model_MigrationError extends Mage_Core_Model_Abstract
{
    const REMOVE_ORPHAN_MESSAGE = 'Image found in sync table that no longer exists. Removing reference: %s';

    public function __construct()
    {
        $this->_init('cloudinary_cloudinary/migrationError');
    }

    /**
     * @param \CloudinaryExtension\Exception\MigrationError $e
     */
    public static function saveFromException(\CloudinaryExtension\Exception\MigrationError $e)
    {
        $image = $e->getImage();
        $filePath = (string)$image;

        $entry = Mage::getModel('cloudinary_cloudinary/migrationError');
        $entry->setFilePath($filePath);

        $entry->setRelativePath($image->getRelativePath());
        $entry->setMessage($e->getMessage());
        $entry->setCode($e->getCode());
        $entry->setTimestamp(time());

        $entry->save();
    }

    /**
     * @param Cloudinary_Cloudinary_Model_Synchronisation $orphanImage
     * @return $this
     */
    public function orphanRemoved(Cloudinary_Cloudinary_Model_Synchronisation $orphanImage)
    {
        $this->setFilePath($orphanImage->getImageName());
        $this->setRelativePath($orphanImage->getImageName());
        $this->setMessage(sprintf(self::REMOVE_ORPHAN_MESSAGE, $orphanImage->getImageName()));
        $this->setTimestamp(time());
        return $this;
    }
}
