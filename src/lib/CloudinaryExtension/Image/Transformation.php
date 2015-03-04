<?php

namespace CloudinaryExtension\Image;

class Transformation
{
    private $gravity;

    private $dimensions;

    private $crop = 'pad';

    public function withGravity(Gravity $gravity)
    {
        $this->gravity = $gravity;
        $this->crop = $gravity->__toString() ? 'crop' : 'pad';

        return $this;
    }

    public function withDimensions(Dimensions $dimensions)
    {
        $this->dimensions = $dimensions;

        return $this;
    }

    public function getDimensions()
    {
        return $this->dimensions;
    }

    public function getCrop()
    {
        return $this->crop;
    }

    public function getGravity()
    {
        return $this->gravity;
    }

    public static function build()
    {
        return new Transformation();
    }
}
