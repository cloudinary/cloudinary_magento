<?php

use CloudinaryExtension\Migration\Task;

class Cloudinary_Cloudinary_Model_Migration extends Mage_Core_Model_Abstract implements Task
{
    const UPLOAD_MIGRATION_TYPE = 'upload';
    const DOWNLOAD_MIGRATION_TYPE = 'download';

    protected function _construct()
    {
        $this->_init('cloudinary_cloudinary/migration');
    }

    /**
     * @return bool
     */
    public function loadType($type = self::UPLOAD_MIGRATION_TYPE)
    {
        $col = $this->getCollection()->addFieldToFilter('type', $type)->setPageSize(1);
        return $col->count() ? $col->getFirstItem() : $this->setType($type);
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
        $this->setInfo('[]');
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

    public function getInfo()
    {
        $info = $this->getData('info');
        if (is_array($info) || is_object($info)) {
            return $info;
        } else {
            return (array) @json_decode($info, true);
        }
    }

    public function setInfo($info)
    {
        if (is_array($info) || is_object($info)) {
            $info = json_encode($info);
        }
        return $this->setData('info', $info);
    }
}
