<?php

namespace Cloudinary\Cloudinary\Model;

use CloudinaryExtension\Cloud;
use CloudinaryExtension\ConfigurationInterface;
use CloudinaryExtension\Credentials;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\Image\Transformation\Dpr;
use CloudinaryExtension\Image\Transformation\FetchFormat;
use CloudinaryExtension\Image\Transformation\Gravity;
use CloudinaryExtension\Image\Transformation\Quality;
use CloudinaryExtension\Security\CloudinaryEnvironmentVariable;
use CloudinaryExtension\UploadConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class Configuration implements ConfigurationInterface
{
    const CONFIG_PATH_ENABLED = 'cloudinary/cloud/cloudinary_enabled';
    const USER_PLATFORM_TEMPLATE = 'CloudinaryMagento/%s (Magento %s)';
    const CONFIG_PATH_ENVIRONMENT_VARIABLE = 'cloudinary/setup/cloudinary_environment_variable';
    const CONFIG_CDN_SUBDOMAIN = 'cloudinary/configuration/cloudinary_cdn_subdomain';
    const CONFIG_DEFAULT_GRAVITY = 'cloudinary/transformations/cloudinary_gravity';
    const CONFIG_DEFAULT_QUALITY = 'cloudinary/transformations/cloudinary_image_quality';
    const CONFIG_DEFAULT_DPR = 'cloudinary/transformations/cloudinary_image_dpr';
    const CONFIG_DEFAULT_FETCH_FORMAT = 'cloudinary/transformations/cloudinary_fetch_format';
    const CONFIG_FOLDERED_MIGRATION = 'cloudinary/configuration/cloudinary_foldered_migration';
    const USE_FILENAME = true;
    const UNIQUE_FILENAME = false;
    const OVERWRITE = false;
    const SCOPE_ID_ONE = 1;
    const SCOPE_ID_ZERO = 0;

    /**
     * @var ScopeConfigInterface
     */
    private $configReader;

    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @var EncryptorInterface
     */
    private $decryptor;

    /**
     * @var CloudinaryEnvironmentVariable
     */
    private $environmentVariable;

    /**
     * @param ScopeConfigInterface $configReader
     * @param WriterInterface      $configWriter
     * @param EncryptorInterface   $decryptor
     */
    public function __construct(
        ScopeConfigInterface $configReader,
        WriterInterface $configWriter,
        EncryptorInterface $decryptor
    ) {
        $this->configReader = $configReader;
        $this->configWriter = $configWriter;
        $this->decryptor = $decryptor;
    }

    /**
     * @return Cloud
     */
    public function getCloud()
    {
        return $this->getEnvironmentVariable()->getCloud();
    }

    /**
     * @return Credentials
     */
    public function getCredentials()
    {
        return $this->getEnvironmentVariable()->getCredentials();
    }

    /**
     * @return Transformation
     */
    public function getDefaultTransformation()
    {
        return Transformation::builder()
            ->withGravity(Gravity::fromString($this->getDefaultGravity()))
            ->withQuality(Quality::fromString($this->getImageQuality()))
            ->withDpr(Dpr::fromString($this->getImageDpr()));
    }

    /**
     * @return boolean
     */
    public function getCdnSubdomainStatus()
    {
        return $this->configReader->isSetFlag(self::CONFIG_CDN_SUBDOMAIN);
    }

    /**
     * @return string
     */
    public function getUserPlatform()
    {
        return sprintf(self::USER_PLATFORM_TEMPLATE, '1.0.0', '2.0.0');
    }

    /**
     * @return UploadConfig
     */
    public function getUploadConfig()
    {
        return UploadConfig::fromBooleanValues(self::USE_FILENAME, self::UNIQUE_FILENAME, self::OVERWRITE);
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->configReader->isSetFlag(self::CONFIG_PATH_ENABLED);
    }

    public function enable()
    {
        $this->configWriter->save(self::CONFIG_PATH_ENABLED, self::SCOPE_ID_ONE);
    }

    public function disable()
    {
        $this->configWriter->save(self::CONFIG_PATH_ENABLED, self::SCOPE_ID_ZERO);
    }

    /**
     * @return array
     */
    public function getFormatsToPreserve()
    {
        return ['png', 'webp', 'gif', 'svg'];
    }

    public function getMigratedPath($file)
    {
        return $file;
    }

    /**
     * @return string
     */
    public function getDefaultGravity()
    {
        return (string) $this->configReader->getValue(self::CONFIG_DEFAULT_GRAVITY);
    }

    /**
     * @return string
     */
    public function getFetchFormat()
    {
        if ($this->configReader->isSetFlag(self::CONFIG_DEFAULT_FETCH_FORMAT)) {
            return FetchFormat::FETCH_FORMAT_AUTO;
        }
        return '';
    }

    /**
     * @return string
     */
    public function getImageQuality()
    {
        return $this->configReader->getValue(self::CONFIG_DEFAULT_QUALITY);
    }

    /**
     * @return string
     */
    public function getImageDpr()
    {
        return $this->configReader->getValue(self::CONFIG_DEFAULT_DPR);
    }

    /**
     * @return CloudinaryEnvironmentVariable
     */
    private function getEnvironmentVariable()
    {
        if (is_null($this->environmentVariable)) {
            $this->environmentVariable = CloudinaryEnvironmentVariable::fromString(
                $this->decryptor->decrypt(
                    $this->configReader->getValue(self::CONFIG_PATH_ENVIRONMENT_VARIABLE)
                )
            );
        }
        return $this->environmentVariable;
    }
}
