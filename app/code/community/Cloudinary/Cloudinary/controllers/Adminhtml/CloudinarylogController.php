<?php

class Cloudinary_Cloudinary_Adminhtml_CloudinarylogController extends Mage_Adminhtml_Controller_Action
{
    const LOG_MISSING_ERROR = 'No logs have been made to logfile: %s';
    const LOG_PERMISSION_ERROR = 'The log file cannot be read by your web server. Please check file permissions.';

    private function configurePage()
    {
        $this->_title($this->__('Download Cloudinary log file'))
            ->_setActiveMenu('cloudinary_cloudinary/log');
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->configurePage();

        $logSizeInBytes = 0;
        $error = '';
        $downloadUrl = Mage::helper('adminhtml')->getUrl('adminhtml/cloudinarylog/download');
        $filename = $this->logFilename();

        try {
            $stat = $this->fileStat($filename, $this->logPath($filename));
            $logSizeInBytes = $stat['size'];
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        $block = $this->getLayout()->getBlock('cloudinary_log');
        $block->assign('logSizeInBytes', $logSizeInBytes);
        $block->assign('error', $error);
        $block->assign('downloadUrl', $downloadUrl);
        $block->assign('name', $filename);
        $block->assign('isActive', Mage::getModel('cloudinary_cloudinary/logger')->isActive());

        $this->renderLayout();
    }

    public function downloadAction()
    {
        $filename = $this->logFilename();
        $filepath = $this->logPath($filename);

        try {
            $this->_prepareDownloadResponse($filename, array('type' => 'filename', 'value' => $filepath));
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
    }

    /**
     * @param $filename
     * @param $filepath
     * @return mixed
     * @throws Exception
     */
    private function fileStat($filename, $filepath)
    {
        $ioAdapter = new Varien_Io_File();

        try {
            $ioAdapter->open(array('path' => $ioAdapter->dirname($filepath)));
            $this->validateFileExists($ioAdapter, $filepath);
        } catch (Exception $e) {
            throw new Exception(sprintf(self::LOG_MISSING_ERROR, $filename));
        }

        try {
            $ioAdapter->streamOpen($filepath, 'r');
        } catch (Exception $e) {
            throw new Exception(self::LOG_PERMISSION_ERROR);
        }

        $stat = $ioAdapter->streamStat();
        $ioAdapter->streamClose();

        return $stat;
    }

    /**
     * @param Varien_Io_File $adapter
     * @param $filepath
     * @throws Exception
     */
    private function validateFileExists(Varien_Io_File $adapter, $filepath)
    {
        if (!$adapter->fileExists($filepath)) {
            throw new Exception(sprintf(self::LOG_MISSING_ERROR, $filepath));
        }
    }

    /**
     * @param string $filename
     * @return string
     */
    private function logPath($filename)
    {
        return sprintf('%s%s', $this->logDirectory(), $filename);
    }

    /**
     * @return string
     */
    private function logFilename()
    {
        $customLogFilename = Mage::getModel('cloudinary_cloudinary/logger')->filename();

        if ($customLogFilename) {
            return $customLogFilename;
        }

        return Mage::getStoreConfig('dev/log/file') ?: 'system.log';
    }

    /**
     * @return string
     */
    private function logDirectory()
    {
        return Mage::getBaseDir('var') . DS . 'log' . DS;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('cloudinary_cloudinary/log');
    }
}
