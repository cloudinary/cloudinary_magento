<?php

namespace CloudinaryExtension;

interface ImageProvider
{
    public function upload(Image $image);
    public function getImageUrlByName($imageName, $options = array());
}