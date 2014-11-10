<?php

namespace spec\CloudinaryExtension;

use CloudinaryExtension\Credentials;
use CloudinaryExtension\Image;
use CloudinaryExtension\ImageProvider;
use CloudinaryExtension\Configuration;
use CloudinaryExtension\Security\Key;
use CloudinaryExtension\Security\Secret;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ImageManagerSpec extends ObjectBehavior
{
    const IMAGE_PATH = 'image_to_upload.png';
    const IMAGE_PROVIDER_URL = "http://image.url.on.provider";

    function it_uploads_an_image(ImageProvider $imageProvider, Configuration $configuration)
    {
        $image = Image::fromPath(self::IMAGE_PATH);

        $imageProvider->upload($image, Argument::any())->shouldBeCalled();
        $imageProvider->getImageUrlByName(self::IMAGE_PATH)->willReturn(self::IMAGE_PROVIDER_URL);

        $this->beConstructedWith($imageProvider, $configuration);

        $this->uploadImage(self::IMAGE_PATH, 'some key', 'some secret');
        $this->getUrlForImage(self::IMAGE_PATH)->shouldReturn(self::IMAGE_PROVIDER_URL);
    }
}
