<?php

namespace CloudinaryExtension\Image;

class Transformation
{
    private $dimensions;

    private $crop = 'pad';

    private function __construct(Dimensions $dimensions)
    {
        $this->dimensions = $dimensions;
    }

    public static function toDimensions(Dimensions $dimensions)
    {
        return new Transformation($dimensions);
    }

    public function getDimensions()
    {
        return $this->dimensions;
    }

    public function getCrop()
    {
        return $this->crop;
    }

}
