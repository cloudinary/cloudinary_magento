<?php

namespace CloudinaryExtension;

use CloudinaryExtension\Image\Transformation;

interface ImageProvider
{
    public function upload(Image $image);
    public function transformImage(Image $image, Transformation $transformation);
    public function deleteImage(Image $image);
}