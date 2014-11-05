<?php

namespace spec\Cloudinary;

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
        $this->shouldHaveType('Cloudinary\Image');
    }
}
