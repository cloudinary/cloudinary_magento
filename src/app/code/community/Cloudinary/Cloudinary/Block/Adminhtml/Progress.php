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
        $percentComplete = number_format(
            $this->getSynchronizedImageCount() * 100 / $this->getImageCount(), 2
        );

        return $this->getLayout()
            ->createBlock('core/text')
            ->setText(
                '<p>Progress: ' . $percentComplete . '%</p>' .
                '<p>' . $this->getSynchronizedImageCount() . ' of ' . $this->getImageCount() . ' images migrated.</p>'
            );
    }
} 