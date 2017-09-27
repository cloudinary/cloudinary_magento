<?php

namespace ImageProviders;

use CloudinaryExtension\Cloud;
use CloudinaryExtension\Security\EnvironmentVariable;
use CloudinaryExtension\Security\Key;
use CloudinaryExtension\Security\Secret;
use CloudinaryExtension\Image;
use CloudinaryExtension\ImageProvider;

class FakeImageProvider implements ImageProvider
{
    private $key;
    private $secret;
    private $uploadedImageUrl = array();
    private $credentials;
    private $mockCloud;
    private $cloud;

    public function __construct(EnvironmentVariable $environmentVariable)
    {
        $this->credentials = $environmentVariable->getCredentials();
        $this->cloud = $environmentVariable->getCloud();
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
        if (array_key_exists((string)$image, $this->uploadedImageUrl)) {
            throw new \Exception('Image already exist at the provider');
        }
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

    public function retrieveTransformed(Image $image, \CloudinaryExtension\Image\Transformation $transformation)
    {
        $imageName = (string)$image;
        if($this->areCredentialsCorrect() && $this->isCloudCorrect()) {
            return array_key_exists($imageName, $this->uploadedImageUrl) ? $this->uploadedImageUrl[$imageName] : '';
        }
        return '';
    }

    public function retrieve(Image $image)
    {

    }

    public function delete(Image $image)
    {
        unset($this->uploadedImageUrl[(string)$image]);
    }
}