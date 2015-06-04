<?php

namespace spec\CloudinaryExtension;

use Cloudinary;
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

    function it_sets_user_agent_string(Configuration $configuration)
    {
        $configuration->getUserPlatform()->willReturn('Test User Agent String');

        $this->getWrappedObject();
        expect(Cloudinary::$USER_PLATFORM)->toBe('Test User Agent String');
    }
}
