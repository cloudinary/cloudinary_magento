<?php

class Cloudinary_Cloudinary_Block_Adminhtml_Cms_Wysiwyg_Images_Content_Uploader extends Mage_Adminhtml_Block_Cms_Wysiwyg_Images_Content_Uploader
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
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->setTemplate('cloudinary/media/uploader.phtml');
        return parent::_toHtml();
    }

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
                    'after_html'    => '</div>
                        <script type="text/javascript">
                        //<![CDATA[
                        var ' . self::DEFAULT_CLD_ML_BUTTON_ID_SUFFIX . '_instance_' . $this->getHtmlId() . ' = new CloudinaryMediaLibrary(' . $this->getCloudinaryMediaLibraryWidgetOptions() . ');
                        //]]>
                        </script>',
                    'id'            => $this->getElementId(self::DEFAULT_CLD_ML_BUTTON_ID_SUFFIX . '_button'),
                    'label'         => Mage::helper('uploader')->__('Add From Cloudinary...'),
                    'type'          => 'button',
                ))
        );

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

    /**
     * Get Cloudinary media library widget options
     *
     * @param bool $multiple Allow multiple
     * @param bool $refresh Refresh options
     * @return string
     */
    public function getCloudinaryMediaLibraryWidgetOptions($multiple = true, $refresh = false)
    {
        if (!($cloudinaryMLoptions = Mage::helper('cloudinary_cloudinary/MediaLibraryHelper')->getCloudinaryMLOptions($multiple, $refresh))) {
            return null;
        }
        return Mage::helper('core')->jsonEncode(array(
            'htmlId' => $this->getHtmlId(),
            'cldMLid' => self::DEFAULT_CLD_ML_BUTTON_ID_SUFFIX . '_' . $this->getHtmlId(),
            'imageUploaderUrl' => $this->getCldImageUploaderUrl(),
            'buttonSelector' => '#' . $this->getElementId(self::DEFAULT_CLD_ML_BUTTON_ID_SUFFIX . '_button'),
            'triggerSelector' => $this->getTriggerSelector(),
            'triggerEvent' => $this->getTriggerEvent(),
            'callbackHandler' => $this->getCallbackHandler(),
            'callbackHandlerMethod' => $this->getCallbackHandlerMethod(),
            'useDerived' => $this->getUseDerived(),
            'addTmpExtension' => $this->getAddTmpExtension(),
            'cloudinaryMLoptions' => $cloudinaryMLoptions,
            'cloudinaryMLshowOptions' => Mage::helper('cloudinary_cloudinary/MediaLibraryHelper')->getCloudinaryMLshowOptions('image'),
        ));
    }

    /**
     * @return string
     */
    protected function getCldImageUploaderUrl()
    {
        return Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('*/cloudinaryretrieveimage/upload', array('type' => 'wysiwyg_image'));
    }

    /**
     * @return bool
     */
    protected function getAddTmpExtension()
    {
        return true;
    }

    /**
     * @return bool
     */
    protected function getUseDerived()
    {
        return false;
    }

    /**
     * @return mixed
     */
    protected function getTriggerEvent()
    {
        return 'addItem';
    }

    /**
     * @return mixed
     */
    protected function getTriggerSelector()
    {
        return 'triggerSelector';
    }

    /**
     * @return mixed
     */
    protected function getCallbackHandler()
    {
        return 'window.MediabrowserInstance';
    }

    /**
     * @return mixed
     */
    protected function getCallbackHandlerMethod()
    {
        return 'selectFolder';
    }
}
