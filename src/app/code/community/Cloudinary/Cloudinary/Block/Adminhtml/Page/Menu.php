<?php

class Cloudinary_Cloudinary_Block_Adminhtml_Page_Menu extends Mage_Adminhtml_Block_Page_Menu
{

    public function getMenuArray()
    {
        $menuArray = $this->_buildMenuArray();
        return $this->_addCloudinaryMediaLibraryUrlToMenu($menuArray);
    }

    private function _addCloudinaryMediaLibraryUrlToMenu($menuArray)
    {
        $menuArray['cloudinary_cloudinary']['children']['console']['click'] =  sprintf(
            "window.open('%s')",
            Mage::helper('cloudinary_cloudinary/console')->getMediaLibraryUrl()
        );
        return $menuArray;
    }

}