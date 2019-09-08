<?php

use CloudinaryExtension\ConfigurationInterface;

class Cloudinary_Cloudinary_Helper_ProductGalleryHelper extends Mage_Core_Helper_Abstract
{

    /**
     * @var ConfigurationInterface
     */
    protected $_configuration;

    /**
     * Cloudinary PG Options
     * @var array|null
     */
    protected $_cloudinaryPGoptions;

    protected $_casting = array(
        'themeProps_primary' => 'string',
        'themeProps_onPrimary' => 'string',
        'themeProps_active' => 'string',
        'themeProps_onActive' => 'string',
        'transition' => 'string',
        'aspectRatio' => 'string',
        'navigation' => 'string',
        'zoom' => 'bool',
        'zoomProps_type' => 'string',
        'zoomPropsViewerPosition' => 'string',
        'zoomProps_trigger' => 'string',
        'carouselLocation' => 'string',
        'carouselOffset' => 'float',
        'carouselStyle' => 'string',
        'thumbnailProps_width' => 'float',
        'thumbnailProps_height' => 'float',
        'thumbnailProps_navigationShape' => 'string',
        'thumbnailProps_selectedStyle' => 'string',
        'thumbnailProps_selectedBorderPosition' => 'string',
        'thumbnailProps_selectedBorderWidth' => 'float',
        'thumbnailProps_mediaSymbolShape' => 'string',
        'indicatorProps_shape' => 'string',
    );

    /**
     * @method __construct
     */
    public function __construct()
    {
        $this->_configuration = Mage::getModel('cloudinary_cloudinary/configuration');
    }

    /**
     * @method getCloudinaryPGOptions
     * @param bool $refresh Refresh options
     * @param bool $ignoreDisabled Get te options even if the module or the product gallery are disabled
     * @return array
     */
    public function getCloudinaryPGOptions($refresh = false, $ignoreDisabled = false)
    {
        if ((is_null($this->_cloudinaryPGoptions) || $refresh) && ($ignoreDisabled || ($this->_configuration->isEnabled() && $this->_configuration->isEnabledProductGallery()))) {
            $this->_cloudinaryPGoptions = $this->_configuration->getProductGalleryAll();
            foreach ($this->_cloudinaryPGoptions as $key => $value) {
                //Change casting
                if (isset($this->_casting[$key])) {
                    \settype($value, $this->_casting[$key]);
                    $this->_cloudinaryPGoptions[$key] = $value;
                }

                //Build options hierarchy
                $path = explode("_", $key);
                $_path = $path[0];
                if (in_array($_path, array('themeProps','zoomProps','thumbnailProps','indicatorProps'))) {
                    if (!isset($this->_cloudinaryPGoptions[$_path])) {
                        $this->_cloudinaryPGoptions[$_path] = array();
                    }

                    array_shift($path);
                    $path = implode("_", $path);
                    $this->_cloudinaryPGoptions[$_path][$path] = $value;
                    unset($this->_cloudinaryPGoptions[$key]);
                }
            }

            if (isset($this->_cloudinaryPGoptions['enabled'])) {
                unset($this->_cloudinaryPGoptions['enabled']);
            }

            if (isset($this->_cloudinaryPGoptions['custom_free_params'])) {
                $customFreeParams = (array) @json_decode($this->_cloudinaryPGoptions['custom_free_params'], true);
                $this->_cloudinaryPGoptions = array_replace_recursive($this->_cloudinaryPGoptions, $customFreeParams);
                unset($this->_cloudinaryPGoptions['custom_free_params']);
            }

            $this->_cloudinaryPGoptions['cloudName'] = (string)$this->_configuration->getCloud();
        }

        return $this->_cloudinaryPGoptions;
    }

    /**
     * @return bool
     */
    public function canDisplayProductGallery()
    {
        return ($this->_configuration->isEnabled() && $this->_configuration->isEnabledProductGallery()) ? true : false;
    }
}
