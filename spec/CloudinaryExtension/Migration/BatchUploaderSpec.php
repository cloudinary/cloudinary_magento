<?php

namespace spec\CloudinaryExtension\Migration;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BatchUploaderSpec extends ObjectBehavior
{
    function it_uploads_and_synchronizes_collection_of_images()
    {
        $images = array('images1', 'image')
        $this->uploadImages()
    }
}
