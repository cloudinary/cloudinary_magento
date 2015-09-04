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
    private $messages = [self::CODE_FILE_ALREADY_EXISTS => 'File already exists (cloudinary is case insensitive!!)'];
    private $image;

    public function getMessageText()
    {
        return $this->messages[$this->getCode()];
    }

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
     * @return MigrationError
     */
    private static function build(Image $image, $code)
    {
        $result = new MigrationError('', $code);
        $result->image = $image;
        $result->message = $result->getMessageText();
        return $result;
    }

    public static function throwWith(Image $image, $code)
    {
        throw self::build($image, $code);
    }

}
