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

    public function setMockCredentials(Key $aKey, Secret $aSecret)
    {
        $this->key = $aKey;
        $this->secret = $aSecret;
    }

    public function upload(Image $anImage, Credentials $credentials)
    {
        if((string)$credentials->getKey() === (string)$this->key && (string)$credentials->getSecret() === (string)$this->secret) {
            $this->uploadSuccessful = true;
        }
    }

    public function wasUploadSuccessful()
    {
        return $this->uploadSuccessful;
    }
}