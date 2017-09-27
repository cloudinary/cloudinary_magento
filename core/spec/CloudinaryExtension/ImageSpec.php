<?php

namespace spec\CloudinaryExtension;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ImageSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('fromPath', ['image_path.gif']);
    }

    function it_provides_a_public_id_from_path()
    {
        $this->getId()->shouldBe('image_path');
    }

    function it_provides_the_extension_from_path()
    {
        $this->getExtension()->shouldBe('gif');
    }
}
