<?php

namespace CloudinaryExtension;

use Cloudinary;
use Cloudinary\Uploader;
use CloudinaryExtension\Image\Transformation;

class CloudinaryImageProvider implements ImageProvider
{
    private $credentials;

    private $cloud;

    public function __construct(Credentials $credentials, Cloud $cloud)
    {
        $this->credentials = $credentials;
        $this->cloud = $cloud;
        $this->authorise();
    }

    public function upload(Image $image)
    {
        Uploader::upload((string)$image, array("public_id" => $image->getId()));
    }

    public function transformImage(Image $image, Transformation $transformation)
    {
        return Image::fromPath(\cloudinary_url($image->getId(), $transformation->build()));
    }

    public function deleteImage(Image $image)
    {
        Uploader::destroy($image->getId());
    }

    private function authorise()
    {
        Cloudinary::config(array(
            "cloud_name" => (string)$this->cloud,
            "api_key"    => (string)$this->credentials->getKey(),
            "api_secret" => (string)$this->credentials->getSecret()
        ));
    }
}
