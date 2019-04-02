<?php

class Cloudinary_Cloudinary_Model_System_Config_Source_ProductGallery_Navigation
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'none',
                'label' => 'None',
            ),
            array(
                'value' => 'always',
                'label' => 'Always',
            ),
            array(
                'value' => 'mouseover',
                'label' => 'Mouseover',
            ),
        );
    }
}
