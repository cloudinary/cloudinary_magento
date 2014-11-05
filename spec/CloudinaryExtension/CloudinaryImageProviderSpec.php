<?php

namespace spec\CloudinaryExtension;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CloudinaryImageProviderSpec extends ObjectBehavior
{
    function it_is_an_image_provider()
    {
        $this->shouldBeAnInstanceOf('CloudinaryExtension\ImageProvider');
    }
}
