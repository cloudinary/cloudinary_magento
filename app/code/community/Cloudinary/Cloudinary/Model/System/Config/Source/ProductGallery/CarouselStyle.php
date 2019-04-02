<?php

class Cloudinary_Cloudinary_Model_System_Config_Source_ProductGallery_CarouselStyle
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'none',
                'label' => 'None',
            ),
            array(
                'value' => 'thumbnails',
                'label' => 'Thumbnails',
            ),
            array(
                'value' => 'indicators',
                'label' => 'Indicators',
            ),
        );
    }
}
