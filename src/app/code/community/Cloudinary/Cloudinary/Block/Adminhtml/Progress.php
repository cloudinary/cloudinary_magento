<?php


class Cloudinary_Cloudinary_Block_Adminhtml_Progress extends Mage_Adminhtml_Block_Abstract
{
    public function __construct()
    {
        $this->_blockGroup = 'cloudinary_cloudinary';

        $this->_controller = 'adminhtml_progress';
    }

    public function build()
    {
        $percentComplete = $this->getSynchronizedImageCount() * 100 / $this->getImageCount();

        return $this->getLayout()
            ->createBlock('core/text')
            ->setText(sprintf(
                '<p>Progress: %d%%</p><p>%d of %d image migrated</p>',
                $percentComplete,
                $this->getSynchronizedImageCount(),
                $this->getImageCount()
            ));
    }
} 