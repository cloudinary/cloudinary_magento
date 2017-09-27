<?php


namespace CloudinaryExtension;

use Cloudinary;
use Cloudinary\Uploader;
use CloudinaryExtension\Exception\FileAlreadyExists;
use CloudinaryExtension\Exception\MigrationError;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\Security;

class CloudinaryImageProvider implements ImageProvider
{
    private $configuration;

    private $uploadConfig = array(
        "use_filename" => true,
        "unique_filename" => false,
        "overwrite" => false
    );

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
        try{
            $imagePath = (string)$image;
            $uploadOptionsAndFolder = $this->uploadConfig + ["folder" => $image->getRelativeFolder()];
            $uploadResult = Uploader::upload($imagePath, $uploadOptionsAndFolder);

            if ($uploadResult['existing'] == 1) {
                MigrationError::throwWith($image, MigrationError::CODE_FILE_ALREADY_EXISTS);
            }
            return $uploadResult;
        } catch (\Exception $e) {
            MigrationError::throwWith($image, MigrationError::CODE_API_ERROR, $e->getMessage());
        }
    }

    public function transformImage(Image $image, Transformation $transformation = null)
    {
        if ($transformation === null) {
            $transformation = $this->configuration->getDefaultTransformation();
        }
        return Image::fromPath(\cloudinary_url($image->getId(), $transformation->build()), $image->getRelativePath());
    }

    public function validateCredentials()
    {
        $signedValidationUrl = $this->getSignedValidationUrl();
        return $this->validationResult($signedValidationUrl);
    }

    public function deleteImage(Image $image)
    {
        Uploader::destroy($image->getId());
    }

    private function authorise()
    {
        Cloudinary::config($this->configuration->build());
        Cloudinary::$USER_PLATFORM = $this->configuration->getUserPlatform();
    }

    private function getSignedValidationUrl()
    {
        $consoleUrl = Security\ConsoleUrl::fromPath("media_library/cms");
        return (string)Security\SignedConsoleUrl::fromConsoleUrlAndCredentials(
            $consoleUrl,
            $this->configuration->getCredentials()
        );
    }

    private function validationResult($signedValidationUrl)
    {
        $request = new ValidateRemoteUrlRequest($signedValidationUrl);
        return $request->validate();
    }
}
