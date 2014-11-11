<?php

namespace CloudinaryExtension\Security;

class Key
{

    private $key;

    public static function fromString($aKey)
    {
        $key = new Key();
        $key->key = $aKey;
        return $key;
    }

    public function __toString()
    {
        return $this->key;
    }

    private function __construct()
    {
    }
}
