<?php

namespace EndToEnd;

use \Mage;
use \Cloudinary_Cloudinary_Helper_Configuration as Cloudinary_Helper;
use Facade\Magento as MagentoFacade;

trait CloudinaryConfig
{
    /**
     * @return MagentoFacade
     */
    protected abstract function getMagentoFacade();

    /**
     * @Given the Cloudinary module credentials are set
     */
    public function theCloudinaryModuleCredentialsAreSet()
    {
        $env = getenv('CLOUDINARY_URL');
        if (!$env) {
            throw new \Exception('The CLOUDINARY_URL environment variable is not defined');
        }

        $this->getMagentoFacade()->setConfigEncrypted(Cloudinary_Helper::CONFIG_PATH_ENVIRONMENT_VARIABLE, $env);
    }

    /**
     * @Given the Cloudinary module integration is enabled
     */
    public function theCloudinaryModuleIntegrationIsEnabled()
    {
        $this->getMagentoFacade()->setConfig(
            Cloudinary_Helper::CONFIG_PATH_ENABLED,
            Cloudinary_Helper::STATUS_ENABLED
        );
    }

    /**
     * @Given the Cloudinary module foldered mode is active
     */
    public function theCloudinaryModuleFolderedModeIsActive()
    {
        $this->getMagentoFacade()->setConfig(
            Cloudinary_Helper::CONFIG_FOLDERED_MIGRATION,
            Cloudinary_Helper::STATUS_ENABLED
        );
    }

    /**
     * @Given the Cloudinary module foldered mode is inactive
     */
    public function theCloudinaryModuleFolderedModeIsInactive()
    {
        $this->getMagentoFacade()->setConfig(
            Cloudinary_Helper::CONFIG_FOLDERED_MIGRATION,
            Cloudinary_Helper::STATUS_DISABLED
        );
    }
}
