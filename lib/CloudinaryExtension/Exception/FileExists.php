<?php

namespace CloudinaryExtension\Exception;

class FileExists extends MigrationError
{
    const DEFAULT_MESSAGE = 'File already exists (cloudinary is case insensitive!!).';
}
