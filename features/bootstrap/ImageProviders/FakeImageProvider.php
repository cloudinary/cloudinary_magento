<?php

namespace ImageProviders;

use CloudinaryExtension\Cloud;
use CloudinaryExtension\Credentials;
use CloudinaryExtension\Security\Key;
use CloudinaryExtension\Security\Secret;
use CloudinaryExtension\Image;
use CloudinaryExtension\ImageProvider;

class FakeImageProvider implements ImageProvider {


    private $key;
    private $secret;
    private $uploadedImageUrl = array();
    private $credentials;
    private $mockCloud;
    private $cloud;

    public function __construct(Credentials $credentials, Cloud $cloud)
    {
        $this->credentials = $credentials;
        $this->cloud = $cloud;
    }

    public function setMockCredentials(Key $aKey, Secret $aSecret)
    {
        $this->key = $aKey;
        $this->secret = $aSecret;
    }

    public function setMockCloud(Cloud $mockCloud)
    {
        $this->mockCloud = $mockCloud;
    }

    public function upload(Image $image)
    {
        $this->uploadedImageUrl[(string)$image] = 'uploaded image URL';
    }

    public function getImageUrlByName($image, $options = array())
    {
        $imageName = (string)$image;
        if($this->areCredentialsCorrect() && $this->isCloudCorrect()) {
            return array_key_exists($imageName, $this->uploadedImageUrl) ? $this->uploadedImageUrl[$imageName] : '';
        }
        return '';
    }

    public function validateCredentials()
    {
        return $this->areCredentialsCorrect();
    }

    private function areCredentialsCorrect()
    {
        return (string)$this->credentials->getKey() === (string)$this->key && (string)$this->credentials->getSecret() === (string)$this->secret;
    }

    private function isCloudCorrect()
    {
        return (string)$this->mockCloud == (string)$this->cloud;
    }

    public function transformImage(Image $image, \CloudinaryExtension\Image\Transformation $transformation)
    {
        $imageName = (string)$image;
        if($this->areCredentialsCorrect() && $this->isCloudCorrect()) {
            return array_key_exists($imageName, $this->uploadedImageUrl) ? $this->uploadedImageUrl[$imageName] : '';
        }
        return '';
    }

    public function deleteImage(Image $image)
    {
        unset($this->uploadedImageUrl[(string)$image]);
    }
}