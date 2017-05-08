<?php
namespace spec\Cloudinary\Cloudinary\Model\ProductImageFinder;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class NewImageFilterSpec extends ObjectBehavior
{
    function it_should_return_empy_for_no_new_images()
    {
        $imageData = [];
        $this->__invoke($imageData)->shouldReturn(false);
    }

    function it_should_return_images_marked_as_new()
    {
        $imageData = ['new_file' => 1];
        $this->__invoke($imageData)->shouldReturn(true);
    }
}