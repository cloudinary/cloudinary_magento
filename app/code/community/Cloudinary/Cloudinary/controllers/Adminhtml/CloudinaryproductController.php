<?php

class Cloudinary_Cloudinary_Adminhtml_CloudinaryproductController extends Mage_Adminhtml_Controller_Action
{
    public function galleryAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
