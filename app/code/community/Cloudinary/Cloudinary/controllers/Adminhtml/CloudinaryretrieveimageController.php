<?php

class Cloudinary_Cloudinary_Adminhtml_CloudinaryretrieveimageController extends Mage_Adminhtml_Controller_Action
{
    public function uploadAction()
    {
        try {
            $remoteFileUrl = $this->getRequest()->getParam('remote_image');
            $baseTmpMediaPath = $this->getBaseTmpMediaPath();
            $localUniqFilePath = $this->appendNewFileName($baseTmpMediaPath . $this->getLocalTmpFileName($remoteFileUrl));
            $this->validateRemoteFileExtensions($localUniqFilePath);
            $this->retrieveRemoteImage($remoteFileUrl, $localUniqFilePath);
            $localFileFullPath = $this->appendAbsoluteFileSystemPath($localUniqFilePath);
            Mage::getSingleton('core/file_validator_image')->setAllowedImageTypes(array('jpg','jpeg','gif','png'))->validate($localFileFullPath);
            $result = $this->appendResultSaveRemoteImage($localUniqFilePath, $baseTmpMediaPath);

            Mage::dispatchEvent(
                'catalog_product_gallery_upload_image_after', array(
                'result' => $result,
                'action' => $this
                )
            );
        } catch (Exception $e) {
            $result = array(
                'error' => $e->getMessage(),
                'errorcode' => $e->getCode());
        }

        $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    protected function getBaseTmpMediaPath()
    {
        $baseTmpMediaPath = false;
        switch ($this->getRequest()->getParam('type')) {
            case 'design_config_fileUploader':
                //$baseTmpMediaPath = 'tmp/' . FileProcessor::FILE_DIR;
                break;
            case 'category_image':
                $baseTmpMediaPath = 'catalog/tmp/category';
                break;
            case 'wysiwyg_image':
                $baseTmpMediaPath = $this->getStorage()->getSession()->getCurrentPath();
                if (substr($baseTmpMediaPath, 0, strlen(Mage::getBaseDir('media') . DS)) == Mage::getBaseDir('media') . DS) {
                    $baseTmpMediaPath = substr($baseTmpMediaPath, strlen(Mage::getBaseDir('media') . DS));
                }
                break;
            default:
                $baseTmpMediaPath = Mage::getSingleton('catalog/product_media_config')->getBaseTmpMediaPath();
                if (substr($baseTmpMediaPath, 0, strlen(Mage::getBaseDir('media') . DS)) == Mage::getBaseDir('media') . DS) {
                    $baseTmpMediaPath = substr($baseTmpMediaPath, strlen(Mage::getBaseDir('media') . DS));
                }
                break;
        }

        if (!$baseTmpMediaPath) {
            throw new Mage_Core_Exception(__("Empty baseTmpMediaPath"));
        }

        return $baseTmpMediaPath;
    }

    protected function getLocalTmpFileName($remoteFileUrl)
    {
        $localFileName = Varien_File_Uploader::getCorrectFileName(basename($remoteFileUrl));
        switch ($this->getRequest()->getParam('type')) {
            case 'design_config_fileUploader':
            case 'category_image':
            case 'wysiwyg_image':
                $localTmpFileName = DIRECTORY_SEPARATOR . $localFileName;
                break;
            default:
                $localTmpFileName = Varien_File_Uploader::getDispretionPath($localFileName) . DIRECTORY_SEPARATOR . $localFileName;
                break;
        }

        return $localTmpFileName;
    }

    /**
     * Invalidates files that have script extensions.
     *
     * @param string $filePath
     * @throws Mage_Core_Exception
     * @return void
     */
    private function validateRemoteFileExtensions($filePath)
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        if (!in_array($extension, array('jpg','jpeg','gif','png'))) {
            throw new Mage_Core_Exception(__('Disallowed file type.'));
        }
    }

    /**
     * @param string $localUniqFilePath
     * @return mixed
     */
    protected function appendResultSaveRemoteImage($localUniqFilePath, $baseTmpMediaPath)
    {
        $localFileFullPath = $this->appendAbsoluteFileSystemPath($localUniqFilePath);
        $tmpFileName = $localUniqFilePath;
        if (substr($tmpFileName, 0, strlen($baseTmpMediaPath)) == $baseTmpMediaPath) {
            $tmpFileName = substr($tmpFileName, strlen($baseTmpMediaPath));
        }

        $result['name'] = basename($localUniqFilePath);
        $result['type'] = Mage::helper('uploader/file')->getMimeTypeByExtension(@pathinfo($localFileFullPath, PATHINFO_EXTENSION));
        $result['error'] = 0;
        $result['size'] = filesize($localFileFullPath);
        $result['url'] = Mage::getBaseUrl('media') . ltrim($localUniqFilePath, DS);
        $result['tmp_name'] = str_replace(DS, "/", $localFileFullPath);
        $result['path'] = str_replace(DS, "/", $this->appendAbsoluteFileSystemPath($baseTmpMediaPath));
        $result['file'] = $tmpFileName;
        $result['cookie'] = array(
            'name'     => session_name(),
            'value'    => $this->_getSession()->getSessionId(),
            'lifetime' => $this->_getSession()->getCookieLifetime(),
            'path'     => $this->_getSession()->getCookiePath(),
            'domain'   => $this->_getSession()->getCookieDomain()
        );
        return $result;
    }

    /**
     * Trying to get remote image to save it locally
     *
     * @param string $fileUrl
     * @param string $localFilePath
     * @return void
     * @throws LocalizedException
     */
    protected function retrieveRemoteImage($fileUrl, $localFilePath)
    {
        $res = Cloudinary_Cloudinary_Helper_Data::curlGetContents($fileUrl);
        if (!$res || $res->getError() || empty(($image = $res->getBody()))) {
            throw new Mage_Core_Exception(
                __('The preview image information is unavailable. Check your connection and try again.')
            );
        }

        Mage::getSingleton('core/file_storage_file')->saveFile(array('filename' => $localFilePath, 'content' => $image), true);
    }

    /**
     * @param string $localFilePath
     * @return string
     */
    protected function appendNewFileName($localFilePath)
    {
        $destinationFile = $this->appendAbsoluteFileSystemPath($localFilePath);
        $fileName = Varien_File_Uploader::getNewFileName($destinationFile);
        $fileInfo = pathinfo($localFilePath);
        return $fileInfo['dirname'] . DS . $fileName;
    }

    /**
     * @param string $localTmpFile
     * @return string
     */
    protected function appendAbsoluteFileSystemPath($localTmpFile)
    {
        return Mage::getBaseDir('media') . DS . ltrim($localTmpFile, DS);
    }

    /**
     * Register storage model and return it
     *
     * @return Mage_Cms_Model_Wysiwyg_Images_Storage
     */
    public function getStorage()
    {
        if (!Mage::registry('storage')) {
            $storage = Mage::getModel('cms/wysiwyg_images_storage');
            Mage::register('storage', $storage);
        }

        return Mage::registry('storage');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return parent::_isAllowed();
    }
}
