<?php
namespace CloudinaryExtension\Image;

use CloudinaryExtension\ImageInterface;

class LocalImage implements ImageInterface
{
    /**
     * @var callable
     */
    private $localPathGenerator;

    /**
     * LocalImage constructor.
     */
    public function __construct($localPathGenerator)
    {
        $this->localPathGenerator = $localPathGenerator;
    }

    public function __toString()
    {
        return call_user_func($this->localPathGenerator);
    }
}
