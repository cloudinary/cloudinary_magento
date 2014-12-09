<?php

namespace CloudinaryExtension\Image;

interface Synchronizable
{
    public function getFilename();

    public function tagAsSynchronized();
} 