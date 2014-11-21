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

    function it_exposes_its_dimensions()
    {
        $dimensions = new Dimension(10, 10);

        $this->setDimensions($dimensions)->shouldReturn($this);
        $this->getDimensions()->shouldBe($dimensions);
    }
}
