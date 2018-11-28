<?php

namespace CloudinaryExtension\Image;

interface SynchronizationChecker
{
    /**
     * @return boolean
     */
    public function isSynchronized($imageName);
}
