<?php

namespace spec\CloudinaryExtension\Security;

use CloudinaryExtension\Cloud;
use CloudinaryExtension\Credentials;
use CloudinaryExtension\Security\Key;
use CloudinaryExtension\Security\Secret;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CloudinaryEnvironmentVariableSpec extends ObjectBehavior
{

    function let()
    {
        $this->beConstructedThrough('fromString', array('CLOUDINARY_URL=cloudinary://aKey:aSecret@aCloud'));
    }

    function it_should_extract_the_cloud_name_from_the_environment_variable()
    {
        $this->getCloud()->shouldBeLike(Cloud::fromName('aCloud'));
    }

    function it_should_extract_the_credentials_from_the_environment_variable()
    {
        $credentials = new Credentials(Key::fromString('aKey'), Secret::fromString('aSecret'));
        $this->getCredentials()->shouldBeLike($credentials);
    }

}
