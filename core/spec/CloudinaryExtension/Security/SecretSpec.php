<?php

namespace spec\CloudinaryExtension\Security;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SecretSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('fromString', ['secret_secret']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('CloudinaryExtension\Security\Secret');
    }
}
