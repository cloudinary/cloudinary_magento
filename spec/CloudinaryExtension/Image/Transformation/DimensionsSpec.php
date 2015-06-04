<?php

namespace spec\CloudinaryExtension\Image\Transformation;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DimensionsSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('fromWidthAndHeight', [100, 150]);
    }

    function it_exposes_its_width_and_height()
    {
        $this->getWidth()->shouldBe(100);
        $this->getHeight()->shouldBe(150);
    }

    function it_rounds_float_values()
    {
        $this->beConstructedThrough('fromWidthAndHeight', [1.5, 2.9]);

        $this->getWidth()->shouldBe(2);
        $this->getHeight()->shouldBe(3);
    }
}
