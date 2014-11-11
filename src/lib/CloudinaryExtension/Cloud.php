<?php

namespace CloudinaryExtension;

class Cloud
{

    private $cloudName;


    private function __construct($cloudName)
    {
        $this->cloudName = $cloudName;
    }

    public static function fromName($aCloudName)
    {
        return new Cloud($aCloudName);
    }

    public function __toString()
    {
        return $this->cloudName;
    }
}
