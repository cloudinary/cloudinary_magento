<?php

namespace CloudinaryExtension\Exception;

use CloudinaryExtension\Image;
use Exception;

/**
 * Class MigrationError
 * @package CloudinaryExtension\Exception
 */
class MigrationError extends Exception
{
    const CODE_FILE_ALREADY_EXISTS = 0;
    const CODE_API_ERROR = 1;

    private static $messages = [
        self::CODE_FILE_ALREADY_EXISTS => 'File already exists (cloudinary is case insensitive!!).',
        self::CODE_API_ERROR => 'Internal API error'
    ];

    private $image;

    /**
     * @return Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param Image $image
     * @param $code
     * @param $message overrides the default message attached to the code
     * @return MigrationError
     */
    private static function build(Image $image, $code, $message = '')
    {
        $result = new MigrationError($message ?: self::$messages[$code], $code);
        $result->image = $image;
        return $result;
    }

    public static function throwWith(Image $image, $code, $message = '')
    {
        throw MigrationError::build($image, $code, $message);
    }
}
