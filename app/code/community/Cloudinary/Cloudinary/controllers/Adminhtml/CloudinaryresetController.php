<?php

class Cloudinary_Cloudinary_Adminhtml_CloudinaryresetController extends Mage_Adminhtml_Controller_Action
{
    const RESET_SUCCESS_MESSSAGE1 = 'All Cloudinary module data has been reset. Please clear your ';
    const RESET_SUCCESS_MESSSAGE2 = 'block and page caches to ensure changes take effect.';
    const RESET_ERROR_MESSAGE = 'Incorrect admin password.';

    public function indexAction()
    {
        $this->loadLayout();

        $this->_title($this->__('Reset Cloudinary'));
        $this->_setActiveMenu('cloudinary_cloudinary/reset');

        $this->renderLayout();
    }

    public function saveAction()
    {
        $formPassword = $this->getRequest()->getPost('password');
        $userPassword = Mage::getSingleton('admin/session')
            ->getUser()
            ->getPassword();

        if ($this->_validateFormKey() && Mage::helper('core')->validateHash($formPassword, $userPassword)) {
            $this->removeModuleData();
            $this->_getSession()->addSuccess(self::RESET_SUCCESS_MESSSAGE1 . self::RESET_SUCCESS_MESSSAGE2);
        } else {
            $this->_getSession()->addError(self::RESET_ERROR_MESSAGE);
        }

        return $this->_redirect('*/cloudinaryreset');
    }

    private function removeModuleData()
    {
        Mage::helper('cloudinary_cloudinary/reset')->removeModuleData();
        Mage::getModel('cloudinary_cloudinary/logger')->notice(
            self::RESET_SUCCESS_MESSSAGE1 . self::RESET_SUCCESS_MESSSAGE2
        );
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('cloudinary_cloudinary/reset');
    }
}
