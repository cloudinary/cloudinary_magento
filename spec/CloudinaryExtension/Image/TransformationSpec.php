<?php

namespace spec\CloudinaryExtension\Image;

use CloudinaryExtension\Image\Dimensions;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TransformationSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('toDimensions', [Dimensions::fromWidthAndHeight(10, 10)]);
    }

    function it_should_be_a_transformation()
    {
        $this->shouldHaveType('CloudinaryExtension\Image\Transformation');
    }

    function it_has_dimensions()
    {
        $this->getDimensions()->shouldBeLike(Dimensions::fromWidthAndHeight(10, 10));
    }
}
