<?php

namespace Cloudinary;

interface ImageProvider
{
    public function upload(Image $anImage, Credentials $credentials);
    public function wasUploadSuccessful();
}