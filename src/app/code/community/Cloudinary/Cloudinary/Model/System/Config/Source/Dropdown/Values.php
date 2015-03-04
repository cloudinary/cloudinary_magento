<?php

class Cloudinary_Cloudinary_Model_System_Config_Source_Dropdown_Values
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => '',
                'label' => 'Select gravity',
            ),
            array(
                'value' => 'g_face',
                'label' => 'Face',
            ),
            array(
                'value' => 'g_center',
                'label' => 'Center',
            ),
        );
    }
}
