<?php

namespace CloudinaryExtension;

use CloudinaryExtension\Image\Transformation;

class Configuration
{
    private $credentials;

    private $cloud;

    private $defaultTransformation;

    private function __construct(Credentials $credentials, Cloud $cloud)
    {
        $this->credentials = $credentials;
        $this->cloud = $cloud;
        $this->defaultTransformation = Transformation::builder();
    }

    public static function fromCloudAndCredentials(Credentials $credentials, Cloud $cloud)
    {
        return new Configuration($credentials, $cloud);
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

    public function setDefaultTransformation(Transformation $transformation)
    {
        $this->defaultTransformation = $transformation;
    }
}