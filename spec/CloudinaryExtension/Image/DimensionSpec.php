<?php

namespace spec\CloudinaryExtension\Image;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DimensionSpec extends ObjectBehavior
{
    function it_exposes_its_width_and_height()
    {
        $this->beConstructedWith(100, 150);

        $this->getWidth()->shouldBe(100);
        $this->getHeight()->shouldBe(150);
    }
}
