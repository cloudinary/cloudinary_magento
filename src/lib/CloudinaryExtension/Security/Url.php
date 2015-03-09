<?php

namespace CloudinaryExtension\Security;

class Url
{

    private $url;

    private function __construct($url)
    {
        $this->url = $url;
    }

    public static function fromString($url)
    {
        return new Url($url);
    }

    public function __toString()
    {
        return $this->url;
    }
}
