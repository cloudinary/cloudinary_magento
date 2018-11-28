<?php

class Cloudinary_Cloudinary_Model_Observer_System extends Mage_Core_Model_Abstract
{
    /**
     * @param Varien_Event_Observer $event
     * @return Varien_Event_Observer
     */
    public function loadCustomAutoloaders(Varien_Event_Observer $event)
    {
        Mage::helper('cloudinary_cloudinary/autoloader')->register();

        return $event;
    }
}
