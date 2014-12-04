<?php

namespace CloudinaryExtension;

class Configuration
{
    private $credentials;

    private $cloud;

    private function __construct(Credentials $credentials, Cloud $cloud)
    {
        $this->credentials = $credentials;
        $this->cloud = $cloud;
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
} 