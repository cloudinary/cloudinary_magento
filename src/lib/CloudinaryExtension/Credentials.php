<?php

namespace CloudinaryExtension;


use CloudinaryExtension\Security\Key;
use CloudinaryExtension\Security\Secret;

class Credentials
{

    private $key;
    private $secret;

    public function __construct(Key $key,Secret $secret)
    {
        $this->key = $key;
        $this->secret = $secret;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getSecret()
    {
        return $this->secret;
    }
}
