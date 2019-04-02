<?php

class Cloudinary_Cloudinary_Model_System_Config_Source_ProductGallery_ThumbnailsNavigationShape
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'none',
                'label' => 'None',
            ),
            array(
                'value' => 'round',
                'label' => 'Round',
            ),
            array(
                'value' => 'square',
                'label' => 'Square',
            ),
            array(
                'value' => 'radius',
                'label' => 'Radius',
            ),
            array(
                'value' => 'rectangle',
                'label' => 'Rectangle',
            ),
        );
    }
}
