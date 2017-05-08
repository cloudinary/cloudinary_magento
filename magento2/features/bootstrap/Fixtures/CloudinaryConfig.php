<?php

namespace Fixtures;

use Bex\Behat\Magento2InitExtension\Fixtures\BaseFixture;
use Cloudinary\Cloudinary\Model\Configuration;

class CloudinaryConfig extends BaseFixture
{
    /**
     * @var mixed
     */
    private $configuration;

    public function __construct()
    {
        parent::__construct();
        $this->configuration = $this->getMagentoObject(Configuration::class);
    }

    public function enableCloudinary()
    {
        $this->configuration->enable();
    }

    public function disableCloudinary()
    {
        $this->configuration->disable();
    }
}
