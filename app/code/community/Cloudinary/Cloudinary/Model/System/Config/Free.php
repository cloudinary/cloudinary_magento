<?php

use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\ConfigurationInterface;
use CloudinaryExtension\Image;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\Image\Transformation\Freeform;

class Cloudinary_Cloudinary_Model_System_Config_Free extends Mage_Core_Model_Config_Data
{
    const ERROR_FORMAT = 'Incorrect Cloudinary Transformation - %s';
    const ERROR_DEFAULT = 'please update';

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param string $resourceModel
     */
    protected function _init($resourceModel)
    {
        $this->configuration = Mage::getModel('cloudinary_cloudinary/configuration');
        return parent::_init($resourceModel);
    }

    /**
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        //Clear config cache before mapping
        Mage::app()->getCacheInstance()->cleanType("config");
        Mage::dispatchEvent('adminhtml_cache_refresh_type', array('type' => "config"));
        Mage::getConfig()->reinit();

        if (!$this->hasAccountConfigured()) {
            return parent::_beforeSave();
        }

        $transform = $this->configuration
            ->getDefaultTransformation()
            ->withFreeform(Freeform::fromString($this->getValue()));

        $this->validateImageUrl($this->sampleImageUrl($transform), false);

        return $this;
    }

    /**
     * @param string $url
     * @param bool   $strict Throw exception on errors
     * @return bool
     */
    public function validateImageUrl($url, $strict = true)
    {
        try {
            $response = $this->httpRequest($url);
        } catch (Exception $e) {
            $this->setValue(null);
            if ($strict) {
                throw new Mage_Core_Exception(sprintf(self::ERROR_FORMAT, self::ERROR_DEFAULT));
            } else {
                Mage::getSingleton('adminhtml/session')->addError(sprintf(self::ERROR_FORMAT, self::ERROR_DEFAULT));
            }
            return false;
        }

        if (is_object($response) && ($response->error || !in_array($response->code, [200,301,302]))) {
            $this->setValue(null);
            if ($strict) {
                throw new Mage_Core_Exception($this->formatError($response));
            } else {
                Mage::getSingleton('adminhtml/session')->addError($this->formatError($response));
            }
            return false;
        }

        return true;
    }

    /**
     * @param string $freeTransforma
     * @return Transformation
     */
    public function defaultTransform($freeTransform)
    {
        return Mage::getModel('cloudinary_cloudinary/configuration')
            ->getDefaultTransformation()
            ->withFreeform(Freeform::fromString($freeTransform));
    }

    /**
     * @param Zend_Http_Response $response
     * @return string
     */
    public function formatError($response)
    {
        return sprintf(
            self::ERROR_FORMAT,
            (is_object($response) && isset($response->headers['x-cld-error']) && $response->headers['x-cld-error']) ? $response->headers['x-cld-error'] : self::ERROR_DEFAULT
        );
    }

    /**
     * @param string $url
     * @return Zend_Http_Response
     */
    public function httpRequest($url)
    {
        $curl = new Varien_Http_Adapter_Curl();
        $curl->write(Zend_Http_Client::GET, $url);
        $response = $curl->read();
        $response = (object)[
            "code" => Zend_Http_Response::extractCode($response),
            "body" => Zend_Http_Response::extractBody($response),
            "headers" => (array) Zend_Http_Response::extractHeaders($response),
            "error" => $curl->getError()
        ];
        return $response;
    }

    /**
     * @return bool
     */
    public function hasAccountConfigured()
    {
        return (string)$this->configuration->getCloud() !== '';
    }

    /**
     * @param Transformation $transformation
     * @return string
     */
    public function sampleImageUrl(Transformation $transformation)
    {
        $imageProvider = CloudinaryImageProvider::fromConfiguration($this->configuration);
        return (string)$imageProvider->retrieveTransformed(
            Image::fromPath('sample'),
            $transformation
        );
    }

    /**
     * @param String $filename
     * @param Transformation $transformation
     * @return string
     */
    public function namedImageUrl($filename, Transformation $transformation)
    {
        if (empty($filename)) {
            throw new RuntimeException('Error: missing image identifier');
        }

        $imageProvider = CloudinaryImageProvider::fromConfiguration($this->configuration);

        return (string)$imageProvider->retrieveTransformed(
            Image::fromPath(
                $filename,
                $this->configuration->isFolderedMigration() ? $this->configuration->getMigratedPath($filename) : ''
            ),
            $transformation
        );
    }
}
