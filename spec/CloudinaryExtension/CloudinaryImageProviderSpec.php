<?php

namespace spec\CloudinaryExtension;

require_once ('src/lib/Cloudinary/src/Cloudinary.php');

use CloudinaryExtension\Cloud;
use CloudinaryExtension\Credentials;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CloudinaryImageProviderSpec extends ObjectBehavior
{
    function let(Credentials $credentials, Cloud $cloud)
    {
        $cloud->__toString()->willReturn('');

        $this->beConstructedWith($credentials, $cloud);
    }

    function it_is_an_image_provider()
    {
        $this->shouldBeAnInstanceOf('CloudinaryExtension\ImageProvider');
    }
}
