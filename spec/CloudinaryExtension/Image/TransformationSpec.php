<?php

namespace spec\CloudinaryExtension\Image;

use CloudinaryExtension\Image\Dimensions;
use CloudinaryExtension\Image\Gravity;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TransformationSpec extends ObjectBehavior
{
    function it_has_no_gravity_value_by_default()
    {
        $this->getGravity()->shouldBe(null);
    }

    function it_can_have_dimensions()
    {
        $this->withDimensions(Dimensions::fromWidthAndHeight(10, 10));

        $this->getDimensions()->shouldBeLike(Dimensions::fromWidthAndHeight(10, 10));
    }

    function it_can_have_gravity()
    {
        $this->withGravity(Gravity::fromString('center'));

        $this->getGravity()->shouldBeLike(Gravity::fromString('center'));
    }
}
