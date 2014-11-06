<?php


namespace CloudinaryExtension;

use Cloudinary;
use Cloudinary\Uploader;

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

    public function wasUploadSuccessful()
    {

    }
}
