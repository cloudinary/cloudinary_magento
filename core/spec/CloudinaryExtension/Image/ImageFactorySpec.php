<?php

namespace spec\CloudinaryExtension\Image;

use CloudinaryExtension\ConfigurationInterface;
use CloudinaryExtension\Image\SynchronizationChecker;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ImageFactorySpec extends ObjectBehavior
{
    function let(
        ConfigurationInterface $configuration,
        SynchronizationChecker $synchronizable
    )
    {
        $this->beConstructedWith(
            $configuration,
            $synchronizable
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('CloudinaryExtension\Image\ImageFactory');
    }
}
