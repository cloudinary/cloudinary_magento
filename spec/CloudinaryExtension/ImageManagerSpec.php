<?php

namespace spec\CloudinaryExtension;

use CloudinaryExtension\Credentials;
use CloudinaryExtension\Image;
use CloudinaryExtension\Image\Dimensions;
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

    function let(ImageProvider $imageProvider)
    {
        $this->beConstructedWith($imageProvider);
    }

    function it_uploads_an_image(ImageProvider $imageProvider)
    {
        $image = Image::fromPath(self::IMAGE_PATH);

        $imageProvider->upload($image, Argument::any())->shouldBeCalled();
        $imageProvider->getImageUrlByName(self::IMAGE_PATH, array())->willReturn(self::IMAGE_PROVIDER_URL);

        $this->uploadImage(self::IMAGE_PATH, 'some key', 'some secret');

        $this->getUrlForImage($image)->shouldReturn(self::IMAGE_PROVIDER_URL);
    }

    function it_builds_an_image_url_given_specific_dimensions(ImageProvider $imageProvider)
    {
        $image = Image::fromPathAndDimensions(self::IMAGE_PATH, Dimensions::fromWithAndHeight(10, 10));

        $imageProvider->getImageUrlByName(Argument::cetera())->willReturn(self::IMAGE_PROVIDER_URL);

        $this->getUrlForImage($image)->shouldReturn(self::IMAGE_PROVIDER_URL);

        $imageProvider->getImageUrlByName($image, array(
            'width' => 10,
            'height' => 10,
            'crop' => 'pad'
        ))->shouldHaveBeenCalled();

    }
}
