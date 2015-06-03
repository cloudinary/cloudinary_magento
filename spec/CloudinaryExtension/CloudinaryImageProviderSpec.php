<?php

namespace spec\CloudinaryExtension;

use CloudinaryExtension\Configuration;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CloudinaryImageProviderSpec extends ObjectBehavior
{
    function let(Configuration $configuration)
    {
        $configuration->build()->shouldBeCalled();
        $this->beConstructedThrough('fromConfiguration', [$configuration]);
    }

    function it_is_an_image_provider()
    {
        $this->shouldBeAnInstanceOf('CloudinaryExtension\ImageProvider');
    }
}
