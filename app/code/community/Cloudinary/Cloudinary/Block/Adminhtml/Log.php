<?php

class Cloudinary_Cloudinary_Block_Adminhtml_Log extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'cloudinary_cloudinary';
        $this->_controller = 'adminhtml_log';
        $this->_headerText = Mage::helper('cloudinary_cloudinary')->__('Logs');
        
        parent::__construct();
    }

    /**
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    public function formatBytes($bytes, $precision = 2)
    {
        if ($bytes <= 0) {
            return '0 Bytes';
        }

        $base = log((int)$bytes, 1024);
        $suffixes = array('Bytes', 'KB', 'MB', 'GB', 'TB');

        return sprintf(
            '%s %s',
            round(pow(1024, $base - floor($base)), $precision),
            $suffixes[floor($base)]
        );
    }
}
