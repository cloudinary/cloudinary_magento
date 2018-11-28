<?php

namespace CloudinaryExtension\Security;

interface EnvironmentVariable
{
    public function getCloud();
    public function getCredentials();
}