<?php

namespace CloudinaryExtension\AutoUploadMapping;

interface Configuration
{
    const ACTIVE = true;
    const INACTIVE = false;

    /**
     * @return bool
     */
    public function isActive();

    /**
     * @param bool $state
     */
    public function setState($state);

    /**
     * @return bool
     */
    public function getRequestState();

    /**
     * @param bool $state
     */
    public function setRequestState($state);
}
