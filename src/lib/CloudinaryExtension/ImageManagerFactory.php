<?php

namespace CloudinaryExtension;

class ImageManagerFactory
{
    public static function buildFromConfiguration(Configuration $configuration)
    {
        return new ImageManager(new CloudinaryImageProvider(
            $configuration->getCredentials(),
            $configuration->getCloud()
        ));
    }
}
