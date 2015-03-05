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

    function it_has_a_default_transformation()
    {
        $transformation = $this->getDefaultTransformation();

        $transformation->shouldBeAnInstanceOf('CloudinaryExtension\Image\Transformation');
    }
}
