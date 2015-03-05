<?php

namespace spec\CloudinaryExtension\Image;

use CloudinaryExtension\Image\Dimensions;
use CloudinaryExtension\Image\Gravity;
use CloudinaryExtension\Image\Transformation\Format;
use CloudinaryExtension\Image\Transformation\Quality;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TransformationSpec extends ObjectBehavior
{
    function it_creates_new_transformation_builders()
    {
        self::builder()->shouldBeAnInstanceOf('CloudinaryExtension\Image\Transformation');
    }

    function it_builds_with_fetch_format_auto_by_default()
    {
        $transformationArray = self::builder()->build();

        $transformationArray->offsetGet('fetch_format')->shouldBe('auto');
    }

    function it_overrides_fetch_format_if_provided()
    {
        $transformationArray = self::builder()
            ->withFormat(Format::fromString(''))
            ->build();

        $transformationArray->offsetGet('fetch_format')->shouldBe('');
    }

    function it_builds_with_default_quality()
    {
        $transformationArray = self::builder()->build();

        $transformationArray->offsetGet('quality')->shouldBe('80');
    }

    function it_overrides_quality_if_provided()
    {
        $transformationArray = self::builder()
            ->withQuality(Quality::fromString('100'))
            ->build();

        $transformationArray->offsetGet('quality')->shouldBe('100');
    }

    function it_builds_no_dimensions_by_default()
    {
        $transformationArray = self::builder()->build();

        $transformationArray->offsetGet('width')->shouldBe(null);
        $transformationArray->offsetGet('height')->shouldBe(null);
    }

    function it_builds_with_dimensions_when_provided()
    {
        $transformationArray = self::builder()
            ->withDimensions(Dimensions::fromWidthAndHeight('80', '90'))
            ->build();

        $transformationArray->offsetGet('width')->shouldBe('80');
        $transformationArray->offsetGet('height')->shouldBe('90');
    }

    function it_builds_with_no_gravity_by_default()
    {
        $transformationArray = self::builder()->build();

        $transformationArray->offsetGet('gravity')->shouldBe(null);
    }

    function it_builds_with_gravity()
    {
        $transformationArray = self::builder()
            ->withGravity(Gravity::fromString('center'))
            ->build();

        $transformationArray->offsetGet('gravity')->shouldBe('center');
    }

    function it_builds_with_crop_set_to_crop_when_gravity_is_set()
    {
        $transformationArray = self::builder()
            ->withGravity(Gravity::fromString('center'))
            ->build();

        $transformationArray->offsetGet('crop')->shouldBe('crop');
    }


    function it_builds_with_crop_set_to_pad_when_gravity_is_not_set()
    {
        $transformationArray = self::builder()->build();

        $transformationArray->offsetGet('crop')->shouldBe('pad');
    }
}
