<?php
namespace spec\Cloudinary\Cloudinary\Model\ProductImageFinder;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DeletedImageFilterSpec extends ObjectBehavior
{
    function it_should_return_empty_array_null_images()
    {
        $imageData = [];
        $this->__invoke($imageData)->shouldReturn(false);
    }

    function it_should_return_empty_when_no_removed_images()
    {
        $imageData = ['removed' => 0];
        $this->__invoke($imageData)->shouldReturn(false);
    }

    function it_should_return_images_marked_as_removed()
    {
        $imageData = ['removed' => 1];
        $this->__invoke($imageData)->shouldReturn(true);
    }
}