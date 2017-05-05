<?php

namespace Facade;

use Cloudinary\Api;
use CloudinaryExtension\Security\CloudinaryEnvironmentVariable;

class CloudinaryConsole
{
    private $cloudinaryEnvironmentVariable;

    public function __construct($cloudinaryEnvironmentVariable)
    {
        $this->cloudinaryEnvironmentVariable = $cloudinaryEnvironmentVariable;
    }

    public function detailsForImagePath($imagePath)
    {
        if (getenv('BEHAT_DEBUG')) {
            echo sprintf('Fetching details for image path: %s%s', $imagePath, PHP_EOL);
        }

        $api = new Api();
        return $api->resource($imagePath);
    }

    public function deleteAll()
    {
        CloudinaryEnvironmentVariable::fromString($this->cloudinaryEnvironmentVariable);
        $api = new Api();
        $api->delete_all_resources();
    }
}
