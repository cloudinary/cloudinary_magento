<?php


class Cloudinary_Cloudinary_Block_Adminhtml_Warning extends Mage_Adminhtml_Block_Abstract
{
    public function __construct()
    {
        $this->_blockGroup = 'cloudinary_cloudinary';

        $this->_controller = 'adminhtml_warning';

        $this->setTemplate('cloudinary/warning.phtml');

    }

    public function build()
    {

        return $this->getLayout()
            ->createBlock('adminhtml/template')
            ->setText('blah')
            ->setTemplate('cloudinary/template/warning.phtml');

        var_dump($a->toHtml());die;
    }
}