<?php

namespace spec\CloudinaryExtension;

use CloudinaryExtension\Cloud;
use CloudinaryExtension\Configuration;
use CloudinaryExtension\Credentials;
use CloudinaryExtension\Image\Gravity;
use CloudinaryExtension\Image\Transformation;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin Configuration
 */
class ConfigurationSpec extends ObjectBehavior
{
    function let(Credentials $credentials, Cloud $cloud)
    {
        $this->beConstructedThrough('fromCloudAndCredentials', array($credentials, $cloud));
    }

    function it_has_a_null_gravity_transformation_by_default()
    {
        $transformation = $this->getDefaultTransformation();

        $transformation->shouldBeAnInstanceOf('CloudinaryExtension\Image\Transformation');
        $transformation->getGravity()->shouldBeLike(Gravity::fromString(null));
    }

    function it_exposes_its_default_image_transformation()
    {
        $transformation = new Transformation();

        $this->setDefaultTransformation($transformation->withGravity(Gravity::fromString('g')));

        $transformation = $this->getDefaultTransformation();

        $transformation->shouldBeAnInstanceOf('CloudinaryExtension\Image\Transformation');
        $transformation->getGravity()->shouldBeLike(Gravity::fromString('g'));
    }
}
