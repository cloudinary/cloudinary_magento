<?php
 
class Cloudinary_Cloudinary_Model_Migration extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('cloudinary_cloudinary/migration');
    }

    public function hasStarted()
    {
        return (bool) $this->getStarted();
    }

    public function stop()
    {
        $this->setStarted(0);
        $this->save();
    }
}