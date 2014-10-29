<?php


class FakeImageProvider implements Cloudinary_Cloudinary_Model_ImageProvider_Interface {


    private $key;
    private $secret;
    private $uploadSuccessful = false;

    public function setCredentials($aKey, $aSecret)
    {
        $this->key = $aKey;
        $this->secret = $aSecret;
    }

    public function upload($key, $secret)
    {
        if($key == $this->key && $secret == $this->secret) {
            $this->uploadSuccessful = true;
        }
    }

    public function wasUploadSuccessful()
    {
        return $this->uploadSuccessful;
    }

    public function addCredentials()
    {
        
    }
}