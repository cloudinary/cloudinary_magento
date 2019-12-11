<?php

class Cloudinary_Cloudinary_Model_Observer_Autoload extends Mage_Core_Model_Abstract
{
    /**
     * @param Varien_Event_Observer $event
     */
    public function autoloadRegister(Varien_Event_Observer $event)
    {
        require_once(Mage::getBaseDir('lib') . DS . 'CloudinaryExtension' . DS . 'vendor' . DS. 'autoload.php');
    }
}
