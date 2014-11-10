<?php


namespace CloudinaryExtension;

use Cloudinary;
use Cloudinary\Uploader;

const DS = DIRECTORY_SEPARATOR;

include_once(dirname(__FILE__) . DS . '..' . DS . 'Cloudinary' . DS . 'src' . DS . 'Helpers.php');

class CloudinaryImageProvider implements ImageProvider
{
    public function upload(Image $image, Credentials $credentials)
    {
        Cloudinary::config(
            array(
                "cloud_name" => "session-digital",
                "api_key" => (string)$credentials->getKey(),
                "api_secret" => (string)$credentials->getSecret()
            )
        );

        Uploader::upload((string)$image);
    }

    public function getImageUrlByName($imageName)
    {
        Cloudinary::config(
            array(
                "cloud_name" => "session-digital",
                "api_key" => (string)$credentials->getKey(),
                "api_secret" => (string)$credentials->getSecret()
            )
        );

        $url = \cloudinary_url($imageName);
        return $url;
    }
}
