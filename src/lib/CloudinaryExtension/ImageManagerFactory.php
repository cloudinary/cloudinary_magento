<?php

namespace CloudinaryExtension;

class ImageManagerFactory
{
    public static function fromConfiguration($config)
    {
        return new ImageManager(new CloudinaryImageProvider(
            $config->buildCredentials(),
            Cloud::fromName($config->getCloudName())
        ));
    }
} 