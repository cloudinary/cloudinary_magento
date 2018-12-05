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
    const DEFAULT_MESSAGE = 'Unknown error';

    /**
     * @var Image
     */
    private $image;

    /**
     * @param string $suffix
     */
    public function suffixMessage($suffix)
    {
        $this->message = sprintf('%s%s', $this->message, $suffix);
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
     * @param string $message
     * @throws MigrationError
     */
    public static function throwWith(Image $image, $message = '')
    {
        $exception = new static($message ?: static::DEFAULT_MESSAGE);
        $exception->image = $image;
        throw $exception;
    }
}
