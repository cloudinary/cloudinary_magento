<?php


use CloudinaryExtension\Credentials;
use CloudinaryExtension\Image;
use CloudinaryExtension\ImageProvider;

class FakeImageProvider implements ImageProvider {


    private $uploadSuccessful = false;

    public function upload(Image $image, Credentials $credentials)
    {
        $this->uploadSuccessful = true;
    }

    public function getImageUrlByName($imageName)
    {
        return $this->uploadSuccessful;
    }
}