<?php

namespace Cloudinary\Credentials;

class Secret
{

    private $secret;

    public static function fromString($aSecret)
    {
        $secret = new Secret();
        $secret->secret = $aSecret;
        return $secret;
    }

    public function __toString()
    {
        return $this->secret;
    }

    private function __construct()
    {
    }
}
