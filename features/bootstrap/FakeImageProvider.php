<?php


use Cloudinary\Credentials;
use Cloudinary\Image;
use Cloudinary\ImageProvider;

class FakeImageProvider implements ImageProvider {


    private $uploadSuccessful = false;

    public function upload(Image $anImage, Credentials $credentials)
    {
        $this->uploadSuccessful = true;
    }

    public function wasUploadSuccessful()
    {
        return $this->uploadSuccessful;
    }
}