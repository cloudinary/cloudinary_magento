<?php

namespace CloudinaryExtension\Security;

use Cloudinary;
use CloudinaryExtension\Cloud;
use CloudinaryExtension\Credentials;

class CloudinaryEnvironmentVariable implements EnvironmentVariable
{

    private $environmentVariable;

    private function __construct($environmentVariable)
    {
        $this->environmentVariable = (string)$environmentVariable;
        Cloudinary::config_from_url(str_replace('CLOUDINARY_URL=', '', $environmentVariable));
    }

    public static function fromString($environmentVariable)
    {
        return new CloudinaryEnvironmentVariable($environmentVariable);
    }

    public function getCloud()
    {
        return Cloud::fromName(Cloudinary::config_get('cloud_name'));
    }

    public function getCredentials()
    {
        return new Credentials(
            Key::fromString(Cloudinary::config_get('api_key')),
            Secret::fromString(Cloudinary::config_get('api_secret'))
        );
    }

    public function __toString()
    {
        return $this->environmentVariable;
    }

}