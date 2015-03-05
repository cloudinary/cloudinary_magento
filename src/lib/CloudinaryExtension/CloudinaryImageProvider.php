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
    }

    public function upload(Image $image)
    {
        $this->setCloudinaryCredentialsAndCloudName();
        Uploader::upload((string)$image, array("public_id" => $this->getImageId($image)));
    }

    public function getImageUrlByName($imageName, $options = array())
    {
        $this->setCloudinaryCredentialsAndCloudName();
        return \cloudinary_url($this->getImageId($imageName), Transformation::builder()->build());
    }

    public function transformImage(Image $image, Transformation $transformation)
    {
        $this->setCloudinaryCredentialsAndCloudName();
        return Image::fromPath(\cloudinary_url($this->getImageId((string)$image), $transformation->build()));
    }

    private function getImageId($image)
    {
        $imagePath = explode(DIRECTORY_SEPARATOR, $image);
        $imageName = explode(".", $imagePath[count($imagePath) - 1]);
        return $imageName[0];
    }

    private function setCloudinaryCredentialsAndCloudName()
    {
        Cloudinary::config(
            array(
                "cloud_name" => (string)$this->cloud,
                "api_key" => (string)$this->credentials->getKey(),
                "api_secret" => (string)$this->credentials->getSecret()
            )
        );
    }

    public function deleteImage(Image $image)
    {
        $this->setCloudinaryCredentialsAndCloudName();
        Uploader::destroy($this->getImageId($image));
    }
}
