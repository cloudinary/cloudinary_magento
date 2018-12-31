<?php


namespace CloudinaryExtension;

use Cloudinary;
use Cloudinary\Uploader;
use CloudinaryExtension\Exception\ApiError;
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

    public function __construct(
        ConfigurationInterface $configuration,
        ConfigurationBuilder $configurationBuilder,
        UploadResponseValidator $uploadResponseValidator
    ) {
        $this->configuration = $configuration;
        $this->uploadResponseValidator = $uploadResponseValidator;
        $this->configurationBuilder = $configurationBuilder;
        $this->authorise();
    }

    public static function fromConfiguration(ConfigurationInterface $configuration)
    {
        return new CloudinaryImageProvider(
            $configuration,
            new ConfigurationBuilder($configuration),
            new UploadResponseValidator()
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
        $imagePath = \cloudinary_url($image->getId(), [
            'transformation' => $transformation->build(),
            'secure' => true,
            'sign_url' => $this->configuration->getUseSignedUrls()
        ]);

        if ($this->configuration->getUseRootPath()) {
            if (strpos($imagePath, "cloudinary.com/{$this->configuration->getCloud()}/image/upload/") !== false) {
                $imagePath = str_replace("cloudinary.com/{$this->configuration->getCloud()}/image/upload/", "cloudinary.com/{$this->configuration->getCloud()}/", $imagePath);
            } elseif (strpos($imagePath, "cloudinary.com/image/upload/") !== false) {
                $imagePath = str_replace("cloudinary.com/image/upload/", "cloudinary.com/", $imagePath);
            }
        }
        if ($this->configuration->getRemoveVersionNumber()) {
            $regex = '/\/v[0-9]+\/' . preg_quote(ltrim($image->getId(), '/'), '/') . '$/';
            $imagePath = preg_replace($regex, '/' . ltrim($image->getId(), '/'), $imagePath);
        }

        return Image::fromPath($imagePath, $image->getRelativePath());
    }

    public function retrieve(Image $image)
    {
        return $this->retrieveTransformed($image, $this->configuration->getDefaultTransformation());
    }

    public function delete(Image $image)
    {
        Uploader::destroy($image->getIdWithoutExtension());
    }

    /**
     * @return bool
     */
    public function validateCredentials()
    {
        try {
            $pingValidation = $this->api->ping();
            if (!(isset($pingValidation["status"]) && $pingValidation["status"] === "ok")) {
                return false;
                //throw new ValidatorException(__(self::CREDENTIALS_CHECK_UNSURE));
            }
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    private function authorise()
    {
        Cloudinary::config($this->configurationBuilder->build());
        Cloudinary::$USER_PLATFORM = $this->configuration->getUserPlatform();
    }
}
