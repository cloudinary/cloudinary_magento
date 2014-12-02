<?php
 
class Cloudinary_Cloudinary_Model_Extension extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('cloudinary_cloudinary/extension');
    }

    public function isEnabled()
    {
        return $this->getEnabled() === 1;
    }

    public function migrationHasBeenTriggered()
    {
        return $this->getMigrationTriggered() === 1;
    }

}