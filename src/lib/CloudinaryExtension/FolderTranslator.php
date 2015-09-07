<?php

namespace CloudinaryExtension;

/**
 * Interface FolderTranslator
 *
 * Supposed to contain the logic of which folder should a file be uploaded in cloudinary.
 *
 * @package CloudinaryExtension\Migration
 */
interface FolderTranslator
{
    public function translate($path);
    public function reverse($folder);
}
