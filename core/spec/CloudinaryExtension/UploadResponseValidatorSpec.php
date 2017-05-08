<?php

namespace spec\CloudinaryExtension;

use CloudinaryExtension\Image;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UploadResponseValidatorSpec extends ObjectBehavior
{
    function it_should_return_upload_response_if_the_image_is_new(Image $image)
    {
        $response = ['existing' => 0, 'test' => 'data'];

        $this->validateResponse($image, $response)->shouldReturn($response);
    }

    function it_should_return_throw_exception_if_the_image_already_exists(Image $image)
    {
        $response = ['existing' => 1, 'test' => 'data'];

        $this->shouldThrow('CloudinaryExtension\Exception\MigrationError')
            ->duringValidateResponse($image, $response);
    }
}
