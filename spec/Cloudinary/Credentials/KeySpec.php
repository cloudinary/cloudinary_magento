<?php

namespace spec\Cloudinary\Credentials;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class KeySpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('fromString', ['secret_key']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Cloudinary\Credentials\Key');
    }
}
