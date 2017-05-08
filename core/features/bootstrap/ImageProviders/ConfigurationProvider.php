<?php
/**
 * Created by PhpStorm.
 * User: danielk
 * Date: 26/01/16
 * Time: 14:23
 */

namespace ImageProviders;


use CloudinaryExtension\Cloud;
use CloudinaryExtension\ConfigurationInterface;
use CloudinaryExtension\Credentials;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\Security\Secret;
use CloudinaryExtension\UploadConfig;
use WebDriver\Key;

class ConfigurationProvider implements ConfigurationInterface
{
    private $defaultTransformation;
    private $isEnabledCdnSubdomain;

    public function __constructor()
    {
        $this->defaultTransformation = Transformation::builder();
        $this->isEnabledCdnSubdomain = false;
    }
    /**
     * @return Cloud
     */
    public function getCloud()
    {
        return Cloud::fromName('aCloudName');
    }

    /**
     * @return Credentials
     */
    public function getCredentials()
    {
        return Credentials::fromKeyAndSecret(
            Key::fromString('aKey'),
            Secret::fromString('aSecret')
        );
    }

    /**
     * @return Transformation
     */
    public function getDefaultTransformation()
    {
        return $this->defaultTransformation;
    }

    /**
     * @param Transformation $transformation
     */
    public function setDefaultTransformation(Transformation $transformation)
    {
        $this->defaultTransformation = $transformation;
    }

    /**
     * @return boolean
     */
    public function getCdnSubdomainStatus()
    {
        return $this->isEnabledCdnSubdomain;
    }

    /**
     * @return string
     */
    public function getUserPlatform()
    {

    }

    /**
     * @return UploadConfig
     */
    public function getUploadConfig()
    {

    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {

    }

    public function enable()
    {

    }

    public function disable()
    {

    }

    /**
     * @return array
     */
    public function getFormatsToPreserve()
    {

    }

    public function enableCdnSubdomain()
    {
        $this->isEnabledCdnSubdomain = true;
    }
}