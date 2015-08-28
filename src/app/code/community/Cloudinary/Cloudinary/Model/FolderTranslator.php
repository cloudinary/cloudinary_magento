<?php

/**
 * Interface FolderTranslator
 *
 * Supposed to contain the logic of which folder should a file be uploaded in cloudinary.
 *
 * @package CloudinaryExtension\Migration
 */
interface Cloudinary_Cloudinary_Model_FolderTranslator
{
    public function translate($folder);
    public function reverse($folder);
}
