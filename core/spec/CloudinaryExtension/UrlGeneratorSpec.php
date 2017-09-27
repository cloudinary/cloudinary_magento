<?php

namespace spec\CloudinaryExtension;

use CloudinaryExtension\Configuration;
use CloudinaryExtension\ConfigurationInterface;
use CloudinaryExtension\Image;
use CloudinaryExtension\Image\ImageFactory;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\ImageInterface;
use CloudinaryExtension\ImageProvider;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UrlGeneratorSpec extends ObjectBehavior
{
    function let(
        ConfigurationInterface $configuration,
        ImageProvider $imageProvider
    )
    {
        $configuration->getFormatsToPreserve()->willReturn(['jpg', 'png']);

        $this->beConstructedWith($configuration, $imageProvider);
    }

    function it_generates_url_from_given_path_and_transformation(
        Image $image,
        ImageProvider $imageProvider
    )
    {
        $transformation = Transformation::builder();
        $image->getExtension()->willReturn('gif');

        $this->generateFor($image, $transformation);

        $imageProvider->retrieveTransformed($image, $transformation)->shouldHaveBeenCalled();
    }

    function it_removes_image_format_if_its_in_list_of_formats_to_preserve(
        Image $image,
        ImageProvider $imageProvider
    )
    {
        $transformation = Transformation::builder();
        $image->getExtension()->willReturn('jpg');

        $this->generateFor($image, $transformation);

        $imageProvider->retrieveTransformed($image, $transformation->withoutFormat())->shouldHaveBeenCalled();
    }

    function it_generates_url_from_given_path_when_no_transformation_given(
        Image $image,
        ImageProvider $imageProvider,
        ConfigurationInterface $configuration
    )
    {
        $transformation = Transformation::builder();
        $configuration->getDefaultTransformation()->willReturn($transformation);

        $image->getExtension()->willReturn('gif');

        $this->generateFor($image);

        $imageProvider->retrieveTransformed($image, $transformation)->shouldHaveBeenCalled();
    }

    function it_does_not_modify_the_transformation_passed(Image $image)
    {
        $transformation = Transformation::builder();
        $image->getExtension()->willReturn('jpg');

        $this->generateFor($image, $transformation);

        expect($transformation)->toBeLike(Transformation::builder());
    }
}
