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
        return !($migration->hasStarted() &&
            ($migration->timeElapsed() > $cronIntervalInSeconds) &&
            !$migration->hasProgress());
    }
}
