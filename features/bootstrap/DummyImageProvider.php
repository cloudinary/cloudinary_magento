<?php


class DummyImageProvider {


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

    public function wasUploadSuccessful(){
        return $this->uploadSuccessful;
    }
}