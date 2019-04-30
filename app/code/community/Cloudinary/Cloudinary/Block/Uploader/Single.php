<?php

class Cloudinary_Cloudinary_Block_Uploader_Single extends Mage_Uploader_Block_Single
{
    /**
     * Default browse button ID suffix
     */
    const DEFAULT_CLD_ML_BUTTON_ID_SUFFIX = 'cld_ml';

    /**
     * Template used for uploader
     *
     * @var string
     */
    protected $_template = 'cloudinary/media/uploader.phtml';
    
    /**
     * Prepare layout, create buttons, set front-end elements ids
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->setChild(
            'cloudinary_ml_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->addData(array(
                    // Workaround for IE9
                    'before_html'   => sprintf(
                        '<div style="display:inline-block;" id="%s">',
                        $this->getElementId(self::DEFAULT_CLD_ML_BUTTON_ID_SUFFIX)
                    ),
                    'after_html'    => '</div>',
                    'id'            => $this->getElementId(self::DEFAULT_CLD_ML_BUTTON_ID_SUFFIX . '_button'),
                    'label'         => Mage::helper('uploader')->__('Add From Cloudinary...'),
                    'type'          => 'button',
                ))
        );

        echo 123;
        die;

        return $this;
    }

    /**
     * Get CLD ML button html
     *
     * @return string
     */
    public function getCldMLButtonHtml()
    {
        return $this->getChildHtml('cloudinary_ml_button');
    }
}
