<?php

namespace spec\Cloudinary;

use Cloudinary\Image;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ImageManagerSpec extends ObjectBehavior
{
    function it_uploads_an_image(Image $anImage)
    {
        $this->uploadImage($anImage)->shouldHaveType('Cloudinary\OnlineImage');
    }
}
