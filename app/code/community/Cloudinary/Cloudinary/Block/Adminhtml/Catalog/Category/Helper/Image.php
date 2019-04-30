<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Category form image field helper
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Cloudinary_Cloudinary_Block_Adminhtml_Catalog_Category_Helper_Image extends Mage_Adminhtml_Block_Catalog_Category_Helper_Image
{
    /**
     * Default browse button ID suffix
     */
    const DEFAULT_CLD_ML_BUTTON_ID_SUFFIX = 'cld_ml';

    /**
     * Return element html code
     *
     * @return string
     */
    public function getElementHtml()
    {
        return parent::getElementHtml();
        //$html = parent::getElementHtml();

        $html = Mage::app()->getLayout()->createBlock('adminhtml/widget_button')
            ->addData(array(
                'before_html'   => sprintf(
                    '<div style="display:inline-block;margin-right:12px;" id="%s">',
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
            ))->toHtml();

        $html .= parent::getElementHtml();
        //$html = '<button id="id_6c2c39d0c25611be1b08deb8f3c2e195_Uploader-cld_ml_button" title="Add From Cloudinary..." type="button" class="scalable " onclick="" style=""><span><span><span>Add From Cloudinary...</span></span></span></button>' . $html;

        return $html;
    }

    public function getHtmlId()
    {
        if ($this->getData('id')===null) {
            $this->setData('id', Mage::helper('core')->uniqHash('id_'));
        }
        return $this->getData('id');
    }

    /**
     * Get button unique id
     *
     * @param string $suffix
     * @return string
     */
    public function getElementId($suffix)
    {
        return $this->getHtmlId() . '-' . $suffix;
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
