<?php


use CloudinaryExtension\Credentials;
use CloudinaryExtension\Security\Key;
use CloudinaryExtension\Security\Secret;
use CloudinaryExtension\Image;
use CloudinaryExtension\ImageProvider;

class DummyImageProvider implements ImageProvider {


    private $key;
    private $secret;
    private $uploadSuccessful = false;
    private $credentials;

    public function __construct(Credentials $credentials)
    {

        $this->credentials = $credentials;
    }

    public function setMockCredentials(Key $aKey, Secret $aSecret)
    {
        $this->key = $aKey;
        $this->secret = $aSecret;
    }

    public function upload(Image $image)
    {
        if((string)$this->credentials->getKey() === (string)$this->key && (string)$this->credentials->getSecret() === (string)$this->secret) {
            $this->uploadSuccessful = true;
        }
    }

    public function getImageUrlByName($imageName)
    {
        return $this->uploadSuccessful;
    }
}