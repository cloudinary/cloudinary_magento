<?php

namespace CloudinaryExtension;

use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\Security\EnvironmentVariable;

class Configuration
{
    private $credentials;

    private $cloud;

    private $defaultTransformation;

    private $cdnSubdomain = true;

    private function __construct(Cloud $cloud,Credentials $credentials)
    {
        $this->cdnSubdomain = false;
        $this->credentials = $credentials;
        $this->cloud = $cloud;
        $this->defaultTransformation = Transformation::builder();
    }

    public static function fromCloudAndCredentials(Cloud $cloud, Credentials $credentials)
    {
        return new Configuration($cloud, $credentials);
    }

    public static function fromEnvironmentVariable(EnvironmentVariable $environmentVariable)
    {
        return new Configuration($environmentVariable->getCloud(), $environmentVariable->getCredentials());
    }

    public function getCloud()
    {
        return $this->cloud;
    }

    public function getCredentials()
    {
        return $this->credentials;
    }

    public function getDefaultTransformation()
    {
        return $this->defaultTransformation;
    }

    public function build()
    {
        $configuration = $this->getMandatoryConfiguration();
        if($this->cdnSubdomain) {
            $configuration['cdn_subdomain'] = true;
        }

        return $configuration;
    }

    public function enableCdnSubdomain()
    {
        $this->cdnSubdomain = true;
    }

    public function getCdnSubdomainStatus()
    {
        return $this->cdnSubdomain;
    }

    private function getMandatoryConfiguration()
    {
        return array(
            "cloud_name" => (string)$this->cloud,
            "api_key" => (string)$this->credentials->getKey(),
            "api_secret" => (string)$this->credentials->getSecret()
        );
    }
}
