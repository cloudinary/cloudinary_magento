<?php

class Cloudinary_Cloudinary_Block_Adminhtml_Reset_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(
            array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save'),
                'method' => 'post',
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset('display', array('class' => 'fieldset-wide'));

        $fieldset->addField(
            'password', 'password', array(
            'name' => 'password',
            'label' => Mage::helper('cloudinary_cloudinary')->__('Enter your admin password to confirm reset')
            )
        );

        return parent::_prepareForm();
    }
}
