<?php


namespace CloudinaryExtension;

use Cloudinary;
use Cloudinary\Uploader;

const DS = DIRECTORY_SEPARATOR;

include_once(implode(DS, array(dirname(__FILE__), '..', 'Cloudinary', 'src', 'Helpers.php')));

class CloudinaryImageProvider implements ImageProvider
{

    private $credentials;

    public function __construct(Credentials $credentials)
    {
        $this->credentials = $credentials;
    }

    public function upload(Image $image)
    {
        $this->logInToCloudinary();
        Uploader::upload((string)$image, array("public_id" => $this->getImageId($image)));
    }

    public function getImageUrlByName($imageName)
    {
        $this->logInToCloudinary();
        return \cloudinary_url($this->getImageId($imageName));
    }

    private function getImageId($image)
    {
        $imagePath = explode(DS, $image);
        $imageName = explode(".", $imagePath[count($imagePath) - 1]);
        return $imageName[0];
    }

    private function logInToCloudinary()
    {
        Cloudinary::config(
            array(
                "cloud_name" => "session-digital",
                "api_key" => (string)$this->credentials->getKey(),
                "api_secret" => (string)$this->credentials->getSecret()
            )
        );
    }
}
