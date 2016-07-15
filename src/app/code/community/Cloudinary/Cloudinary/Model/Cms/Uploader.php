<?php

use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Image;

class Cloudinary_Cloudinary_Model_Cms_Uploader extends Mage_Core_Model_File_Uploader
{
    use Cloudinary_Cloudinary_Model_PreConditionsValidator;

    private $requiredParams = ['path', 'file', 'type'];

    protected function _afterSave($result)
    {
        parent::_afterSave($result);

        if ($this->shouldUpload($result)) {
            $this->upload($result);
        }

        return $this;
    }

    private function upload($result)
    {
        $imageProvider = CloudinaryImageProvider::fromConfiguration($this->_getConfigHelper()->buildConfiguration());
        $imageProvider->upload(Image::fromPath($result['path'] . DIRECTORY_SEPARATOR . $result['file']));
        Mage::getModel('cloudinary_cloudinary/cms_synchronisation')->setValue($result['file'])->tagAsSynchronized();
    }

    /**
     * @param  array $result
     *
     * @return boolean
     */
    private function shouldUpload($result)
    {
        return $this->hasRequiredParams($result) && $this->isImage($result);
    }

    /**
     * @param  array  $result
     *
     * @return boolean
     */
    private function hasRequiredParams($result)
    {
        foreach ($this->requiredParams as $requiredParam) {
            if (empty($result[$requiredParam])) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array  $result
     *
     * @return boolean
     */
    private function isImage($result)
    {
        return strpos($result['type'], 'image') !== false;
    }
}
