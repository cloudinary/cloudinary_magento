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
        //Clear config cache
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

        if (is_object($response) && ($response->error || !in_array($response->code, array(200,301,302)))) {
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
     * @return object
     */
    public function httpRequest($url)
    {
        $ch = curl_init();
        curl_setopt_array(
            $ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_VERBOSE => 1,
            CURLOPT_HEADER => 1,
            )
        );
        $res = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = array();
        foreach (explode("\r\n", substr($res, 0, $header_size)) as $i => $line) {
            if ($i === 0) {
                $headers['http_code'] = $line;
            } else {
                list($key, $value) = array_pad(explode(': ', $line), 2, '');
                if($key){
                    $headers[$key] = $value;
                }
            }
        }

        $body = substr($res, $header_size);
        curl_close($ch);

        $response = (object)array(
            "code" => $httpCode,
            "body" => $body,
            "headers" => (array) $headers,
            "error" => $err
        );
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
