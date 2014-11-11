<?php


use CloudinaryExtension\Credentials;
use CloudinaryExtension\Image;
use CloudinaryExtension\ImageProvider;

class FakeImageProvider implements ImageProvider {


    private $uploadedImageUrl = '';

    public function upload(Image $image)
    {
        $this->uploadedImageUrl = 'Uploaded Image url';
    }

    public function getImageUrlByName($imageName)
    {
        return $this->uploadedImageUrl;
    }
}