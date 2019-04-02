<?php

class Cloudinary_Cloudinary_Model_System_Config_Source_ProductGallery_Transition
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'none',
                'label' => 'None',
            ),
            array(
                'value' => 'fade',
                'label' => 'Fade',
            ),
            array(
                'value' => 'slide',
                'label' => 'Slide',
            ),
        );
    }
}
