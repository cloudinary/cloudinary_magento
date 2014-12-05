<?php

use CloudinaryExtension\Migration\Task;

class Cloudinary_Cloudinary_Model_Migration extends Mage_Core_Model_Abstract implements Task
{
    const CLOUDINARY_MIGRATION_ID = 1;

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

    public function start()
    {
        $this->setStarted(1);
        $this->save();
    }
}