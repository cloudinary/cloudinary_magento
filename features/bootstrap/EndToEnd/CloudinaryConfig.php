<?php

namespace EndToEnd;

use \Mage;
use \Cloudinary_Cloudinary_Model_Configuration as Configuration;
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

        $this->getMagentoFacade()->setConfigEncrypted(Configuration::CONFIG_PATH_ENVIRONMENT_VARIABLE, $env);
    }

    /**
     * @Given the Cloudinary module integration is enabled
     */
    public function theCloudinaryModuleIntegrationIsEnabled()
    {
        $this->getMagentoFacade()->setConfig(
            Configuration::CONFIG_PATH_ENABLED,
            Configuration::STATUS_ENABLED
        );
    }

    /**
     * @Given the Cloudinary module foldered mode is active
     */
    public function theCloudinaryModuleFolderedModeIsActive()
    {
        $this->getMagentoFacade()->setConfig(
            Configuration::CONFIG_FOLDERED_MIGRATION,
            Configuration::STATUS_ENABLED
        );
    }

    /**
     * @Given the Cloudinary module foldered mode is inactive
     */
    public function theCloudinaryModuleFolderedModeIsInactive()
    {
        $this->getMagentoFacade()->setConfig(
            Configuration::CONFIG_FOLDERED_MIGRATION,
            Configuration::STATUS_DISABLED
        );
    }
}
