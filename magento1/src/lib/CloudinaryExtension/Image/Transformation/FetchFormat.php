<?php

namespace CloudinaryExtension\Image\Transformation;

class FetchFormat
{
    const FETCH_FORMAT_AUTO = 'auto';

    private $value;

    private function __construct($value)
    {
        $this->value = $value;
    }

    public static function fromString($value)
    {
        return new FetchFormat($value);
    }

    public function __toString()
    {
        return $this->value;
    }
}
