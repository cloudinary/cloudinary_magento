<?php

namespace CloudinaryExtension;

class ImageProviderFactory
{

    public static function fromProviderName($providerName, Credentials $credentials, Cloud $cloud)
    {
        if ($providerName == 'cloudinary') {
            return new CloudinaryImageProvider($credentials, $cloud);
        }
        $providerClass = ucwords($providerName) . 'ImageProvider';
        return new $providerClass($credentials, $cloud);
    }

}