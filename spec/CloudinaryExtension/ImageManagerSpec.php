<?php

namespace spec\CloudinaryExtension;

use CloudinaryExtension\Image;
use CloudinaryExtension\Image\Dimensions;
use CloudinaryExtension\ImageProvider;
use CloudinaryExtension\Configuration;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ImageManagerSpec extends ObjectBehavior
{
    const IMAGE_PATH = 'example_image.png';
    const IMAGE_PROVIDER_URL = "http://image.url.on.provider";

    function let(ImageProvider $imageProvider)
    {
        $this->beConstructedWith($imageProvider);
    }

    function it_uploads_an_image(ImageProvider $imageProvider)
    {
        $image = Image::fromPath(self::IMAGE_PATH);

        $imageProvider->upload($image, Argument::any())->shouldBeCalled();
        $imageProvider->getImageUrlByName(self::IMAGE_PATH)->willReturn(self::IMAGE_PROVIDER_URL);

        $this->uploadImage(self::IMAGE_PATH, 'some key', 'some secret');

        $this->getUrlForImage($image)->shouldReturn(self::IMAGE_PROVIDER_URL);
    }

    function it_builds_an_image_url_given_specific_dimensions(ImageProvider $imageProvider)
    {
        $image = Image::fromPath(self::IMAGE_PATH);
        $transformation = new Image\Transformation();
        $transformation->withDimensions(Dimensions::fromWidthAndHeight(10, 10));

        $imageProvider->transformImage($image, $transformation)->willReturn(self::IMAGE_PROVIDER_URL);

        $this->getUrlForImageWithTransformation($image, $transformation)->shouldReturn(self::IMAGE_PROVIDER_URL);
    }

    function it_deletes_an_image(ImageProvider $imageProvider)
    {
        $image = Image::fromPath(self::IMAGE_PATH);
        $imageProvider->deleteImage($image)->shouldBeCalled();

        $this->deleteImage($image);
    }
}
