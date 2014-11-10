<?php

namespace spec\CloudinaryExtension;

use CloudinaryExtension\Credentials;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CloudinaryImageProviderSpec extends ObjectBehavior
{
    function let(Credentials $credentials)
    {
        $this->beConstructedWith($credentials);
    }

    function it_is_an_image_provider()
    {
        $this->shouldBeAnInstanceOf('CloudinaryExtension\ImageProvider');
    }
}
