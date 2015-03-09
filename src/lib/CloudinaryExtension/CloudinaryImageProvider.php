<?php


namespace CloudinaryExtension;

use Cloudinary;
use Cloudinary\Api\GeneralError;
use Cloudinary\Uploader;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\Security;

class CloudinaryImageProvider implements ImageProvider
{
    private $configuration;

    private function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
        $this->authorise();
    }

    public static function fromConfiguration(Configuration $configuration)
    {
        return new CloudinaryImageProvider($configuration);
    }

    public function upload(Image $image)
    {
        Uploader::upload((string)$image, array("public_id" => $image->getId()));
    }

    public function transformImage(Image $image, Transformation $transformation = null)
    {
        if ($transformation === null) {
            $transformation = $this->configuration->getDefaultTransformation();
        }
        return Image::fromPath(\cloudinary_url($image->getId(), $transformation->build()));
    }

    public function validateCredentials()
    {
        $url = Security\Url::fromString("https://cloudinary.com/console/media_library/cms");
        $signedUrl = Security\SignedUrl::fromUrlAndCredentials($url, $this->credentials);

        $curlHandler = curl_init($signedUrl);

        curl_setopt($curlHandler, CURLOPT_HEADER, 1);
        curl_setopt($curlHandler, CURLOPT_FAILONERROR, 1);
        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, 1);

        curl_exec($curlHandler);

        $responseCode = curl_getinfo($curlHandler, CURLINFO_HTTP_CODE);
        $curlError = null;
        if (curl_errno($curlHandler))
        {
            $curlError = curl_error($curlHandler);
        }

        curl_close($curlHandler);

        if ($responseCode == 200 && is_null($curlError)) {
            return true;
        }
        return false;
    }

    public function deleteImage(Image $image)
    {
        Uploader::destroy($image->getId());
    }

    private function authorise()
    {
        Cloudinary::config($this->configuration->build());
    }
}
