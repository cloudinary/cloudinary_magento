<?php


class Cloudinary_Cloudinary_Model_MigrationError extends Mage_Core_Model_Abstract
{
    public function __construct()
    {
        $this->_init('cloudinary_cloudinary/migrationError');
    }

    public static function saveFromException(\CloudinaryExtension\Exception\MigrationError $e)
    {
        $image = $e->getImage();
        $filePath = (string)$image;
        print_r(__FUNCTION__ . ": " . get_class($image) . "\n");

        $entry = Mage::getModel('cloudinary_cloudinary/migrationError');
        $entry->setFilePath($filePath);

        $entry->setRelativePath($image->getRelativePath());
        $entry->setMessage($e->getMessageText());
        $entry->setCode($e->getCode());
        $entry->setTimestamp(time());

        $entry->save();
    }
}
