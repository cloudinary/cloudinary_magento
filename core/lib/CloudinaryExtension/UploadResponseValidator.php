<?php

namespace CloudinaryExtension;

use CloudinaryExtension\Exception\MigrationError;

class UploadResponseValidator
{
    public function validateResponse($image, $uploadResponse)
    {
        if ($uploadResponse['existing'] == 1) {
            MigrationError::throwWith($image, MigrationError::CODE_FILE_ALREADY_EXISTS);
        }

        return $uploadResponse;
    }
}
