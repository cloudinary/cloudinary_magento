<?php

class Cloudinary_Cloudinary_Helper_Config extends Mage_Core_Helper_Abstract
{
    /**
     * @param string $key
     * @param array $value
     * @return array
     */
    public function flatten($key, $value)
    {
        $output = array();

        $this->doFlatten($key, $value, $output);

        return $output;
    }

    /**
     * @param string $key
     * @param array|string $value
     * @param array $output
     */
    private function doFlatten($key, $value, array &$output)
    {
        if (is_array($value)) {
            foreach ($value as $childKey => $childValue) {
                $this->doFlatten(sprintf('%s/%s', $key, $childKey), $childValue, $output);
            }
        } else {
            $output[substr($key, 0, -6)] = $value;
        }
    }
}
