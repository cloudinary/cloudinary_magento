<?php

class Cloudinary_Cloudinary_Model_System_Config_Source_ProductGallery_ThumbnailsSelectedStyle
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'border',
                'label' => 'Border',
            ),
            array(
                'value' => 'gradient',
                'label' => 'Gradient',
            ),
            array(
                'value' => 'all',
                'label' => 'All',
            ),
        );
    }
}
