<?php


namespace CloudinaryExtension;

use Cloudinary;
use Cloudinary\Uploader;
use CloudinaryExtension\Exception\FileAlreadyExists;
use CloudinaryExtension\Exception\MigrationError;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\Security;
use CloudinaryExtension\Image\Transformation\Format;
use CloudinaryExtension\Image\Transformation\FetchFormat;

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
        $this->authorise();
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
        try {
            $uploadResult = Uploader::upload(
                (string)$image,
                $this->configuration->getUploadConfig()->toArray() + [ "folder" => $image->getRelativeFolder()]
            );

            return $this->uploadResponseValidator->validateResponse($image, $uploadResult);

        } catch (\Exception $e) {
            MigrationError::throwWith($image, MigrationError::CODE_API_ERROR, $e->getMessage());
        }
    }

    public function retrieveTransformed(Image $image, Transformation $transformation)
    {
        return Image::fromPath(
            \cloudinary_url($image->getId(), $transformation->build() + ["secure" => true]),
            $image->getRelativePath()
        );
    }

    public function retrieve(Image $image)
    {
        return $this->retrieveTransformed($image, $this->configuration->getDefaultTransformation());
    }

    public function delete(Image $image)
    {
        Uploader::destroy($image->getId());
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