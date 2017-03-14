<?php

class Cloudinary_Cloudinary_Helper_Cron extends Mage_Core_Helper_Abstract
{
    /**
     * @param Cloudinary_Cloudinary_Model_Migration $migration
     * @param int $cronIntervalInSeconds
     * @return bool
     */
    public function validate(Cloudinary_Cloudinary_Model_Migration $migration, $cronIntervalInSeconds)
    {
        return !($this->isInitialising($migration) && ($migration->timeElapsed() > $cronIntervalInSeconds));
    }

    /**
     * @param Cloudinary_Cloudinary_Model_Migration $migration
     * @return bool
     */
    public function isInitialising(Cloudinary_Cloudinary_Model_Migration $migration)
    {
        return $migration->hasStarted() && !$migration->hasProgress();
    }
}
