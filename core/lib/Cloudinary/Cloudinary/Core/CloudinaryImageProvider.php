<?php


namespace Cloudinary\Cloudinary\Core;

use Cloudinary;
use Cloudinary\Uploader;
use Cloudinary\Cloudinary\Core\Exception\ApiError;
use Cloudinary\Cloudinary\Core\Image\Transformation;
use Cloudinary\Cloudinary\Core\Security;
use Cloudinary\Cloudinary\Core\Image\Transformation\Format;
use Cloudinary\Cloudinary\Core\Image\Transformation\FetchFormat;

class CloudinaryImageProvider implements ImageProvider
{
    private $configuration;
    /**
     * @var UploadResponseValidator
     */
    private $uploadResponseValidator;
    /**
     * @var ConfigurationBuilder
     */
    private $configurationBuilder;
    /**
     * @var CredentialValidator
     */
    private $credentialValidator;

    public function __construct(
        ConfigurationInterface $configuration,
        ConfigurationBuilder $configurationBuilder,
        UploadResponseValidator $uploadResponseValidator,
        CredentialValidator $credentialValidator
    ) {
        $this->configuration = $configuration;
        $this->uploadResponseValidator = $uploadResponseValidator;
        $this->configurationBuilder = $configurationBuilder;
        $this->credentialValidator = $credentialValidator;
        if ($configuration->isEnabled()) {
            $this->authorise();
        }
    }

    public static function fromConfiguration(ConfigurationInterface $configuration){
        return new CloudinaryImageProvider(
            $configuration,
            new ConfigurationBuilder($configuration),
            new UploadResponseValidator(),
            new CredentialValidator()
        );
    }

    public function upload(Image $image)
    {
        $uploadResult = null;

        try {
            $uploadResult = Uploader::upload(
                (string)$image,
                $this->configuration->getUploadConfig()->toArray() + [ "folder" => $image->getRelativeFolder()]
            );
        } catch (\Exception $e) {
            ApiError::throwWith($image, $e->getMessage());
        }

        return $this->uploadResponseValidator->validateResponse($image, $uploadResult);
    }

    public function retrieveTransformed(Image $image, Transformation $transformation)
    {
        return Image::fromPath(
            \cloudinary_url($image->getId(), ['transformation' => $transformation->build(), 'secure' => true]),
            $image->getRelativePath()
        );
    }

    public function retrieve(Image $image)
    {
        return $this->retrieveTransformed($image, $this->configuration->getDefaultTransformation());
    }

    public function delete(Image $image)
    {
        Uploader::destroy($image->getIdWithoutExtension());
    }

    public function validateCredentials()
    {
        return $this->credentialValidator->validate($this->configuration->getCredentials());
    }

    private function authorise()
    {
        Cloudinary::config($this->configurationBuilder->build());
        Cloudinary::$USER_PLATFORM = $this->configuration->getUserPlatform();
    }
}
