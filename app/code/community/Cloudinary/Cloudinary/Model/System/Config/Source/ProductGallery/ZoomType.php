<?php

class Cloudinary_Cloudinary_Model_System_Config_Source_ProductGallery_ZoomType
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'inline',
                'label' => 'Inline',
            ),
            array(
                'value' => 'flyout',
                'label' => 'Flyout',
            ),
            array(
                'value' => 'lightbox',
                'label' => 'Lightbox',
            ),
        );
    }
}
