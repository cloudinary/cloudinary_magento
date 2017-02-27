<?php

use CloudinaryExtension\Image;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\Image\Transformation\Dimensions;
use CloudinaryExtension\Image\ImageFactory;
use CloudinaryExtension\UrlGenerator;
use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\ConfigurationInterface;

class Cloudinary_Cloudinary_Model_Cms_Wysiwyg_Images_Storage extends Mage_Cms_Model_Wysiwyg_Images_Storage
{
    /**
     * @var ImageFactory
     */
    private $_imageFactory;

    /**
     * @var UrlGenerator
     */
    private $_urlGenerator;

    /**
     * @var ConfigurationInterface
     */
    private $_configuration;

    public function __construct()
    {
        $this->_configuration = Mage::getModel('cloudinary_cloudinary/configuration');

        $this->_imageFactory = new ImageFactory(
            $this->_configuration,
            Mage::getModel('cloudinary_cloudinary/synchronizationChecker')
        );

        $this->_urlGenerator = new UrlGenerator(
            $this->_configuration,
            CloudinaryImageProvider::fromConfiguration($this->_configuration)
        );
    }

    /**
     * @param string $filePath
     * @param bool $checkFile
     * @return string
     */
    public function getThumbnailUrl($filePath, $checkFile = false)
    {
        $image = $this->_imageFactory->build(
            $filePath,
            function() use($filePath, $checkFile) {
                return parent::getThumbnailUrl($filePath, $checkFile);
            }
        );

        return $this->_urlGenerator->generateWithDimensions(
            $image,
            Dimensions::fromWidthAndHeight(
                $this->getConfigData('resize_width'),
                $this->getConfigData('resize_height')
            )
        );
    }

    /**
     * @param string $targetPath
     * @param null|string $type
     * @return array
     */
    public function uploadFile($targetPath, $type = null)
    {
        if (!$this->_configuration->isEnabled()) {
           return parent::uploadFile($targetPath, $type);
        }

        $uploader = new Cloudinary_Cloudinary_Model_Cms_Uploader('image');
        if ($allowed = $this->getAllowedExtensions($type)) {
            $uploader->setAllowedExtensions($allowed);
        }
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(false);
        $result = $uploader->save($targetPath);

        if (!$result) {
            Mage::throwException(Mage::helper('cms')->__('Cannot upload file.'));
        }

        // create thumbnail
        $this->resizeFile($targetPath . DS . $uploader->getUploadedFileName(), true);

        $result['cookie'] = array(
            'name'     => session_name(),
            'value'    => $this->getSession()->getSessionId(),
            'lifetime' => $this->getSession()->getCookieLifetime(),
            'path'     => $this->getSession()->getCookiePath(),
            'domain'   => $this->getSession()->getCookieDomain()
        );

        return $result;
    }
}
