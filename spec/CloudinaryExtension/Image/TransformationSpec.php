<?php

namespace spec\CloudinaryExtension\Image;

use CloudinaryExtension\Image\Dimensions;
use CloudinaryExtension\Image\Gravity;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TransformationSpec extends ObjectBehavior
{
    function it_creates_new_transformation_builders()
    {
        self::build()->shouldBeAnInstanceOf('CloudinaryExtension\Image\Transformation');
    }

    function it_builds_with_dimensions()
    {
        $transformation = $this->withDimensions(Dimensions::fromWidthAndHeight(10, 10));

        $this->getDimensions()->shouldBeLike(Dimensions::fromWidthAndHeight(10, 10));
        $transformation->shouldBe($this);
    }

    function it_builds_with_gravity()
    {
        $transformation = $this->withGravity(Gravity::fromString('center'));

        $this->getGravity()->shouldBeLike(Gravity::fromString('center'));
        $transformation->shouldBe($this);
    }
}
