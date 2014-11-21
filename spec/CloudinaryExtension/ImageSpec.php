<?php

namespace spec\CloudinaryExtension;

use CloudinaryExtension\Image\Dimension;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ImageSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('fromPath', ['image_path.gif']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('CloudinaryExtension\Image');
    }

    function it_can_be_constructed_with_specific_dimensions()
    {
        $dimensions = new Dimension(10, 10);

        $this->beConstructedThrough('fromPathAndDimensions', ['image_path.gif', $dimensions]);

        $this->getDimensions()->shouldBe($dimensions);
    }
}
