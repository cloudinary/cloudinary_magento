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
     * Return files
     *
     * @param string $path Parent directory path
     * @param string $type Type of storage, e.g. image, media etc.
     * @return Varien_Data_Collection_Filesystem
     */
    public function getFilesCollection($path, $type = null)
    {
        if (!$this->_configuration->isEnabled()) {
            return parent::getFilesCollection($path, $type);
        }

        if (Mage::helper('core/file_storage_database')->checkDbUsage()) {
            $files = Mage::getModel('core/file_storage_database')->getDirectoryFiles($path);

            $fileStorageModel = Mage::getModel('core/file_storage_file');
            foreach ($files as $file) {
                $fileStorageModel->saveFile($file);
            }
        }

        $collection = $this->getCollection($path)
            ->setCollectDirs(false)
            ->setCollectFiles(true)
            ->setCollectRecursively(false)
            ->setOrder('mtime', Varien_Data_Collection::SORT_ORDER_ASC);

        // Add files extension filter
        if ($allowed = $this->getAllowedExtensions($type)) {
            $collection->setFilesFilter('/\.(' . implode('|', $allowed). ')$/i');
        }

        $helper = $this->getHelper();

        // prepare items
        foreach ($collection as $item) {
            $item->setId($helper->idEncode($item->getBasename()));
            $item->setName($item->getBasename());
            $item->setShortName($helper->getShortFilename($item->getBasename()));
            $item->setUrl($helper->getCurrentUrl() . $item->getBasename());

            if ($this->isImage($item->getBasename())) {
                $thumbUrl = $this->getThumbnailUrl(
                    Mage_Core_Model_File_Uploader::getCorrectFileName($item->getFilename()),
                    true,
                    $item->getFilename()
                );
                // generate thumbnail "on the fly" if it does not exists
                if (! $thumbUrl) {
                    $thumbUrl = Mage::getSingleton('adminhtml/url')->getUrl('*/*/thumbnail', array('file' => $item->getId()));
                }

                $size = @getimagesize($item->getFilename());

                if (is_array($size)) {
                    $item->setWidth($size[0]);
                    $item->setHeight($size[1]);
                }
            } else {
                $thumbUrl = Mage::getDesign()->getSkinBaseUrl() . self::THUMB_PLACEHOLDER_PATH_SUFFIX;
            }

            $item->setThumbUrl($thumbUrl);
        }

        return $collection;
    }

    /**
     * @param string $filePath
     * @param bool $checkFile
     * @return string
     */
    public function getThumbnailUrl($filePath, $checkFile = false, $origFilePath = null)
    {
        $_origUrl = $origUrl = parent::getThumbnailUrl($filePath, $checkFile);

        if (!$this->_configuration->isEnabled()) {
            return $_origUrl;
        }

        if (!$_origUrl && !is_null($origFilePath)) {
            $filePath = $origFilePath;
            $origUrl = parent::getThumbnailUrl($filePath, $checkFile);
        }

        if (!$origUrl) {
            return $_origUrl;
        }

        $image = $this->_imageFactory->build(
            $filePath,
            function () use ($_origUrl) {
                return (string) $_origUrl;
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
