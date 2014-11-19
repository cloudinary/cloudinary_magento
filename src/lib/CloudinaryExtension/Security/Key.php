<?php

namespace CloudinaryExtension\Security;

class Key
{

    private $key;

    private function __construct($key)
    {
        $this->key = $key;
    }

    public static function fromString($aKey)
    {
        return new Key($aKey);
    }

    public function __toString()
    {
        return $this->key;
    }

}
