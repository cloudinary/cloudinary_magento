<?php

namespace CloudinaryExtension;

interface ImageProvider
{
    public function upload(Image $image, Credentials $credentials);
    public function getImageUrlByName($imageName);
}