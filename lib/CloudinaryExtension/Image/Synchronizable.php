<?php

namespace CloudinaryExtension\Image;

interface Synchronizable
{
    /**
     * @return string
     */
    public function getFilename();

    /**
     * @return string
     */
    public function getRelativePath();

    /**
     * @return void
     */
    public function tagAsSynchronized();
}
