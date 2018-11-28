<?php

namespace CloudinaryExtension;

use CloudinaryExtension\Exception\FileExists;

class UploadResponseValidator
{
    public function validateResponse($image, $uploadResponse)
    {
        if ($uploadResponse['existing'] == 1) {
            FileExists::throwWith($image);
        }

        return $uploadResponse;
    }
}
