<?php

namespace CloudinaryExtension;

class ImageProviderFactory
{

    public static function fromProviderNameAndConfiguration($providerName, Configuration $configuration)
    {
        if ($providerName == 'cloudinary') {
            return CloudinaryImageProvider::fromConfiguration($configuration);
        }
        $providerClass = ucwords($providerName) . 'ImageProvider';
        return $providerClass::fromConfiguration($configuration);
    }

}