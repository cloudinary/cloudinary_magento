<?php

use CloudinaryExtension\Migration\Task;

class Cloudinary_Cloudinary_Model_Migration extends Mage_Core_Model_Abstract implements Task
{
    const CLOUDINARY_MIGRATION_ID = 1;

    protected function _construct()
    {
        $this->_init('cloudinary_cloudinary/migration');
    }

    /**
     * @return bool
     */
    public function hasStarted()
    {
        return (bool) $this->getStarted();
    }

    /**
     * @return bool
     */
    public function hasBeenStopped()
    {
        $this->load($this->getId());

        return (bool) $this->getStarted() == 0;
    }

    public function stop()
    {
        $this->setStarted(0);
        $this->save();
    }

    public function start()
    {
        $this->setStarted(1);
        $this->setStartedAt($this->_dateNow());
        $this->setBatchCount(0);
        $this->save();
    }

    public function recordBatchProgress()
    {
        if ($this->hasStarted()) {
            $this->setBatchCount($this->getBatchCount() + 1)->save();
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function hasProgress()
    {
        return $this->getBatchCount() > 0;
    }

    /**
     * @return int
     */
    public function timeElapsed()
    {
        $calendar = Mage::getModel('core/date');
        return $calendar->timestamp($this->_dateNow()) - $calendar->timestamp($this->getStartedAt());
    }

    /**
     * @return string
     */
    private function _dateNow()
    {
        return Mage::getModel('core/date')->date('Y/m/d H:i:s');
    }
}
