<?php

use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\ConfigurationInterface;
use CloudinaryExtension\Image;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\Image\Transformation\Freeform;

class Cloudinary_Cloudinary_Model_System_Config_Free extends Mage_Core_Model_Config_Data
{
    const ERROR_FORMAT = 'Incorrect custom transform - %s';
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
        if (!$this->hasAccountConfigured()) {
            return parent::_beforeSave();
        }

        $transform = $this->configuration
            ->getDefaultTransformation()
            ->withFreeform(Freeform::fromString($this->getValue()));

        $this->validateImageUrl($this->sampleImageUrl($transform));

        return $this;
    }

    /**
     * @param string $url
     */
    public function validateImageUrl($url)
    {
        try {
            $response = $this->httpRequest($url);
        } catch (Exception $e) {
            throw new Mage_Core_Exception(sprintf(self::ERROR_FORMAT, self::ERROR_DEFAULT));
        }

        if ($response->isError()) {
            throw new Mage_Core_Exception($this->formatError($response));
        }
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
    public function formatError(Zend_Http_Response $response)
    {
        return sprintf(
            self::ERROR_FORMAT,
            $response->getStatus() == 400 ? $response->getHeader('x-cld-error') : self::ERROR_DEFAULT
        );
    }

    /**
     * @param string $url
     * @return Zend_Http_Response
     */
    public function httpRequest($url)
    {
        $client = new Varien_Http_Client($url);
        $client->setMethod(Varien_Http_Client::GET);
        return $client->request();
    }

    /**
     * @return bool
     */
    public function hasAccountConfigured()
    {
        return (Mage::registry('cloudinaryEnvironmentVariableIsValid') || (is_null(Mage::registry('cloudinaryEnvironmentVariableIsValid')) && (string)$this->configuration->getCloud() !== ''))? true : false;
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
