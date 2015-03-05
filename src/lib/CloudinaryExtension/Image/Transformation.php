<?php

namespace CloudinaryExtension\Image;

use CloudinaryExtension\Image\Transformation\Format;
use CloudinaryExtension\Image\Transformation\Quality;

class Transformation
{
    private $gravity;

    private $dimensions;

    private $crop;

    private $fetchFormat;

    private $quality;

    public function __construct()
    {
        $this->quality = Quality::fromString('80');
        $this->fetchFormat = Format::fromString('auto');
        $this->crop = 'pad';
    }

    public function withGravity(Gravity $gravity)
    {
        $this->gravity = $gravity;
        $this->crop = ((string) $gravity) ? 'crop' : 'pad';

        return $this;
    }

    public function withDimensions(Dimensions $dimensions)
    {
        $this->dimensions = $dimensions;

        return $this;
    }

    public function withFormat(Format $format)
    {
        $this->fetchFormat = $format;

        return $this;
    }

    public function withQuality(Quality $quality)
    {
        $this->quality = $quality;

        return $this;
    }

    public static function builder()
    {
        return new Transformation();
    }

    public function build()
    {
        return array(
            'fetch_format' => (string) $this->fetchFormat,
            'quality' => (string) $this->quality,
            'crop' => (string) $this->crop,
            'gravity' => (string) $this->gravity ?: null,
            'width' => $this->dimensions ? $this->dimensions->getWidth() : null,
            'height' => $this->dimensions ? $this->dimensions->getHeight() : null
        );
    }
}

