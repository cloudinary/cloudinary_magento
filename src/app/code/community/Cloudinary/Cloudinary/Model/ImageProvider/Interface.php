<?php

interface Cloudinary_Cloudinary_Model_ImageProvider_Interface
{
    public function upload($key, $secret);
    public function wasUploadSuccessful();
}